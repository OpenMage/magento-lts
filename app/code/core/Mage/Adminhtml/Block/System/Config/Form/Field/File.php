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
 * File config field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_File extends Varien_Data_Form_Element_File
{
    /**
     * Get element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();
        return $html . $this->_getDeleteCheckbox();
    }

    /**
     * Get html for additional delete checkbox field
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ((string) $this->getValue()) {
            $label = Mage::helper('adminhtml')->__('Delete File');
            $html .= '<div>' . Mage::helper('adminhtml')->escapeHtml($this->getValue()) . ' ';
            $html .= '<input type="checkbox" name="' . parent::getName() . '[delete]" value="1" class="checkbox" id="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' disabled="disabled"' : '') . '/>';
            $html .= '<label for="' . $this->getHtmlId() . '_delete"' . ($this->getDisabled() ? ' class="disabled"' : '') . '> ' . $label . '</label>';
            $html .= '<input type="hidden" name="' . parent::getName() . '[value]" value="' . $this->getValue() . '" />';
            $html .= '</div>';
        }
        return $html;
    }
}
