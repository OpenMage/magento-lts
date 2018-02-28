<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form select element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Multiselect extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('multiple');
        $this->setSize(10);
    }

    public function getName()
    {
        $name = parent::getName();
        if (strpos($name, '[]') === false) {
            $name.= '[]';
        }
        return $name;
    }

    public function getElementHtml()
    {
        $this->addClass('select multiselect');
        $html = '';
        if ($this->getCanBeEmpty() && empty($this->_data['disabled'])) {
            $html .= '<input type="hidden" name="' . parent::getName() . '" value="" />';
        }
        $html .= '<select id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" ' .
            $this->serialize($this->getHtmlAttributes()) . ' multiple="multiple">' . "\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                if (is_array($option['value'])) {
                    $html .= '<optgroup label="' . $option['label'] . '">' . "\n";
                    foreach ($option['value'] as $groupItem) {
                        $html .= $this->_optionToHtml($groupItem, $value);
                    }
                    $html .= '</optgroup>' . "\n";
                }
                else {
                    $html .= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html .= '</select>' . "\n";
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'size', 'tabindex');
    }

    public function getDefaultHtml()
    {
        $result = ( $this->getNoSpan() === true ) ? '' : '<span class="field-row">'."\n";
        $result.= $this->getLabelHtml();
        $result.= $this->getElementHtml();


        if($this->getSelectAll() && $this->getDeselectAll()) {
            $result .= '<a href="#" onclick="return ' . $this->getJsObjectName() . '.selectAll()">' .
                $this->getSelectAll() . '</a> <span class="separator">&nbsp;|&nbsp;</span>';
            $result .= '<a href="#" onclick="return ' . $this->getJsObjectName() . '.deselectAll()">' .
                $this->getDeselectAll() . '</a>';
        }

        $result.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";


        $result.= '<script type="text/javascript">' . "\n";
        $result.= '   var ' . $this->getJsObjectName() . ' = {' . "\n";
        $result.= '     selectAll: function() { ' . "\n";
        $result.= '         var sel = $("' . $this->getHtmlId() . '");' . "\n";
        $result.= '         for(var i = 0; i < sel.options.length; i ++) { ' . "\n";
        $result.= '             sel.options[i].selected = true; ' . "\n";
        $result.= '         } ' . "\n";
        $result.= '         return false; ' . "\n";
        $result.= '     },' . "\n";
        $result.= '     deselectAll: function() {' . "\n";
        $result.= '         var sel = $("' . $this->getHtmlId() . '");' . "\n";
        $result.= '         for(var i = 0; i < sel.options.length; i ++) { ' . "\n";
        $result.= '             sel.options[i].selected = false; ' . "\n";
        $result.= '         } ' . "\n";
        $result.= '         return false; ' . "\n";
        $result.= '     }' . "\n";
        $result.= '  }' . "\n";
        $result.= "\n" . '</script>';

        return $result;
    }

    public function getJsObjectName() {
         return $this->getHtmlId() . 'ElementControl';
    }

    protected function _optionToHtml($option, $selected)
    {
        $html = '<option value="'.$this->_escape($option['value']).'"';
        $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
        $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
        if (in_array((string)$option['value'], $selected)) {
            $html.= ' selected="selected"';
        }
        $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        return $html;
    }
}
