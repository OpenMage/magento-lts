<?php

/**
 * @category   Varien
 * @package    Varien_Filter
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
