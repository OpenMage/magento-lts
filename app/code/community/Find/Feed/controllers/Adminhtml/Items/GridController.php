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
 * @category    
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TheFind feed product grid controller
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Adminhtml_Items_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Main index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();    
        $this->getResponse()->setBody($this->getLayout()->createBlock('find_feed/adminhtml_list_items_grid')->toHtml());
    }
    
    /**
     * Product list for mass action
     *
     * @return array
     */
    protected function _getMassActionProducts()
    {
        $idList = $this->getRequest()->getParam('item_id');
        if (!empty($idList)) {   
            $products = array();
            foreach ($idList as $id) {
                $model = Mage::getModel('catalog/product');
                if ($model->load($id)) {
                    array_push($products, $model);
                }
            }
            return $products;
        } else {
            return array();
        }
    }

    /**
     * Add product to feed mass action
     */
    public function massEnableAction()
    {
        $idList = $this->getRequest()->getParam('item_id');
        $updateAction = Mage::getModel('catalog/product_action');
        $attrData = array(
            'is_imported' => 1
        );
        $updatedProducts = count($idList);
        if ($updatedProducts) {
            try {
                $updateAction->updateAttributes($idList, $attrData, Mage::app()->getStore()->getId());
                Mage::getModel('find_feed/import')->processImport();
                $this->_getSession()->addSuccess(Mage::helper('find_feed')->__("%s product in feed.", $updatedProducts));
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('find_feed')->__("Unable to process an import. ") . $e->getMessage());
            } 
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Not add product to feed mass action
     */
    public function massDisableAction()
    {
        $updatedProducts = 0;
        foreach ($this->_getMassActionProducts() as $product) {
            $product->setIsImported(0);
            $product->save();
            $updatedProducts++;
        }
        if ($updatedProducts) {
            Mage::getModel('find_feed/import')->processImport();
            $this->_getSession()->addSuccess(Mage::helper('find_feed')->__("%s product not in feed.", $updatedProducts));
        } 
        $this->_redirect('*/*/index');
    }
}
