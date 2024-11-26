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
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * ProductAlert controller
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_AddController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return $this
     */
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

    public function testObserverAction()
    {
        $object = new Varien_Object();
        $observer = Mage::getSingleton('productalert/observer');
        $observer->process($object);
    }

    public function priceAction()
    {
        $session = $this->getCatalogSession();
        $backUrl    = $this->getRequest()->getParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            /** @var Mage_Catalog_Model_Product $product */
            $session->addError($this->__('Not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        try {
            $model  = Mage::getModel('productalert/price')
                ->setCustomerId($this->getCustomerSession()->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess($this->__('The alert subscription has been saved.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }

    public function stockAction()
    {
        $session = $this->getCatalogSession();
        $backUrl    = $this->getRequest()->getParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product) {
            $session->addError($this->__('Not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = Mage::getModel('productalert/stock')
                ->setCustomerId($this->getCustomerSession()->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess($this->__('Alert subscription has been saved.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }
}
