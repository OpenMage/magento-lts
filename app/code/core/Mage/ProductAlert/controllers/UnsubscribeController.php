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
 * @package     Mage_ProductAlert
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert unsubscribe controller
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_UnsubscribeController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!Mage::getSingleton('customer/session')->getBeforeUrl()) {
                Mage::getSingleton('customer/session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }

    public function priceAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }
        $session    = Mage::getSingleton('catalog/session');

        /* @var $session Mage_Catalog_Model_Session */
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            /* @var $product Mage_Catalog_Model_Product */
            Mage::getSingleton('customer/session')->addError($this->__('The product is not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('productalert/price')
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }

            $session->addSuccess($this->__('The alert subscription has been deleted.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function priceAllAction()
    {
        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */

        try {
            Mage::getModel('productalert/price')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess($this->__('You will no longer receive price alerts for this product.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }

    public function stockAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        $session = Mage::getSingleton('catalog/session');
        /* @var $session Mage_Catalog_Model_Session */
        $product = Mage::getModel('catalog/product')->load($productId);
        /* @var $product Mage_Catalog_Model_Product */
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            Mage::getSingleton('customer/session')->addError($this->__('The product was not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('productalert/stock')
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $session->addSuccess($this->__('You will no longer receive stock alerts for this product.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function stockAllAction()
    {
        $session = Mage::getSingleton('customer/session');
        /* @var $session Mage_Customer_Model_Session */

        try {
            Mage::getModel('productalert/stock')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess($this->__('You will no longer receive stock alerts.'));
        }
        catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
