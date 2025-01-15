<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form textarea element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method $this setCols(int $int)
 * @method $this setRows(int $int)
 */
class Varien_Data_Form_Element_Textarea extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Textarea constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('textarea');
        $this->setExtType('textarea');
        $this->setRows(2);
        $this->setCols(15);
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['title', 'class', 'style', 'onclick', 'onchange', 'rows', 'cols', 'readonly', 'disabled', 'onkeyup', 'tabindex'];
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('textarea');
        $html = '<textarea id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' >';
        $html .= $this->getEscapedValue();
        $html .= '</textarea>';
        return $html . $this->getAfterElementHtml();
    }
}
