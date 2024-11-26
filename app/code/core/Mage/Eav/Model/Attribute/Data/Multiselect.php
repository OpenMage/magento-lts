<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * EAV Entity Attribute Multiply select Data Model
 *
 * @category   Mage
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
     * @return string|array
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
