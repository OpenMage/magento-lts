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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Backend_Datetime extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Formatting date value before save
     *
     * Should set (bool, string) correct type for empty value from html form,
     * necessary for farther process, else date string
     *
     * @param Varien_Object $object
     * @throws Mage_Eav_Exception
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $_formated     = $object->getData($attributeName . '_is_formated');
        if (!$_formated && $object->hasData($attributeName)) {
            try {
                $value = $this->formatDate($object->getData($attributeName));
            } catch (Exception $e) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid date'));
            }

            if (is_null($value)) {
                $value = $object->getData($attributeName);
            }

            $object->setData($attributeName, $value);
            $object->setData($attributeName . '_is_formated', true);
        }

        return $this;
    }

    /**
     * Prepare date for save in DB
     *
     * string format used from input fields (all date input fields need apply locale settings)
     * int value can be declared in code (this meen whot we use valid date)
     *
     * @param   string|int $date
     * @return  string|null
     */
    public function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // unix timestamp given - simply instantiate date object
        if (preg_match('/^[0-9]+$/', $date)) {
            $date = new Zend_Date((int)$date);
        } elseif (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
            // international format
            $zendDate = new Zend_Date();
            $date = $zendDate->setIso($date);
        } else {
            // parse this date in current locale, do not apply GMT offset
            $date = Mage::app()->getLocale()->date(
                $date,
                Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                null,
                false
            );
        }
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }
}
