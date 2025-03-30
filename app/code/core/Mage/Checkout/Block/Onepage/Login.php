<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Block_Onepage_Login extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('login', ['label' => Mage::helper('checkout')->__('Checkout Method'), 'allow' => true]);
        }
        parent::_construct();
    }

    /**
     * @return Mage_Core_Model_Message_Collection
     */
    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }

    /**
     * @return string
     */
    public function getPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', ['_secure' => true]);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getMethod();
    }

    /**
     * @return array
     */
    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->getUrl('*/*');
    }

    /**
     * @return string
     */
    public function getErrorUrl()
    {
        return $this->getUrl('*/*');
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        return Mage::getSingleton('customer/session')->getUsername(true);
    }
}
