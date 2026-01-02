<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

use Carbon\Carbon;

/**
 * EAV Entity Attribute Date Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Date extends Mage_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @return false|string
     * @throws Mage_Core_Exception
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        $value = $this->_getRequestValue($request);
        return $value ? $this->_applyInputFilter($value) : false;
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param  array|string                    $value
     * @return array|bool
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function validateValue($value)
    {
        $errors     = [];
        $attribute  = $this->getAttribute();
        $label      = $attribute->getStoreLabel();

        if ($attribute->getIsRequired() && empty($value)) {
            $errors[] = Mage::helper('eav')->__('"%s" is a required value.', $label);
        }

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->getEntity()->getDataUsingMethod($attribute->getAttributeCode());
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        $result = $this->_validateInputRule($value);
        if ($result !== true) {
            $errors = array_merge($errors, $result);
        }

        //range validation
        $validateRules = $attribute->getValidateRules();
        if ((!empty($validateRules['date_range_min']) && (Carbon::parse($value)->getTimestamp() < $validateRules['date_range_min']))
            || (!empty($validateRules['date_range_max']) && (Carbon::parse($value)->getTimestamp() > $validateRules['date_range_max']))
        ) {
            $format = 'd/m/Y';
            if (!empty($validateRules['date_range_min']) && !empty($validateRules['date_range_max'])) {
                $errors[] = Mage::helper('customer')->__(
                    'Please enter a valid date between %s and %s at %s.',
                    Carbon::createFromTimestamp($validateRules['date_range_min'])->format($format),
                    Carbon::createFromTimestamp($validateRules['date_range_max'])->format($format),
                    $label,
                );
            } elseif (!empty($validateRules['date_range_min'])) {
                $errors[] = Mage::helper('customer')->__(
                    'Please enter a valid date equal to or greater than %s at %s.',
                    Carbon::createFromTimestamp($validateRules['date_range_min'])->format($format),
                    $label,
                );
            } elseif (!empty($validateRules['date_range_max'])) {
                $errors[] = Mage::helper('customer')->__(
                    'Please enter a valid date less than or equal to %s at %s.',
                    Carbon::createFromTimestamp($validateRules['date_range_max'])->format($format),
                    $label,
                );
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param  array|string        $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function compactValue($value)
    {
        if ($value !== false && empty($value)) {
            $value = null;
        }

        $this->getEntity()->setDataUsingMethod($this->getAttribute()->getAttributeCode(), $value);

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param  array|string        $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return formatted attribute value from entity model
     *
     * @param  string                $format
     * @return array|string
     * @throws Mage_Core_Exception
     * @throws Zend_Locale_Exception
     */
    public function outputValue($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if ($value) {
            switch ($format) {
                case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT:
                case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_HTML:
                case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_PDF:
                    $this->_dateFilterFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                    break;
            }

            $value = $this->_applyOutputFilter($value);
        }

        $this->_dateFilterFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        return $value;
    }
}
