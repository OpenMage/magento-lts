<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Password length config field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Passwordlength extends Mage_Core_Model_Config_Data
{
    /**
     * Before save processing
     *
     * @throws Mage_Core_Exception
     * @return Mage_Adminhtml_Model_System_Config_Backend_Passwordlength
     */
    protected function _beforeSave()
    {
        if ((int) $this->getValue() < Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH) {
            Mage::throwException(Mage::helper('adminhtml')
                ->__('Password must be at least of %d characters.', Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH));
        }
        return $this;
    }
}
