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
 * @category    
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * TheFind feed product grid container
 *
 * @category    Find
 * @package     Find_Feed
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Find_Feed_Block_Adminhtml_List_Items_Grid  extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid settings
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('find_feed_list_items');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Return Current work store
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Prepare product collection
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->_getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('is_imported');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'            => Mage::helper('find_feed')->__('ID'),
            'sortable'          => true,
            'width'             => '60px',
            'index'             => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'            => Mage::helper('find_feed')->__('Product Name'),
            'index'             => 'name',
            'column_css_class'  => 'name'
        ));

        $this->addColumn('type', array(
            'header'            => Mage::helper('find_feed')->__('Type'),
            'width'             => '60px',
            'index'             => 'type_id',
            'type'              => 'options',
            'options'           => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $entityTypeId =  Mage::helper('find_feed')->getProductEntityType();
        $sets           = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'            => Mage::helper('find_feed')->__('Attrib. Set Name'),
            'width'             => '100px',
            'index'             => 'attribute_set_id',
            'type'              => 'options',
            'options'           => $sets,
        ));

        $this->addColumn('sku', array(
            'header'            => Mage::helper('find_feed')->__('SKU'),
            'width'             => '80px',
            'index'             => 'sku',
            'column_css_class'  => 'sku'
        ));

        $this->addColumn('price', array(
            'header'            => Mage::helper('find_feed')->__('Price'),
            'align'             => 'center',
            'type'              => 'currency',
            'currency_code'     => $this->_getStore()->getCurrentCurrencyCode(),
            'rate'              => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index'             => 'price'
        ));

        $source = Mage::getModel('eav/entity_attribute_source_boolean');
        $isImportedOptions = $source->getOptionArray();

        $this->addColumn('is_imported', array(
            'header'    => Mage::helper('find_feed')->__('In feed'),
            'width'     => '100px',
            'index'     => 'is_imported',
            'type'      => 'options',
            'options'   => $isImportedOptions
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return Find_Feed_Block_Adminhtml_List_Items_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('item_id');

        $this->getMassactionBlock()->addItem('enable', array(
            'label'         => Mage::helper('find_feed')->__('Publish'),
            'url'           => $this->getUrl('*/items_grid/massEnable'),
            'selected'      => true,
        ));
        $this->getMassactionBlock()->addItem('disable', array(
            'label'         => Mage::helper('find_feed')->__('Not publish'),
            'url'           => $this->getUrl('*/items_grid/massDisable'),
        ));

        return $this;
    }

    /**
     * Return Grid URL for AJAX query
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
