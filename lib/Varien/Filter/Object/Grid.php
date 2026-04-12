<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Filter
 */

class Varien_Filter_Object_Grid extends Varien_Filter_Object
{
    /**
     * @param  array|Varien_Object                   $grid
     * @return array<array-key, mixed>|Varien_Object
     * @throws Exception
     */
    public function filter($grid)
    {
        $out = [];
        if (is_array($grid)) {
            foreach ($grid as $index => $array) {
                $out[$index] = parent::filter($array);
            }
        }

        return $out;
    }
}
