<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Model_System_Config_Source_Dashboard_Chart_Type
{
    /**
     * @return array<array<string, string>>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'line', 'label' => Mage::helper('adminhtml')->__('Line')],
            ['value' => 'bar', 'label' => Mage::helper('adminhtml')->__('Bar')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'bar' => Mage::helper('adminhtml')->__('Bar'),
            'line' => Mage::helper('adminhtml')->__('Line'),
        ];
    }
}
