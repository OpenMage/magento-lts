<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Backend for serialized array data
 *
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
