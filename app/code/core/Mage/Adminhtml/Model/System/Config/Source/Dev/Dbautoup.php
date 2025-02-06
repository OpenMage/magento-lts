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
