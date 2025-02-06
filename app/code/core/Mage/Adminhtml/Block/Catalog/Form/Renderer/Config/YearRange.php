<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog Custom Options Config Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Form_Renderer_Config_YearRange extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setStyle('width:70px;')
            ->setName($element->getName() . '[]');

        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $from = $element->setValue($values[0] ?? null)->getElementHtml();
        $to = $element->setValue($values[1] ?? null)->getElementHtml();
        return Mage::helper('adminhtml')->__('from') . ' ' . $from
            . ' '
            . Mage::helper('adminhtml')->__('to') . ' ' . $to;
    }
}
