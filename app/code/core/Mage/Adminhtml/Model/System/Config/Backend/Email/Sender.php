<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
