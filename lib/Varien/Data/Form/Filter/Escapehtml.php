<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form Input/Output Escape HTML entities Filter
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Filter_Escapehtml implements Varien_Data_Form_Filter_Interface
{
    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value)
    {
        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        return htmlspecialchars($value);
    }
}
