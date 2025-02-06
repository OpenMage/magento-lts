<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales Billing Agreement info block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Payment_Info_Billing_Agreement extends Mage_Payment_Block_Info
{
    /**
     * Add reference id to payment method information
     *
     * @param Varien_Object|array $transport
     * @return Varien_Object|null
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if ($this->_paymentSpecificInformation !== null) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $referenceID = $info->getAdditionalInformation(
            Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::PAYMENT_INFO_REFERENCE_ID,
        );
        $transport = new Varien_Object([$this->__('Reference ID') => $referenceID,]);
        $transport = parent::_prepareSpecificInformation($transport);

        return $transport;
    }
}
