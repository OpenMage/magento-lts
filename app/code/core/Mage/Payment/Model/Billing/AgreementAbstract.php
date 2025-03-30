<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Billing Agreement abstaract class
 *
 * @category   Mage
 * @package    Mage_Payment
 *
 * @method string getMethodCode()
 * @method string getReferenceId()
 * @method int getStoreId()
 */
abstract class Mage_Payment_Model_Billing_AgreementAbstract extends Mage_Core_Model_Abstract
{
    /**
     * Payment method instance
     *
     * @var Mage_Payment_Model_Method_Abstract|null
     */
    protected $_paymentMethodInstance = null;

    /**
     * Billing Agreement Errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Init billing agreement
     *
     */
    abstract public function initToken();

    /**
     * Verify billing agreement details
     *
     */
    abstract public function verifyToken();

    /**
     * Create billing agreement
     *
     */
    abstract public function place();

    /**
     * Cancel billing agreement
     *
     */
    abstract public function cancel();

    /**
     * Retrieve payment method instance
     *
     * @return Mage_Payment_Model_Method_Abstract|null
     */
    public function getPaymentMethodInstance()
    {
        if (is_null($this->_paymentMethodInstance)) {
            $this->_paymentMethodInstance = Mage::helper('payment')->getMethodInstance($this->getMethodCode());
        }
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($this->getStoreId());
        }
        return $this->_paymentMethodInstance;
    }

    /**
     * Validate data before save
     *
     * @return bool
     */
    public function isValid()
    {
        $this->_errors = [];
        if (is_null($this->getPaymentMethodInstance()) || !$this->getPaymentMethodInstance()->getCode()) {
            $this->_errors[] = Mage::helper('payment')->__('Payment method code is not set.');
        }
        if (!$this->getReferenceId()) {
            $this->_errors[] = Mage::helper('payment')->__('Reference ID is not set.');
        }
        return empty($this->_errors);
    }

    /**
     * Before save, it's overridden just to make data validation on before save event
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->isValid()) {
            return parent::_beforeSave();
        }
        array_unshift($this->_errors, Mage::helper('payment')->__('Unable to save Billing Agreement:'));
        throw new Mage_Core_Exception(implode(' ', $this->_errors));
    }
}
