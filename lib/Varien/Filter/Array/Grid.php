<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Filter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Varien_Filter_Array_Grid extends Varien_Filter_Array
{
    public function filter($grid)
    {
        $out = [];
        foreach ($grid as $i => $array) {
            $out[$i] = parent::filter($array);
        }
        return $out;
    }
}
