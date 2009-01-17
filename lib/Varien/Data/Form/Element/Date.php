
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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Varien data selector form element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Varien_Data_Form_Element_Date extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    public function getElementHtml()
    {
        $html = null;

        if (!($datetimeFormat = $this->getFormat())){
            if($this->getTime()) {
                $datetimeFormat = '%m/%d/%y %I:%M %p';
            } else {
                $datetimeFormat = '%m/%d/%y';
            }
            $this->setFormat($datetimeFormat);

        }

        $this->addClass('input-text');

        $html = '<input name="'.$this->getName().'" id="'.$this->getHtmlId().'" value="'.$this->getEscapedValue().'" ' . $this->serialize($this->getHtmlAttributes()) . ' style="width:100px; "/> <img src="' . $this->getImage() . '" alt="" class="v-middle" id="'.$this->getHtmlId().'_trig" title="' . __('Select Date') . '"' . ($this->getDisabled() ? ' style="display:none;"' : '') . ' />';
        $html.= '<script type="text/javascript">
            Calendar.setup({
                inputField : "'.$this->getHtmlId().'",
                lang : "fr",
                ';


        if($this->getTime()) {
            $html.='showsTime:true,' . "\n";
            $html.='ifFormat : "' . $datetimeFormat . '",' . "\n";
        } else {
            $html.='ifFormat : "' . $datetimeFormat . '",' . "\n";
        }
        $html.='button : "'.$this->getHtmlId().'_trig",
                align : "Bl",
                singleClick : true
            });
        </script>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }

    public function getEscapedValue($index=null) {

        if($this->getFormat() && $this->getValue()) {
            $timestamp = strtotime($this->getValue());
            // if (empty($timestamp)) {
            if (empty($timestamp) || (-1 === $timestamp)) {
                return '';
            }
            return strftime($this->getFormat(), $timestamp);
        }

        return htmlspecialchars($this->getValue());
    }

}// Class Varien_Data_Form_Element_Date END
