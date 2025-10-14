<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product form image field helper
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media') . 'catalog/product/' . $this->getValue();
        }

        return $url;
    }

    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($attribute = $this->getEntityAttribute()) {
            if (!$attribute->getIsRequired()) {
                $html .= parent::_getDeleteCheckbox();
            } else {
                $html .= '<input value="' . $this->getValue() . '" id="' . $this->getHtmlId() . '_hidden" type="hidden" class="required-entry" />';
                $html .= '<script type="text/javascript">
                    syncOnchangeValue(\'' . $this->getHtmlId() . '\', \'' . $this->getHtmlId() . '_hidden\');
                </script>';
            }
        } else {
            $html .= parent::_getDeleteCheckbox();
        }

        return $html;
    }
}
