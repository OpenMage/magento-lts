<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Date_Short
{
    public function toOptionArray()
    {
        $arr = [];
        $arr[] = ['label' => '', 'value' => ''];
        $arr[] = ['label' => strftime('MM/DD/YY (%m/%d/%y)'), 'value' => '%m/%d/%y'];
        $arr[] = ['label' => strftime('MM/DD/YYYY (%m/%d/%Y)'), 'value' => '%m/%d/%Y'];
        $arr[] = ['label' => strftime('DD/MM/YY (%d/%m/%y)'), 'value' => '%d/%m/%y'];
        $arr[] = ['label' => strftime('DD/MM/YYYY (%d/%m/%Y)'), 'value' => '%d/%m/%Y'];
        return $arr;
    }
}
