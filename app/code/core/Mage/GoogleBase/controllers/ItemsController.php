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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleBase Admin Items Controller
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @name       Mage_GoogleBase_ItemTypesController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_GoogleBase_ItemsController extends Mage_Adminhtml_Controller_Action
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
        $contentBlock = $this->getLayout()->createBlock('googlebase/adminhtml_items');

        if ($this->getRequest()->getParam('captcha_token') && $this->getRequest()->getParam('captcha_url')) {
            $contentBlock->setGbaseCaptchaToken(
                Mage::helper('core')->urlDecode($this->getRequest()->getParam('captcha_token'))
            );
            $contentBlock->setGbaseCaptchaUrl(
                Mage::helper('core')->urlDecode($this->getRequest()->getParam('captcha_url'))
            );
        }

        if (!$this->_getConfig()->isValidBaseCurrencyCode($this->_getStore()->getId())) {
            $_countryInfo = $this->_getConfig()->getTargetCountryInfo($this->_getStore()->getId());
            $this->_getSession()->addNotice(
                $this->__(
                    "Base Currency should be set to %s for %s in system configuration. Otherwise item prices won't be correct in Google Base.",
                    $_countryInfo['currency_name'],
                    $_countryInfo['name']
                )
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
        $productIds = $this->getRequest()->getParam('product');

        $totalAdded = 0;

        try {
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
            if ($totalAdded > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d product(s) were successfully added to Google Base', $totalAdded)
                );
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
                    $this->__('Total of %d items(s) were successfully removed from Google Base', $totalDeleted)
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
            foreach ($itemIds as $itemId) {
                $item = Mage::getModel('googlebase/item')->load($itemId);
                if ($item->getId()) {
                    $item->activateItem();
                    $totalPublished++;
                }
            }
            if ($totalPublished > 0) {
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d items(s) were successfully published', $totalPublished)
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
                    $this->__('Total of %d items(s) were successfully saved as Inactive items', $totalHidden)
                );
            } else {
                $this->_getSession()->addError($this->__('No items were saved as Inactive items'));
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
            $collection = Mage::getResourceModel('googlebase/item_collection')
                ->addStoreFilterId($storeId)
                ->load();

            $existing = array();
            foreach ($collection as $item) {
                $existing[$item->getGbaseItemId()] = array(
                    'id'    => $item->getId(),
                    'is_hidden' => $item->getIsHidden(),
                );
            }

            $stats = Mage::getModel('googlebase/service_feed')->getItemsStatsArray($storeId);

            foreach ($existing as $entryId => $itemInfo) {

                $item = Mage::getModel('googlebase/item')->load($itemInfo['id']);

                if (!isset($stats[$entryId])) {
                    $item->delete();
                    $totalDeleted++;
                    continue;
                }

                if ($stats[$entryId]['draft'] != $itemInfo['is_hidden']) {
                    $item->setIsHidden($stats[$entryId]['draft']);
                }

                if (isset($stats[$entryId]['expires'])) {
                    $item->setExpires($stats[$entryId]['expires']);
                }

                $item->save();
                $totalUpdated++;
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d items(s) were successfully deleted, Total of %d items(s) were successfully updated', $totalDeleted, $totalUpdated)
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
            $this->_getSession()->addSuccess($this->__('Captcha confirmed successfully'));

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

    public function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if ($storeId == 0) {
            return Mage::app()->getDefaultStoreView();
        }
        return Mage::app()->getStore($storeId);
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
            try {
                $xml = new Varien_Simplexml_Element($row);
                $error = $xml->getAttribute('reason');
                $result[] = $error;
            } catch (Exception $e) {
                continue;
            }
        }
        return implode(" ", $result);
    }
}
