<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customers newsletter subscription controller
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_ManageController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getCustomerSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('Newsletter Subscription'));
        $this->renderLayout();
    }

    /**
     * @return Mage_Newsletter_ManageController|void
     */
    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            $this->getCustomerSession()->getCustomer()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->setIsSubscribed((bool)$this->getRequest()->getParam('is_subscribed', false))
            ->save();
            if ((bool)$this->getRequest()->getParam('is_subscribed', false)) {
                $this->getCustomerSession()->addSuccess($this->__('The subscription has been saved.'));
            } else {
                $this->getCustomerSession()->addSuccess($this->__('The subscription has been removed.'));
            }
        } catch (Exception $e) {
            $this->getCustomerSession()->addError($this->__('An error occurred while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
