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
abstract class Mage_Adminhtml_Block_System_Config_Form_Field_AbstractDate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    abstract protected function getDateClass(): Varien_Data_Form_Element_Abstract;

    /**
     * @param string $format
     * @return string
     */
    abstract protected function getDateFormat(string $format): string;

    /**
     * @return bool Show date with time
     */
    abstract protected function isShowTime(): bool;

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Mage_Core_Model_Config_Element $field */
        $field = $element->getFieldConfig();

        $type = $element->getType();
        if ($type !== 'date' && $type !== 'datetime' && $type !== 'text') {
            Mage::throwException(
                Mage::helper('adminhtml')->__(
                    'Invalid frontend type for field "%s". Onyl "text", "date" and "datetime" are allowed.',
                    $field->descend('label')
                )
            );
        }

        $format = Mage_Core_Helper_Date::getDateFormatFromString($field->format->__toString());
        $format = $this->getDateFormat($format);

        if (empty($field->editable)) {
            return $this->getLocale()
                ->date((int)$element->getValue())
                ->toString($format);
        }

        $date = $this->getDateClass();
        $date->setData([
            'name'    => $element->getName(),
            'html_id' => $element->getId(),
            'image'   => $this->getSkinUrl('images/grid-cal.gif'),
            'time'    => $this->isShowTime()
        ]);
        $date->setFormat($format);
        $date->setValue($element->getValue());
        $date->setForm($element->getForm());

        return $date->getElementHtml();
    }

    /**
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        return Mage::app()->getLocale();
    }
}
