<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Fieldset config form element renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Frontend_Product_Watermark extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public const XML_PATH_IMAGE_TYPES = 'global/catalog/product/media/image_types';

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);
        $renderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');

        $attributes = Mage::getConfig()->getNode(self::XML_PATH_IMAGE_TYPES)->asArray();

        foreach ($attributes as $key => $attribute) {
            /**
             * Watermark size field
             */
            $field = new Varien_Data_Form_Element_Text();
            $field->setName("groups[watermark][fields][{$key}_size][value]")
                ->setForm($this->getForm())
                ->setLabel(Mage::helper('adminhtml')->__('Size for %s', $attribute['title']))
                ->setRenderer($renderer);
            $html .= $field->toHtml();

            /**
             * Watermark upload field
             */
            $field = new Varien_Data_Form_Element_Imagefile();
            $field->setName("groups[watermark][fields][{$key}_image][value]")
                ->setForm($this->getForm())
                ->setLabel(Mage::helper('adminhtml')->__('Watermark File for %s', $attribute['title']))
                ->setRenderer($renderer);
            $html .= $field->toHtml();

            /**
             * Watermark position field
             */
            $field = new Varien_Data_Form_Element_Select();
            $field->setName("groups[watermark][fields][{$key}_position][value]")
                ->setForm($this->getForm())
                ->setLabel(Mage::helper('adminhtml')->__('Position of Watermark for %s', $attribute['title']))
                ->setRenderer($renderer)
                ->setValues(Mage::getSingleton('adminhtml/system_config_source_watermark_position')->toOptionArray());
            $html .= $field->toHtml();
        }

        return $html . $this->_getFooterHtml($element);
    }

    protected function _getHeaderHtml($element)
    {
        $id = $element->getHtmlId();
        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        $html = '<h4 class="icon-head head-edit-form">' . $element->getLegend() . '</h4>';
        $html .= '<fieldset class="config" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        // field label column
        $html .= '<table cellspacing="0"><colgroup class="label" /><colgroup class="value" />';
        if (!$default) {
            $html .= '<colgroup class="use-default" />';
        }

        return $html . '<tbody>';
    }

    protected function _getFooterHtml($element)
    {
        return '</tbody></table></fieldset>';
    }
}
