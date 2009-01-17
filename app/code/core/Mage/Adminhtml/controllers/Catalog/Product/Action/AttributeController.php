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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml catalog product action attribute update controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Catalog_Product_Action_AttributeController extends Mage_Adminhtml_Controller_Action
{

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Mage_Catalog');
    }

    public function editAction()
    {
        if (!$this->_validateProducts()) {
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_validateProducts()) {
            return;
        }

        /* Collect Data */
        $inventoryData = $this->getRequest()->getParam('inventory', array());
        $attributesData = $this->getRequest()->getParam('attributes', array());
        $websiteRemoveData = $this->getRequest()->getParam('remove_website_ids', array());
        $websiteAddData = $this->getRequest()->getParam('add_website_ids', array());

        try {
            if ($attributesData) {
                $product = Mage::getModel('catalog/product');
                if ($inventoryData) {
                    $stockItem = Mage::getModel('cataloginventory/stock_item');
                }
                foreach ($this->_getHelper()->getProductIds() as $productId) {
                    $product->setData(array());
                    $product->setStoreId($this->_getHelper()->getSelectedStoreId())
                        ->load($productId)
                        ->setIsMassupdate(true)
                        ->setExcludeUrlRewrite(true);

                    if (!$product->getId()) {
                        continue;
                    }

                    $product->addData($attributesData);
                    if ($inventoryData) {
                        $product->setStockData($inventoryData);
                    }

                    $dataChanged = false;
                    foreach ($attributesData as $k => $v) {
                        if ($product->dataHasChangedFor($k)) {
                            $dataChanged = true;
                        }
                    }

                    if ($dataChanged) {
                        $product->save();
                    }
                    elseif ($inventoryData) {
                        $stockItem->setData(array());
                        $stockItem->loadByProduct($productId)
                            ->setProductId($productId);
                        $stockDataChanged = false;
                        foreach ($inventoryData as $k => $v) {
                            $stockItem->setDataUsingMethod($k, $v);
                            if ($stockItem->dataHasChangedFor($k)) {
                                $stockDataChanged = true;
                            }
                        }
                        if ($stockDataChanged) {
                            $stockItem->save();
                        }
                    }
                }
            }
            elseif ($inventoryData) {
                $stockItem = Mage::getModel('cataloginventory/stock_item');

                foreach ($this->_getHelper()->getProductIds() as $productId) {
                    $stockItem->setData(array());
                    $stockItem->loadByProduct($productId)
                        ->setProductId($productId);

                    $stockDataChanged = false;
                    foreach ($inventoryData as $k => $v) {
                        $stockItem->setDataUsingMethod($k, $v);
                        if ($stockItem->dataHasChangedFor($k)) {
                            $stockDataChanged = true;
                        }
                    }
                    if ($stockDataChanged) {
                        $stockItem->save();
                    }
                }
            }

            if ($websiteAddData || $websiteRemoveData) {
                $productWebsite = Mage::getModel('catalog/product_website');
                /* @var $productWebsite Mage_Catalog_Model_Product_Website */

                if ($websiteRemoveData) {
                    $productWebsite->removeProducts($websiteRemoveData, $this->_getHelper()->getProductIds());
                }
                if ($websiteAddData) {
                    $productWebsite->addProducts($websiteAddData, $this->_getHelper()->getProductIds());
                }

                $this->_getSession()->addNotice(
                    $this->__('Please refresh "Catalog Rewrites" and "Layered Navigation Indices" in System -> <a href="%s">Cache Management</a>', $this->getUrl('adminhtml/system_cache'))
                );
            }

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) were successfully updated',
                count($this->_getHelper()->getProductIds()))
            );
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('There was an error while updating product(s) attributes'));
        }

        $this->_redirect('*/catalog_product/', array('store'=>$this->_getHelper()->getSelectedStoreId()));
    }

    /**
     * Validate selection of products for massupdate
     *
     * @return boolean
     */
    protected function _validateProducts()
    {
        if (!is_array($this->_getHelper()->getProductIds())) {
            $this->_getSession()->addError($this->__('Please select products for attributes update'));
            $this->_redirect('*/catalog_product/', array('_current'=>true));
            return false;
        }

        return true;
    }

    /**
     * Rertive data manipulation helper
     *
     * @return Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml/catalog_product_edit_action_attribute');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes');
    }
}