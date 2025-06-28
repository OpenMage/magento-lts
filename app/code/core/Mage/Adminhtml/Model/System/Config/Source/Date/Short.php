<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Carbon\Carbon;

/**
 * @package    Mage_Adminhtml
 * @deprecated
 */
class Mage_Adminhtml_Model_System_Config_Source_Date_Short
{
    public function toOptionArray()
    {
        $format = 'm/d/y';

        $arr = [];
        $arr[] = ['label' => '', 'value' => ''];
        $arr[] = ['label' => sprintf('MM/DD/YY (%s)', Carbon::now()->format($format)), 'value' => '%m/%d/%y'];
        $arr[] = ['label' => sprintf('MM/DD/YYYY (%s)', Carbon::now()->format($format)), 'value' => '%m/%d/%Y'];
        $arr[] = ['label' => sprintf('DD/MM/YY (%s)', Carbon::now()->format($format)), 'value' => '%d/%m/%y'];
        $arr[] = ['label' => sprintf('DD/MM/YYYY (%s)', Carbon::now()->format($format)), 'value' => '%d/%m/%Y'];
        return $arr;
    }
}
