<?php

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Price_Scope
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => Mage::helper('core')->__('Global')],
            ['value' => '1', 'label' => Mage::helper('core')->__('Website')],
        ];
    }
}
