<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/**
 * Adminhtml catalog inventory "Minimum Qty Allowed in Shopping Cart" field
 *
 * @category   Mage
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
            $this->_groupRenderer = $this->getLayout()->createBlock(
                'cataloginventory/adminhtml_form_field_customergroup',
                '',
                ['is_render_to_js_template' => true],
            );
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
