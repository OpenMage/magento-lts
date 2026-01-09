<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Select Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Select extends Mage_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        return $this->_getRequestValue($request);
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param  array|string $value
     * @return array|bool
     */
    public function validateValue($value)
    {
        $errors     = [];
        $attribute  = $this->getAttribute();
        $label      = Mage::helper('eav')->__($attribute->getStoreLabel());

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->getEntity()->getData($attribute->getAttributeCode());
        }

        if ($attribute->getIsRequired() && empty($value) && $value != '0') {
            $errors[] = Mage::helper('eav')->__('"%s" is a required value.', $label);
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param  array|string $value
     * @return $this
     */
    public function compactValue($value)
    {
        if ($value !== false) {
            $this->getEntity()->setData($this->getAttribute()->getAttributeCode(), $value);
        }

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param  array|string $value
     * @return $this
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return a text for option value
     *
     * @param  int    $value
     * @return string
     */
    protected function _getOptionText($value)
    {
        return $this->getAttribute()->getSource()->getOptionText($value);
    }

    /**
     * Return formatted attribute value from entity model
     *
     * @param  string              $format
     * @return array|string
     * @throws Mage_Core_Exception
     */
    public function outputValue($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        switch ($format) {
            case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_JSON:
                $output = $value;
                break;
            default:
                if ($value != '') {
                    $output = $this->_getOptionText($value);
                } else {
                    $output = '';
                }

                break;
        }

        return $output;
    }
}
