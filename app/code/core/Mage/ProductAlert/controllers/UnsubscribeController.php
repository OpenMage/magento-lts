<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductAlert unsubscribe controller
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_UnsubscribeController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getCustomerSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!$this->getCustomerSession()->getBeforeUrl()) {
                $this->getCustomerSession()->setBeforeUrl($this->_getRefererUrl());
            }
        }
        return $this;
    }

    public function priceAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        $session = $this->getCatalogSession();

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->getCustomerSession()->addError($this->__('The product is not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('productalert/price')
                ->setCustomerId($this->getCustomerSession()->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }

            $session->addSuccess($this->__('The alert subscription has been deleted.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function priceAllAction()
    {
        $session = $this->getCustomerSession();

        try {
            Mage::getModel('productalert/price')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId(),
            );
            $session->addSuccess($this->__('You will no longer receive price alerts for this product.'));
        } catch (Exception $e) {
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

        $session = $this->getCatalogSession();
        $product = Mage::getModel('catalog/product')->load($productId);

        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->getCustomerSession()->addError($this->__('The product was not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('productalert/stock')
                ->setCustomerId($this->getCustomerSession()->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $session->addSuccess($this->__('You will no longer receive stock alerts for this product.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function stockAllAction()
    {
        $session = $this->getCustomerSession();

        try {
            Mage::getModel('productalert/stock')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId(),
            );
            $session->addSuccess($this->__('You will no longer receive stock alerts.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
