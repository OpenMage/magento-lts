<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Directory currency backend model
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
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
