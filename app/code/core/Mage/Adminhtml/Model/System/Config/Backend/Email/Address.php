<?php
/**
 * System config email field backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Email_Address extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!Zend_Validate::is($value, 'EmailAddress')) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid email address "%s".', $value));
        }
        return $this;
    }
}
