<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Special Price Attribute Block
 *
 * @category   Mage
 * @package    Mage_Bundle
 *
 * @method $this setDisableChild(bool $value)
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        return '<input id="' . $this->getElement()->getHtmlId() . '" name="' . $this->getElement()->getName()
             . '" value="' . $this->getElement()->getEscapedValue() . '" ' . $this->getElement()->serialize($this->getElement()->getHtmlAttributes()) . '/>' . "\n"
             . '<strong>[%]</strong>';
    }
}
