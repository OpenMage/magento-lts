<?php
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
 * Adminhtml system config label field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Label extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue()->unsPath();
        return parent::render($element);
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $field = $element->getFieldConfig();
        $element->setValue($field->value);
        $element->setBold(!empty($field->bold));

        return $element->getElementHtml();
    }
}
