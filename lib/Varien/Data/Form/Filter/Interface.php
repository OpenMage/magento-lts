<?php
/**
 * Form Input/Output Filter Interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Data
 */
/**
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
