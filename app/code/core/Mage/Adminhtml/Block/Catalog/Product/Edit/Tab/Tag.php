<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Products' tags grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tag/tag')
            ->getResourceCollection()
            ->addProductFilter($this->getProductId())
            ->addPopularity();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('catalog')->__('Tag Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('popularity', [
            'header'        => Mage::helper('catalog')->__('# of Use'),
            'width'         => '50px',
            'index'         => 'popularity',
            'type'          => 'number',
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => [
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('catalog')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('catalog')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('catalog')->__('Approved'),
            ],
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/tag/edit', [
            'tag_id'        => $row->getId(),
            'product_id'    => $this->getProductId(),
        ]);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/tagGrid', [
            '_current'      => true,
            'id'            => $this->getProductId(),
            'product_id'    => $this->getProductId(),
        ]);
    }
}
