<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml additional helper block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Config extends Varien_Data_Form_Element_Select
{
    /**
     * Retrieve element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $value = $this->getValue();
        if ($value == '') {
            $this->setValue($this->_getValueFromConfig());
        }
        $html = parent::getElementHtml();

        $htmlId   = 'use_config_' . $this->getHtmlId();
        $checked  = ($value == '') ? ' checked="checked"' : '';
        $disabled = ($this->getReadonly()) ? ' disabled="disabled"' : '';

        $html .= '<input id="' . $htmlId . '" name="product[' . $htmlId . ']" ' . $disabled . ' value="1" ' . $checked;
        $html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';
        $html .= ' <label for="' . $htmlId . '">' . Mage::helper('adminhtml')->__('Use Config Settings') . '</label>';
        $html .= '<script type="text/javascript">toggleValueElements($(\'' . $htmlId . '\'), $(\'' . $htmlId . '\').parentNode);</script>';

        return $html;
    }

    /**
     * Get config value data
     *
     * @return mixed
     */
    protected function _getValueFromConfig()
    {
        return '';
    }
}
