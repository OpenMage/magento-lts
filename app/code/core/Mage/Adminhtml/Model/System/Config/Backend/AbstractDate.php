<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Model_System_Config_Backend_AbstractDate extends Mage_Core_Model_Config_Data
{
    /**
     * @param string $format
     * @return string
     */
    abstract protected function getDateFormat(string $format): string;

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $value = (string)$this->getValue();
        if ($value !== '') {
            $this->setValue($this->getLocale()->date($value)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        if ($value !== '') {
            $this->setValue($this->filterDateTime($value));
        }

        return $this;
    }

    /**
     * @param string $date
     * @return string
     */
    protected function filterDateTime(string $date): string
    {
        /** @var Mage_Core_Model_Config_Element $field */
        $field = $this->getFieldConfig();

        $format = Mage_Core_Helper_Date::getDateFormatFromString($field->format->__toString());
        $format = $this->getDateFormat($format);

        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format' => $format
        ]);

        $date = $filterInput->filter($date);

        // convert to utc
        return $this->getLocale()->utcDate(null, $date, true)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        return Mage::app()->getLocale();
    }
}
