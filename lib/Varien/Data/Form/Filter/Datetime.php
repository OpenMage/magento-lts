<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Filter_Datetime extends Varien_Data_Form_Filter_Date
{
    /**
     * Returns the result of filtering $value
     *
     * @param string|null $value
     * @return string|null
     */
    public function inputFilter($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format'   => $this->_dateFormat,
            'locale'        => $this->_locale,
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format'   => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'locale'        => $this->_locale,
        ]);

        $value = $filterInput->filter($value);
        return $filterInternal->filter($value);
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string|null $value
     * @return string
     */
    public function outputFilter($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format'   => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'locale'        => $this->_locale,
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format'   => $this->_dateFormat,
            'locale'        => $this->_locale,
        ]);

        $value = $filterInput->filter($value);
        return $filterInternal->filter($value);
    }
}
