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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Carbon\Carbon;

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @deprecated
 */
class Mage_Adminhtml_Model_System_Config_Source_Date_Short
{
    public function toOptionArray()
    {
        $arr = [];
        $arr[] = ['label' => '', 'value' => ''];
        $arr[] = ['label' => sprintf('MM/DD/YY (%s)', Carbon::now()->format('m/d/y')), 'value' => '%m/%d/%y'];
        $arr[] = ['label' => sprintf('MM/DD/YYYY (%s)', Carbon::now()->format('m/d/y')), 'value' => '%m/%d/%Y'];
        $arr[] = ['label' => sprintf('DD/MM/YY (%s)', Carbon::now()->format('m/d/y')), 'value' => '%d/%m/%y'];
        $arr[] = ['label' => sprintf('DD/MM/YYYY (%s)', Carbon::now()->format('m/d/y')), 'value' => '%d/%m/%Y'];
        return $arr;
    }
}
