<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * List of customers tagged a product
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_customers_grid');
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if ($this->isModuleEnabled('Mage_Tag', 'catalog')) {
            $collection = Mage::getModel('tag/tag')
                ->getCustomerCollection()
                ->addProductFilter($this->getProductId())
                ->addGroupByTag()
                ->addDescOrder();

            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', [
            'header'    => Mage::helper('catalog')->__('First Name'),
            'index'     => 'firstname',
        ]);

        $this->addColumn('middlename', [
            'header'        => Mage::helper('catalog')->__('Middle Name'),
            'index'         => 'middlename',
        ]);

        $this->addColumn('lastname', [
            'header'        => Mage::helper('catalog')->__('Last Name'),
            'index'         => 'lastname',
        ]);

        $this->addColumn('email', [
            'header'        => Mage::helper('catalog')->__('Email'),
            'index'         => 'email',
        ]);

        $this->addColumn('name', [
            'header'        => Mage::helper('catalog')->__('Tag Name'),
            'index'         => 'name',
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', ['id' => $row->getEntityId()]);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product/tagCustomerGrid', [
            '_current' => true,
            'id'       => $this->getProductId(),
            'product_id' => $this->getProductId(),
        ]);
    }
}
