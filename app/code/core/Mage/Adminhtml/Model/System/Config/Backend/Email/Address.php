<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * System config email field backend model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Email_Address extends Mage_Core_Model_Config_Data
{
    /**
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        /** @var Mage_Validation_Helper_Data $validator */
        $validator  = Mage::helper('validation');

        $email = $this->getValue();
        if ($validator->validateEmail($email)->count() > 0) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid email address "%s".', $email));
        }

        return $this;
    }
}
