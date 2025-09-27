<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Currency_Default extends Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract
{
    /**
     * Check default currency is available in installed currencies
     * Check default currency is available in allowed currencies
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $allowedCurrencies = $this->_getAllowedCurrencies();

        if (!is_array($allowedCurrencies)) {
            Mage::throwException(Mage::helper('adminhtml')->__('At least one currency has to be allowed.'));
        }

        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(Mage::helper('adminhtml')->__('Selected default display currency is not available in installed currencies.'));
        }

        if (!in_array($this->getValue(), $allowedCurrencies)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Selected default display currency is not available in allowed currencies.'));
        }

        return $this;
    }
}
