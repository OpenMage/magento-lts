<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Dashboard Year-To-Date Month and Day starts Field Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Config_Form_Field_YtdStart extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $_months = [];
        for ($i = 1; $i <= 12; $i++) {
            $_months[$i] = Mage::app()->getLocale()
                ->date(mktime(null,null,null,$i))
                ->get(Zend_Date::MONTH_NAME);
        }

        $_days = [];
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0'.$i : $i;
        }

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $element->setName($element->getName() . '[]');

        $_monthsHtml = $element->setStyle('width:100px;')
            ->setValues($_months)
            ->setValue(isset($values[0]) ? $values[0] : null)
            ->getElementHtml();

        $_daysHtml = $element->setStyle('width:50px;')
            ->setValues($_days)
            ->setValue(isset($values[1]) ? $values[1] : null)
            ->getElementHtml();

        return sprintf('%s %s', $_monthsHtml, $_daysHtml);
    }
}
