<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 * @deprecated
 */
class Mage_Adminhtml_Model_System_Config_Source_Date_Short
{
    /**
     * @return array<int, array<string, string>>
     */
    public function toOptionArray()
    {
        $now = Mage::helper('core/clock')->now();
        return [
            ['label' => '', 'value' => ''],
            ['label' => sprintf('MM/DD/YY (%s)', $now->format('m/d/y')), 'value' => '%m/%d/%y'],
            ['label' => sprintf('MM/DD/YYYY (%s)', $now->format('m/d/Y')), 'value' => '%m/%d/%Y'],
            ['label' => sprintf('DD/MM/YY (%s)', $now->format('d/m/y')), 'value' => '%d/%m/%y'],
            ['label' => sprintf('DD/MM/YYYY (%s)', $now->format('d/m/Y')), 'value' => '%d/%m/%Y'],
        ];
    }
}
