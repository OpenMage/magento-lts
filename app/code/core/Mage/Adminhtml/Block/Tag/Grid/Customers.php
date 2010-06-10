<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tagginf customers grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Tag_Grid_Customers extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('tag_customer/collection')
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
//            ->addAttributeToSelect('email')
//            ->addAttributeToSelect('created_at')
//            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing')
//            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing')
//            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing')
//            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing')
//            ->joinField('billing_country_name', 'directory/country_name', 'name', 'country_id=billing_country_id', array('language_code'=>'en'))
        ;

        if ($productId = $this->getRequest()->getParam('product_id')) {
            $collection->addProductFilter($productId);
        }
        if ($tagId = $this->getRequest()->getParam('tag_id')) {
            $collection->addTagFilter($tagId);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('tag')->__('ID'),
            'width'     => '40px',
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'entity_id'
        ));
        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('tag')->__('First Name'),
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('tag')->__('Last Name'),
            'index'     =>'lastname'
        ));
//        $this->addColumn('email', array(
//            'header'    =>Mage::helper('tag')->__('Email'),
//            'align'     =>'center',
//            'index'     =>'email'
//        ));
//        $this->addColumn('Telephone', array(
//            'header'    =>Mage::helper('tag')->__('Telephone'),
//            'align'     =>'center',
//            'index'     =>'billing_telephone'
//        ));
//        $this->addColumn('billing_postcode', array(
//            'header'    =>Mage::helper('tag')->__('ZIP/Post Code'),
//            'index'     =>'billing_postcode',
//        ));
//        $this->addColumn('billing_country_name', array(
//            'header'    =>Mage::helper('tag')->__('Country'),
//            #'filter'    => 'adminhtml/customer_grid_filter_country',
//            'index'     =>'billing_country_name',
//        ));
//        $this->addColumn('customer_since', array(
//            'header'    =>Mage::helper('tag')->__('Customer Since'),
//            'type'      => 'date',
//            'align'     => 'center',
//            #'format'    => 'Y.m.d',
//            'index'     =>'created_at',
//        ));
        $this->addColumn('tags', array(
            'header'    => Mage::helper('tag')->__('Tags'),
            'index'     => 'tags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/tag_grid_column_renderer_tags'
        ));
        $this->addColumn('action', array(
            'header'    =>Mage::helper('tag')->__('Action'),
            'align'     =>'center',
            'width'     => '120px',
            'format'    =>'<a href="'.$this->getUrl('*/*/products/customer_id/$entity_id').'">'.Mage::helper('tag')->__('View Products').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));

        $this->setColumnFilter('entity_id')
            ->setColumnFilter('email')
            ->setColumnFilter('firstname')
            ->setColumnFilter('lastname');

//        $this->addExportType('*/*/exportCsv', Mage::helper('tag')->__('CSV'));
//        $this->addExportType('*/*/exportXml', Mage::helper('tag')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            $this->getCollection()->addAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        }
        return $this;
    }

}
