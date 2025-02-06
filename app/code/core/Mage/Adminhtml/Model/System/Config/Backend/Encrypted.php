<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Encrypted config field backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Encrypted extends Mage_Core_Model_Config_Data
{
    /**
     * Decrypt value after loading
     */
    protected function _afterLoad()
    {
        $value = (string) $this->getValue();
        if (!empty($value) && ($decrypted = Mage::helper('core')->decrypt($value))) {
            $this->setValue($decrypted);
        }
        return $this;
    }

    /**
     * Encrypt value before saving
     *
     */
    protected function _beforeSave()
    {
        $value = (string) $this->getValue();
        // don't change value, if an obscured value came
        if (preg_match('/^\*+$/', $this->getValue())) {
            $value = $this->getOldValue();
        }
        if (!empty($value) && ($encrypted = Mage::helper('core')->encrypt($value))) {
            $this->setValue($encrypted);
        }
        return $this;
    }

    /**
     * Get & decrypt old value from configuration
     *
     * @return string
     */
    public function getOldValue()
    {
        return Mage::helper('core')->decrypt(parent::getOldValue());
    }
}
