<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Backend for serialized array data
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array extends Mage_Adminhtml_Model_System_Config_Backend_Serialized
{
    /**
     * Check object existence in incoming data and unset array element with '__empty' key
     *
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        try {
            Mage::helper('core/unserializeArray')->unserialize(serialize($this->getValue()));
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Serialized data is incorrect'));
        }

        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        return parent::_beforeSave();
    }
}
