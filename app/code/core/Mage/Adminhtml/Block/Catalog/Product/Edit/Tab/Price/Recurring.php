<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Recurring profile attribute edit renderer
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Recurring
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $result = new stdClass;
        $result->output = '';
        Mage::dispatchEvent('catalog_product_edit_form_render_recurring', [
            'result' => $result,
            'product_element' => $this->_element,
            'product'   => Mage::registry('current_product'),
        ]);
        return $result->output;
    }
}
