<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales create order product search grid product name column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render product name to add Configure link
     *
     * @param  Mage_Catalog_Model_Product $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $rendered       =  parent::render($row);
        $isConfigurable = $row->canConfigure();
        $style          = $isConfigurable ? '' : 'style="color: #CCC;"';
        $prodAttributes = $isConfigurable ? sprintf('list_type = "product_to_add" product_id = %s', $row->getId()) : 'disabled="disabled"';
        return sprintf(
            '<a href="javascript:void(0)" %s class="f-right" %s>%s</a>',
            $style,
            $prodAttributes,
            Mage::helper('sales')->__('Configure'),
        ) . $rendered;
    }
}
