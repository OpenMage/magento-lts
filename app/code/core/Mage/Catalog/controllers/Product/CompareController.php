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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog comapare controller
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Product_CompareController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    public function indexAction()
    {
        $items = $this->getRequest()->getParam('items');

        if ($beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            Mage::getSingleton('catalog/session')
                ->setBeforeCompareUrl(Mage::helper('core')->urlDecode($beforeUrl));
        }

        if ($items) {
            $items = explode(',', $items);
            $list = Mage::getSingleton('catalog/product_compare_list');
            $list->addProducts($items);
            $this->_redirect('*/*/*');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Add item to compare list
     */
    public function addAction()
    {
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if ($product->getId()/* && !$product->isSuper()*/) {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                Mage::getSingleton('catalog/session')->addSuccess(
                    $this->__('Product %s successfully added to compare list', $product->getName())
                );
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
            }

            Mage::helper('catalog/product_compare')->calculate();
        }

        $this->_redirectReferer();
    }

    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                    Mage::getSingleton('catalog/session')->addSuccess(
                        $this->__('Product %s successfully removed from compare list', $product->getName())
                    );
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                }
            }
        }
        $this->_redirectReferer();
    }

    public function clearAction()
    {
        $items = Mage::getResourceModel('catalog/product_compare_item_collection')
            //->useProductItem(true)
            //->setStoreId(Mage::app()->getStore()->getId())
            ;

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        }
        else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        $session = Mage::getSingleton('catalog/session');
        /* @var $session Mage_Catalog_Model_Session */

        try {
            $items->clear();
            $session->addSuccess($this->__('Compare list successfully cleared'));
            Mage::helper('catalog/product_compare')->calculate();
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('There was an error while cleared compare list'));
        }

        $this->_redirectReferer();
    }
}
