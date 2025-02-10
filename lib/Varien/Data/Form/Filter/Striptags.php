<?php
/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Data
 */
/**
 * @package    Varien_Data
 */


class Varien_Data_Form_Filter_Striptags implements Varien_Data_Form_Filter_Interface
{
    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value)
    {
        return strip_tags($value);
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        return $value;
    }
}
