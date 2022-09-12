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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tagginf customers grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'width'    => '40px',
            'align'    => 'center',
            'sortable' => true,
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
            'header'    => Mage::helper('tag')->__('Action'),
            'align'     => 'center',
            'width'     => '120px',
            'format'    => '<a href="' . $this->getUrl('*/*/products/customer_id/$entity_id') . '">' . Mage::helper('tag')->__('View Products') . '</a>',
            'filter'    => false,
            'sortable'  => false,
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
