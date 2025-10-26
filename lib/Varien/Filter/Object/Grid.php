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
     * @param Varien_Object|array $grid
     * @return array
     * @throws Exception
     */
    public function filter($grid)
    {
        $out = [];
        if (is_array($grid)) {
            foreach ($grid as $i => $array) {
                $out[$i] = parent::filter($array);
            }
        }

        return $out;
    }
}
