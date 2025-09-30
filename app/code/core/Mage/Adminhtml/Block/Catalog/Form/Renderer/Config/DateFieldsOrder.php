<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog Custom Options Config Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Form_Renderer_Config_DateFieldsOrder extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $_options = [
            'd' => Mage::helper('adminhtml')->__('Day'),
            'm' => Mage::helper('adminhtml')->__('Month'),
            'y' => Mage::helper('adminhtml')->__('Year'),
        ];

        $element->setValues($_options)
            ->setClass('select-date')
            ->setName($element->getName() . '[]');
        if ($element->getValue()) {
            $values = explode(',', $element->getValue());
        } else {
            $values = [];
        }

        $_parts = [];
        $_parts[] = $element->setValue($values[0] ?? null)->getElementHtml();
        $_parts[] = $element->setValue($values[1] ?? null)->getElementHtml();
        $_parts[] = $element->setValue($values[2] ?? null)->getElementHtml();

        return implode(' <span>/</span> ', $_parts);
    }
}
