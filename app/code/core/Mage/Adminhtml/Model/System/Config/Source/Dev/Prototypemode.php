<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Source model for Prototype.js loading mode
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Dev_Prototypemode
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Core_Helper_Js::PROTOTYPE_MODE_FULL, 'label' => Mage::helper('adminhtml')->__('Full (Prototype + Scriptaculous)')],
            ['value' => Mage_Core_Helper_Js::PROTOTYPE_MODE_SHIM, 'label' => Mage::helper('adminhtml')->__('Shim (Lightweight compatibility layer)')],
            ['value' => Mage_Core_Helper_Js::PROTOTYPE_MODE_NONE, 'label' => Mage::helper('adminhtml')->__('None (Fully migrated sites only)')],
        ];
    }
}
