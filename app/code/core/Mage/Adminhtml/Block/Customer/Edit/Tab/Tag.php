<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer's tags grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Tag_Model_Resource_Customer_Collection getCollection()
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
        $tagId = Mage::registry('tagId');

        if ($this->getCustomerId() instanceof Mage_Customer_Model_Customer) {
            $this->setCustomerId($this->getCustomerId()->getId());
        }

        $collection = Mage::getResourceModel('tag/customer_collection')
            ->addCustomerFilter($this->getCustomerId())
            ->addGroupByTag();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->addProductName();
        return parent::_afterLoadCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('customer')->__('Tag Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('customer')->__('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'    => [
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('customer')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('customer')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('customer')->__('Approved'),
            ],
            'filter'    => false,
        ]);

        $this->addColumn('product', [
            'header'    => Mage::helper('customer')->__('Product Name'),
            'index'     => 'product',
            'filter'    => false,
            'sortable'  => false,
        ]);

        $this->addColumn('product_sku', [
            'header'    => Mage::helper('customer')->__('SKU'),
            'index'     => 'product_sku',
            'filter'    => false,
            'sortable'  => false,
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/tag/edit', [
            'tag_id' => $row->getTagId(),
            'customer_id' => $this->getCustomerId(),
        ]);
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/customer/tagGrid', [
            '_current' => true,
            'id'       => $this->getCustomerId(),
        ]);
    }
}
