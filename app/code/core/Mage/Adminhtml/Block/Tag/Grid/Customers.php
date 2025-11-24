<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tagginf customers grid block
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Tag_Model_Resource_Customer_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tag_Grid_Customers extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('tag_customer/collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('middlename')
            ->addAttributeToSelect('lastname');

        if ($productId = $this->getRequest()->getParam('product_id')) {
            $collection->addProductFilter($productId);
        }

        if ($tagId = $this->getRequest()->getParam('tag_id')) {
            $collection->addTagFilter($tagId);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'   => Mage::helper('tag')->__('ID'),
            'align'    => 'center',
            'index'    => 'entity_id',
        ]);
        $this->addColumn('firstname', [
            'header' => Mage::helper('tag')->__('First Name'),
            'index'  => 'firstname',
        ]);
        $this->addColumn('middlename', [
            'header' => Mage::helper('tag')->__('Middle Name'),
            'index'  => 'middlename',
        ]);
        $this->addColumn('lastname', [
            'header' => Mage::helper('tag')->__('Last Name'),
            'index'  => 'lastname',
        ]);
        $this->addColumn('tags', [
            'header'   => Mage::helper('tag')->__('Tags'),
            'index'    => 'tags',
            'sortable' => false,
            'filter'   => false,
            'renderer' => 'adminhtml/tag_grid_column_renderer_tags',
        ]);
        $this->addColumn('action', [
            'type'      => 'action',
            'align'     => 'center',
            'width'     => '120',
            'format'    => '<a href="' . $this->getUrl('*/*/products/customer_id/$entity_id') . '">' . Mage::helper('tag')->__('View Products') . '</a>',
            'is_system' => true,
        ]);

        $this->setColumnFilter('entity_id')
            ->setColumnFilter('email')
            ->setColumnFilter('firstname')
            ->setColumnFilter('middlename')
            ->setColumnFilter('lastname');

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }

        return $this;
    }
}
