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
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Color config field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Color extends Mage_Core_Model_Config_Data
{
    /**
     * @throws Mage_Core_Exception
     * @return $this
     */
    protected function _beforeSave()
    {
        /** @var Mage_Core_Model_Config_Element $config */
        $config = $this->getFieldConfig();

        $validate = [];
        if (isset($config->validate)) {
            $validate = array_map('trim', explode(' ', $config->validate));
        }

        if (!(string) $this->getValue() && !in_array('required-entry', $validate)) {
            return $this;
        }

        $withHash = true;
        if (isset($config->with_hash)) {
            $withHash = $config->is('with_hash', true);
        }

        if ($withHash) {
            $regex = Varien_Data_Form_Element_Color::VALIDATION_REGEX_WITH_HASH;
            $errorMessage = 'Color must be in hexadecimal format with the hash character';
        } else {
            $regex = Varien_Data_Form_Element_Color::VALIDATION_REGEX_WITHOUT_HASH;
            $errorMessage = 'Color must be in hexadecimal format without the hash character';
        }

        if (!(bool) preg_match($regex, (string) $this->getValue())) {
            Mage::throwException(Mage::helper('adminhtml')->__($errorMessage));
        }

        return $this;
    }
}
