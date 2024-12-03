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
 * Varien Form element renderer to display link element
 *
 * @category   Varien
 * @package    Varien_Data
 *
 * @method string getBeforeElementHtml()
 */
class Varien_Data_Form_Element_Link extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Link constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('link');
    }

    /**
     * Generates element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml();
        $html .= '<a id="' . $this->getHtmlId() . '" ' . $this->serialize($this->getHtmlAttributes()) . '>' . $this->getEscapedValue() . "</a>\n";
        $html .= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Prepare array of anchor attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['charset', 'coords', 'href', 'hreflang', 'rel', 'rev', 'name',
            'shape', 'target', 'accesskey', 'class', 'dir', 'lang', 'style',
            'tabindex', 'title', 'xml:lang', 'onblur', 'onclick', 'ondblclick',
            'onfocus', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover',
            'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup'];
    }
}
