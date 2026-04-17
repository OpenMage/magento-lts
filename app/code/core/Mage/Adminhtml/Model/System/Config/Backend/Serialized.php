<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Serialized extends Mage_Core_Model_Config_Data
{
    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $serializedValue = $this->getValue();
            $unserializedValue = false;
            if (!empty($serializedValue) && is_string($serializedValue)) {
                try {
                    $unserializedValue = Mage::helper('core/unserializeArray')
                        ->unserialize($serializedValue);
                } catch (Exception $exception) {
                    Mage::logException($exception);
                }
            }

            $this->setValue($unserializedValue);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        if (is_array($this->getValue())) {
            $this->setValue(serialize($this->getValue()));
        }

        return $this;
    }
}
