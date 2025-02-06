<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * System config email sender field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Email_Sender extends Mage_Core_Model_Config_Data
{
    /**
     * Check sender name validity
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!preg_match("/^[\S ]+$/", $value)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid sender name "%s". Please use only visible characters and spaces.', $value));
        }

        if (strlen($value) > 255) {
            Mage::throwException(Mage::helper('adminhtml')->__('Maximum sender name length is 255. Please correct your settings.'));
        }
        return $this;
    }
}
