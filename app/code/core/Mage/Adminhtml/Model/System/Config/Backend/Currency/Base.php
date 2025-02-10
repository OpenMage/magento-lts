<?php
/**
 * Adminhtml Directory currency backend model
Allows dispatching before and after events for each controller action
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Currency_Base extends Mage_Adminhtml_Model_System_Config_Backend_Currency_Abstract
{
    /**
     * Check base currency is available in installed currencies
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if (!in_array($this->getValue(), $this->_getInstalledCurrencies())) {
            Mage::throwException(Mage::helper('adminhtml')->__('Selected base currency is not available in installed currencies.'));
        }

        return $this;
    }
}
