<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
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
