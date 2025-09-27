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
class Mage_Adminhtml_Model_System_Config_Source_Dev_Dbautoup
{
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Core_Model_Resource::AUTO_UPDATE_ALWAYS, 'label' => Mage::helper('adminhtml')->__('Always (during development)')],
            ['value' => Mage_Core_Model_Resource::AUTO_UPDATE_ONCE,   'label' => Mage::helper('adminhtml')->__('Only Once (version upgrade)')],
            ['value' => Mage_Core_Model_Resource::AUTO_UPDATE_NEVER,  'label' => Mage::helper('adminhtml')->__('Never (production)')],
        ];
    }
}
