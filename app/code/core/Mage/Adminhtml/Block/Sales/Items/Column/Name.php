<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Sales Order items name column renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Default
{
    /**
     * Add line breaks and truncate value
     *
     * @param string $value
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
