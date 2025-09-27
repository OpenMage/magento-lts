<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tagged products grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Grid_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
        ;
        if ($tagId = $this->getRequest()->getParam('tag_id')) {
            $collection->addTagFilter($tagId);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $collection->addCustomerFilter($customerId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header'    => Mage::helper('tag')->__('ID'),
            'align'     => 'center',
            'width'     => '60px',
            'sortable'  => false,
            'index'     => 'product_id',
        ]);
        $this->addColumn('sku', [
            'header'    => Mage::helper('tag')->__('SKU'),
            'align'     => 'center',
            'index'     => 'sku',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('tag')->__('Name'),
            'index'     => 'name',
        ]);
        $this->addColumn('tags', [
            'header'    => Mage::helper('tag')->__('Tags'),
            'index'     => 'tags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/tag_grid_column_renderer_tags',
        ]);
        $this->addColumn('action', [
            'type'      => 'action',
            'align'     => 'center',
            'width'     => '120',
            'format'    => '<a href="' . $this->getUrl('*/*/customers/product_id/$product_id') . '">' . Mage::helper('tag')->__('View Customers') . '</a>',
            'is_system' => true,
        ]);

        return parent::_prepareColumns();
    }
}
