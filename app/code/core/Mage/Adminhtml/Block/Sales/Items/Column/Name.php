<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sales Order items name column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Default
{
    /**
     * Add line breaks and truncate value
     *
     * @param  string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = Mage::helper('core/string')->truncate($value, 55, '', $remainder);
        return [
            'value' => nl2br($value),
            'remainder' => nl2br($remainder),
        ];
    }
}
