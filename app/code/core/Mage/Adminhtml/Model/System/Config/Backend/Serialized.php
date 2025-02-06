<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
            if (!empty($serializedValue)) {
                try {
                    $unserializedValue = Mage::helper('core/unserializeArray')
                        ->unserialize((string) $serializedValue);
                } catch (Exception $e) {
                    Mage::logException($e);
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
