<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * EAV Entity Attribute Multiply select Data Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Attribute_Data_Multiselect extends Mage_Eav_Model_Attribute_Data_Select
{
    /**
     * Extract data from request and return value
     *
     * @return array|string
     */
    public function extractValue(Zend_Controller_Request_Http $request)
    {
        $values = $this->_getRequestValue($request);
        if ($values !== false && !is_array($values)) {
            $values = [$values];
        }

        return $values;
    }

    /**
     * Export attribute value to entity model
     *
     * @inheritDoc
     */
    public function compactValue($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        return parent::compactValue($value);
    }

    /**
     * Return formatted attribute value from entity model
     *
     * @param string $format
     * @return array|string
     * @throws Mage_Core_Exception
     */
    public function outputValue($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $values = $this->getEntity()->getData($this->getAttribute()->getAttributeCode());
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        switch ($format) {
            case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_JSON:
            case Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY:
                $output = $values;
                break;
            default:
                $output = [];
                foreach ($values as $value) {
                    if (!$value) {
                        continue;
                    }

                    $output[] = $this->getAttribute()->getSource()->getOptionText($value);
                }

                $output = implode(', ', $output);
                break;
        }

        return $output;
    }
}
