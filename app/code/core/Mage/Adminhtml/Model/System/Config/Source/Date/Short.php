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
    public function toOptionArray()
    {
        $arr = [];
        $arr[] = ['label' => '', 'value' => ''];
        $arr[] = ['label' => sprintf('MM/DD/YY (%s)', date('m/d/y')), 'value' => '%m/%d/%y'];
        $arr[] = ['label' => sprintf('MM/DD/YYYY (%s)', date('m/d/Y')), 'value' => '%m/%d/%Y'];
        $arr[] = ['label' => sprintf('DD/MM/YY (%s)', date('d/m/y')), 'value' => '%d/%m/%y'];
        $arr[] = ['label' => sprintf('DD/MM/YYYY (%s)', date('d/m/Y')), 'value' => '%d/%m/%Y'];
        return $arr;
    }
}
