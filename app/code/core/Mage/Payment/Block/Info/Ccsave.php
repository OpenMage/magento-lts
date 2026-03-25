<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Info_Ccsave extends Mage_Payment_Block_Info_Cc
{
    /**
     * Show name on card, expiration date and full cc number
     *
     * Expiration date and full number will show up only in secure mode (only for admin, not in emails or pdfs)
     *
     * @param  array|Varien_Object $transport
     * @return Varien_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if ($this->_paymentSpecificInformation !== null) {
            return $this->_paymentSpecificInformation;
        }

        $info = $this->getInfo();
        $transport = new Varien_Object([Mage::helper('payment')->__('Name on the Card') => $info->getCcOwner(),]);
        $transport = parent::_prepareSpecificInformation($transport);
        if (!$this->getIsSecureMode()) {
            $transport->addData([
                Mage::helper('payment')->__('Expiration Date') => $this->_formatCardDate(
                    $info->getCcExpYear(),
                    $this->getCcExpMonth(),
                ),
                Mage::helper('payment')->__('Credit Card Number') => $info->getCcNumber(),
            ]);
        }

        return $transport;
    }
}
