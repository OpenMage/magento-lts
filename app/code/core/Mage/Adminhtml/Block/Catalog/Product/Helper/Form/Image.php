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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product form image field helper
 *
 * @category   Mage
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
