<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Encrypted config field backend model
 *
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
