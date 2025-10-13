<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Adminhtml sales order create validation card block
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Adminhtml_Validation extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_validation_card');
    }

    /**
     * Return text for block`s header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('centinel')->__('3D Secure Card Validation');
    }

    /**
     * Return css class name for header block
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-payment-method';
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $payment = $this->getQuote()->getPayment();
        if (!$payment->getMethod()
            || !$payment->getMethodInstance()
            || $payment->getMethodInstance()->getIsDummy()
            || !$payment->getMethodInstance()->getIsCentinelValidationEnabled()
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
