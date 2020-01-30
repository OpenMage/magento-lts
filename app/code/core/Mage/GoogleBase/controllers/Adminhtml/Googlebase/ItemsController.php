<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleBase Admin Items Controller
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @name       Mage_GoogleBase_Adminhtml_Googlebase_ItemsController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_GoogleBase_Adminhtml_Googlebase_ItemsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/googlebase/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Google Base'), Mage::helper('adminhtml')->__('Google Base'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Google base'))
             ->_title($this->__('Manage Items'));

        if (0 === (int)$this->getRequest()->getParam('store')) {
            $this->_redirect('*/*/', array('store' => Mage::app()->getAnyStoreView()->getId(), '_current' => true));
            return;
        }
        $contentBlock = $this->getLayout()->createBlock('googlebase/adminhtml_items')->setStore($this->_getStore());

        if ($this->getRequest()->getParam('captcha_token') && $this->getRequest()->getParam('captcha_url')) {
            $contentBlock->setGbaseCaptchaToken(
                Mage::helper('core')->urlDecodeAndEscape($this->getRequest()->getParam('captcha_token'))
            );
            $contentBlock->setGbaseCaptchaUrl(
                Mage::helper('core')->urlDecodeAndEscape($this->getRequest()->getParam('captcha_url'))
            );
        }

        if (!$this->_getConfig()->isValidBaseCurrencyCode($this->_getStore()->getId())) {
            $_countryInfo = $this->_getConfig()->getTargetCountryInfo($this->_getStore()->getId());
            $this->_getSession()->addNotice(
                $this->__("Base Currency should be set to %s for %s in system configuration. Otherwise item prices won't be correct in Google Base.",$_countryInfo['currency_name'],$_countryInfo['name'])
            );
        }

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('googlebase')->__('Items'), Mage::helper('googlebase')->__('Items'))
            ->_addContent($contentBlock)
            ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        return $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('googlebase/adminhtml_items_item')
                ->setIndex($this->getRequest()->getParam('index'))
                ->toHtml()
           );
    }

    public function massAddAction()
    {
        $storeId = $this->_getStore()->getId();
        $productIds = $this->getRequest()->getParam('product', null);

        $totalAdded = 0;

        try {
            if (is_array($productIds)) {
                foreach ($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')
                        ->setStoreId($storeId)
                        ->load($productId);

                    if ($product->getId()) {
                        Mage::getModel('googlebase/item')
                            ->setProduct($product)
                            ->insertItem()
                            ->save();

                        $totalAdded++;
                    }
                }
            }

            if ($totalAdded > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d product(s) have been added to Google Base.', $totalAdded)
                );
            } elseif (is_null($productIds)) {
                $this->_getSession()->addError($this->__('Session expired during export. Please revise exported products and repeat the process if necessary.'));
            } else {
                $this->_getSession()->addError($this->__('No products were added to Google Base'));
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massDeleteAction()
    {
        $storeId = $this->_getStore()->getId();
        $itemIds = $this->getRequest()->getParam('item');

        $totalDeleted = 0;

        try {
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->deleteItem();
                    $item->delete();
                    $totalDeleted++;
                }
            }
            if ($totalDeleted > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) have been removed from Google Base.', $totalDeleted)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were deleted from Google Base'));
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massPublishAction()
    {
        $storeId = $this->_getStore()->getId();
        $itemIds = $this->getRequest()->getParam('item');

        $totalPublished = 0;

        try {
            if (!empty($itemIds) && is_array($itemIds)) {
                foreach ($itemIds as $itemId) {
                    $item = Mage::getModel('googlebase/item')->load($itemId);
                    if ($item->getId()) {
                        $item->activateItem();
                        $totalPublished++;
                    }
                }
            }
            if ($totalPublished > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) have been published.', $totalPublished)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were published'));
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function massHideAction()
    {
        $storeId = $this->_getStore()->getId();
        $itemIds = $this->getRequest()->getParam('item');

        $totalHidden = 0;

        try {
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->hideItem();
                    $totalHidden++;
                }
            }
            if ($totalHidden > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) have been saved as inactive items.', $totalHidden)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were saved as inactive items'));
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    /**
     *  Update items statistics and remove the items which are not available in Google Base
     */
    public function refreshAction()
    {
        $storeId = $this->_getStore()->getId();
        $totalUpdated = 0;
        $totalDeleted = 0;

        try {
            $itemIds = $this->getRequest()->getParam('item');
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);

                $stats = Mage::getSingleton('googlebase/service_feed')->getItemStats($item->getGbaseItemId(), $storeId);
                if ($stats === null) {
                    $item->delete();
                    $totalDeleted++;
                    continue;
                }

                if ($stats['draft'] != $item->getIsHidden()) {
                    $item->setIsHidden($stats['draft']);
                }

                if (isset($stats['clicks'])) {
                    $item->setClicks($stats['clicks']);
                }

                if (isset($stats['impressions'])) {
                    $item->setImpr($stats['impressions']);
                }

                if (isset($stats['expires'])) {
                    $item->setExpires($stats['expires']);
                }

                $item->save();
                $totalUpdated++;
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d items(s) have been deleted; total of %d items(s) have been updated.', $totalDeleted, $totalUpdated)
            );

        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    public function confirmCaptchaAction()
    {
        $storeId = $this->_getStore()->getId();
        try {
            Mage::getModel('googlebase/service')->getClient(
                $storeId,
                Mage::helper('core')->urlDecode($this->getRequest()->getParam('captcha_token')),
                $this->getRequest()->getParam('user_confirm')
            );
            $this->_getSession()->addSuccess($this->__('Captcha has been confirmed.'));

        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_getSession()->addError($this->__('Captcha confirmation error: %s', $e->getMessage()));
            $this->_redirectToCaptcha($e);
            return;
        } catch (Zend_Gdata_App_Exception $e) {
            $this->_getSession()->addError( $this->_parseGdataExceptionMessage($e->getMessage()) );
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Captcha confirmation error: %s', $e->getMessage()));
        }

        $this->_redirect('*/*/index', array('store'=>$storeId));
    }

    /**
     * Redirect user to Google Captcha challenge
     *
     * @param Zend_Gdata_App_CaptchaRequiredException $e
     */
    protected function _redirectToCaptcha($e)
    {
        $this->_redirect('*/*/index',
            array('store' => $this->_getStore()->getId(),
                'captcha_token' => Mage::helper('core')->urlEncode($e->getCaptchaToken()),
                'captcha_url' => Mage::helper('core')->urlEncode($e->getCaptchaUrl())
            )
        );
    }

    /**
     * Get store object, basing on request
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Exception
     */
    public function _getStore()
    {
        $store = Mage::app()->getStore((int)$this->getRequest()->getParam('store', 0));
        if ((!$store) || 0 == $store->getId()) {
            Mage::throwException($this->__('Unable to select a Store View.'));
        }
        return $store;
    }

    protected function _getConfig()
    {
        return Mage::getSingleton('googlebase/config');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/googlebase/items');
    }

    /**
     * Parse Exception Response Body
     *
     * @param string $message Exception message to parse
     * @return string
     */
    protected function _parseGdataExceptionMessage($message)
    {
        $result = array();
        foreach (explode("\n", $message) as $row) {
            if (strip_tags($row) == $row) {
                $result[] = $row;
                continue;
            }

            // parse not well-formatted xml
            preg_match_all('/(reason|field|type)=\"([^\"]+)\"/', $row, $matches);

            if (is_array($matches) && count($matches) == 3) {
                if (is_array($matches[1]) && count($matches[1]) > 0) {
                    $c = count($matches[1]);
                    for ($i = 0; $i < $c; $i++) {
                        if (isset($matches[2][$i])) {
                            $result[] = ucfirst($matches[1][$i]) . ': ' . $matches[2][$i];
                        }
                    }
                }
            }
        }
        return implode(". ", $result);
    }
}
