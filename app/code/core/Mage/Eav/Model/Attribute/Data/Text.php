<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Text Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Text extends Mage_Eav_Model_Attribute_Data_Abstract
{
    /**
     * Extract data from request and return value
     *
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        $value = $this->_getRequestValue($request);
        return $this->_applyInputFilter($value);
    }

    /**
     * Validate data
     * Return true or array of errors
     *
     * @param string|bool|null $value
     * @return bool|array
     */
    public function validateValue($value)
    {
        $errors     = [];
        $attribute  = $this->getAttribute();
        $label      = Mage::helper('eav')->__($attribute->getStoreLabel());

        if ($value === false) {
            // try to load original value and validate it
            $value = $this->getEntity()->getDataUsingMethod($attribute->getAttributeCode());
        }

        if ($attribute->getIsRequired() && (string) $value === '') {
            $errors[] = Mage::helper('eav')->__('"%s" is a required value.', $label);
        }

        if (!$errors && !$attribute->getIsRequired() && empty($value)) {
            return true;
        }

        // validate length
        $length = Mage::helper('core/string')->strlen(trim((string) $value));

        $validateRules = $attribute->getValidateRules();
        if (!empty($validateRules['min_text_length']) && $length < $validateRules['min_text_length']) {
            $v = $validateRules['min_text_length'];
            $errors[] = Mage::helper('eav')->__('"%s" length must be equal or greater than %s characters.', $label, $v);
        }

        if (!empty($validateRules['max_text_length']) && $length > $validateRules['max_text_length']) {
            $v = $validateRules['max_text_length'];
            $errors[] = Mage::helper('eav')->__('"%s" length must be equal or less than %s characters.', $label, $v);
        }

        $result = $this->_validateInputRule($value);
        if ($result !== true) {
            $errors = array_merge($errors, $result);
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Export attribute value to entity model
     *
     * @param array|string $value
     * @return $this
     */
    public function compactValue($value)
    {
        if ($value !== false) {
            $this->getEntity()->setDataUsingMethod($this->getAttribute()->getAttributeCode(), $value);
        }

        return $this;
    }

    /**
     * Restore attribute value from SESSION to entity model
     *
     * @param array|string $value
     * @return $this
     */
    public function restoreValue($value)
    {
        return $this->compactValue($value);
    }

    /**
     * Return formatted attribute value from entity model
     *
     * @param string $format
     * @return string|array
     */
    public function outputValue($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $value = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());

        return $this->_applyOutputFilter($value);
    }
}
