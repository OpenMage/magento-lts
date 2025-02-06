<?php

/**
 * @category   Varien
 * @package    Varien_Data
 */

/**
 * Form Input/Output Filter Interface
 *
 * @category   Varien
 * @package    Varien_Data
 */
interface Varien_Data_Form_Filter_Interface
{
    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value);

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value);
}
