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
 * @package     Mage_Payment
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Billing Agreement abstaract class
 *
 * @category    Mage
 * @package     Mage_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_Payment_Model_Billing_Agreement extends Mage_Core_Model_Abstract
{
    /**
     * Payment method instance
     *
     * @var Mage_Payment_Model_Method_Abstract
     */
    protected $_paymentMethodInstance = null;

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
     * @param Mage_Customer_Model_Customer $customer
     */
    abstract public function place(Mage_Customer_Model_Customer $customer);

    /**
     * Cancel billing agreement
     *
     */
    abstract public function cancel();

    /**
     * Retreive payment method instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getPaymentMethodInstance()
    {
        if (is_null($this->_paymentMethodInstance)) {
            $this->_paymentMethodInstance = Mage::helper('payment')->getMethodInstance($this->getMethodCode());
        }
        return $this->_paymentMethodInstance;
    }

    /**
     * Validate data before save
     *
     * @return Mage_Payment_Model_Billing_Agreement
     */
    public function validate()
    {
        if (is_null($this->_paymentMethodInstance)
            || !$this->_paymentMethodInstance->getCode()
            || !$this->getCustomerId()
            || !$this->getReferenceId()
            || !$this->getStatus()) {
            throw new Mage_Core_Exception('Not enough data to save billing agreement instance.');
        }
        return $this;
    }

    /**
     * Before save
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->validate();
        return parent::_beforeSave();
    }
}
