<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gateway URL config field backend model
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Gatewayurl extends  Mage_Core_Model_Config_Data
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
            if (!isset($parsed['scheme']) || (('https' != $parsed['scheme']) && ('http' != $parsed['scheme']))) {
                Mage::throwException(Mage::helper('core')->__('Invalid URL scheme.'));
            }
        }

        return $this;
    }
}
