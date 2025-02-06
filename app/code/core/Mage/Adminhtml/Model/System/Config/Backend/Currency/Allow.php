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
class Mage_Adminhtml_Model_System_Config_Backend_Currency_Allow extends Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract
{
    /**
     * Check is isset default display currency in allowed currencies
     * Check allowed currencies is available in installed currencies
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $exceptions = [];
        foreach ($this->_getAllowedCurrencies() as $currencyCode) {
            if (!in_array($currencyCode, $this->_getInstalledCurrencies())) {
                $exceptions[] = Mage::helper('adminhtml')->__('Selected allowed currency "%s" is not available in installed currencies.', Mage::app()->getLocale()->currency($currencyCode)->getName());
            }
        }

        if (!in_array($this->_getCurrencyDefault(), $this->_getAllowedCurrencies())) {
            $exceptions[] = Mage::helper('adminhtml')->__('Default display currency "%s" is not available in allowed currencies.', Mage::app()->getLocale()->currency($this->_getCurrencyDefault())->getName());
        }

        if ($exceptions) {
            Mage::throwException(implode("\n", $exceptions));
        }

        return $this;
    }
}
