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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Renderer for specific checkbox that is used on Rule Information tab in Shopping cart price rules
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main_Renderer_Checkbox extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Checkbox render function
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $checkbox = new Varien_Data_Form_Element_Checkbox($element->getData());
        $checkbox->setForm($element->getForm());

        $elementHtml = $checkbox->getElementHtml() . sprintf(
            '<label for="%s"><b>%s</b></label><p class="note">%s</p>',
            $element->getHtmlId(),
            $element->getLabel(),
            $element->getNote(),
        );
        $html  = '<td class="label">&nbsp;</td>';

        return $html . ('<td class="value">' . $elementHtml . '</td>');
    }
}
