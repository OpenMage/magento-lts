<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Dashboard Year-To-Date Month and Day starts Field Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Config_Form_Field_YtdStart extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $_months = [];
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = Mage::app()->getLocale()
                ->date(mktime(0, 0, 0, $i))
                ->get(Zend_Date::MONTH_NAME);
        }

        $_days = [];
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0' . $i : $i;
        }

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $element->setName($element->getName() . '[]');

        $monthsHtml = $element->setStyle('width:100px;')
            ->setValues($_months)
            ->setValue($values[0] ?? null)
            ->getElementHtml();

        $daysHtml = $element->setStyle('width:50px;')
            ->setValues($_days)
            ->setValue($values[1] ?? null)
            ->getElementHtml();

        return sprintf('%s %s', $monthsHtml, $daysHtml);
    }
}
