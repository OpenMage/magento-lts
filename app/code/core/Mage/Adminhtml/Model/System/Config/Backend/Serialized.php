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
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
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
                        ->unserialize((string)$serializedValue);
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
