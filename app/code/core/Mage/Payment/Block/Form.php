<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Payment method form base block
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof Mage_Payment_Model_Method_Abstract)) {
            Mage::throwException($this->__('Cannot retrieve the payment method model object.'));
        }

        return $method;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * Retrieve field value data from payment info object
     *
     * @param   string $field
     * @return  string
     */
    public function getInfoData($field)
    {
        return $this->escapeHtml($this->getMethod()->getInfoInstance()->getData($field));
    }

    /**
     * Check whether current payment method can create billing agreement
     *
     * @return bool
     */
    public function canCreateBillingAgreement()
    {
        return $this->getMethod()->canCreateBillingAgreement();
    }
}
