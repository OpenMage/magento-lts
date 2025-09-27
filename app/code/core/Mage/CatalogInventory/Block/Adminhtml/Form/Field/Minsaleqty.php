<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Block_Adminhtml_Form_Field_Minsaleqty extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected $_groupRenderer;

    /**
     * Retrieve group column renderer
     *
     * @return Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            /** @var Mage_CatalogInventory_Block_Adminhtml_Form_Field_Customergroup $block */
            $block = $this->getLayout()->createBlock(
                'cataloginventory/adminhtml_form_field_customergroup',
                '',
                ['is_render_to_js_template' => true],
            );
            $this->_groupRenderer = $block;
            $this->_groupRenderer->setClass('customer_group_select');
            $this->_groupRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('customer_group_id', [
            'label' => Mage::helper('customer')->__('Customer Group'),
            'renderer' => $this->_getGroupRenderer(),
        ]);
        $this->addColumn('min_sale_qty', [
            'label' => Mage::helper('cataloginventory')->__('Minimum Qty'),
            'style' => 'width:100px',
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('cataloginventory')->__('Add Minimum Qty');
    }

    /**
     * Prepare existing row data object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('customer_group_id')),
            'selected="selected"',
        );
    }
}
