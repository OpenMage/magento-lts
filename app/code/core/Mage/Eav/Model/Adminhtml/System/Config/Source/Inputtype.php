<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'text', 'label' => Mage::helper('eav')->__('Text Field')],
            ['value' => 'textarea', 'label' => Mage::helper('eav')->__('Text Area')],
            ['value' => 'date', 'label' => Mage::helper('eav')->__('Date')],
            ['value' => 'boolean', 'label' => Mage::helper('eav')->__('Yes/No')],
            ['value' => 'multiselect', 'label' => Mage::helper('eav')->__('Multiple Select')],
            ['value' => 'select', 'label' => Mage::helper('eav')->__('Dropdown')],
        ];
    }
}
