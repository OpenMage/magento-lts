<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

declare(strict_types=1);

/**
 * Magento model aliases require underscore-separated class names.
 *
 * @SuppressWarnings("php:S101")
 */
class Mage_Adminhtml_Model_System_Config_Source_Grid_Export_Separator
{
    /** @return list<array{value: string, label: string}> */
    public function toOptionArray(): array
    {
        return [
            ['value' => Mage_Adminhtml_Model_Grid_Export::CSV_SEPARATOR_COMMA, 'label' => Mage::helper('adminhtml')->__('Comma (,)')],
            ['value' => Mage_Adminhtml_Model_Grid_Export::CSV_SEPARATOR_SEMICOLON, 'label' => Mage::helper('adminhtml')->__('Semicolon (;)')],
        ];
    }
}
