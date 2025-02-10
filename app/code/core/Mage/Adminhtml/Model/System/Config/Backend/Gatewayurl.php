<?php
/**
 * Gateway URL config field backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Gatewayurl extends Mage_Core_Model_Config_Data
{
    /**
     * Before save processing
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Model_System_Config_Backend_Gatewayurl
     */
    protected function _beforeSave()
    {
        if ($this->getValue()) {
            $parsed = parse_url($this->getValue());
            if (!isset($parsed['scheme']) || (($parsed['scheme'] != 'https') && ($parsed['scheme'] != 'http'))) {
                Mage::throwException(Mage::helper('core')->__('Invalid URL scheme.'));
            }
        }

        return $this;
    }
}
