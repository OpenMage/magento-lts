<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle selection product grid
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method bool getFirstShow()
 * @method string getIndex()
 * @method $this setIndex(string $value)
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('bundle_selection_search_grid');
        $this->setRowClickCallback('bSelection.productGridRowClick.bind(bSelection)');
        $this->setCheckboxCheckCallback('bSelection.productGridCheckboxCheck.bind(bSelection)');
        $this->setRowInitCallback('bSelection.productGridRowInit.bind(bSelection)');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->setId($this->getId() . '_' . $this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName() . '.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName() . '.doFilter()');

        return parent::_beforeToHtml();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStore($this->getStore())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToFilter('type_id', ['in' => $this->getAllowedSelectionTypes()])
            ->addFilterByRequiredOptions()
            ->addStoreFilter()
            ->addAttributeToFilter('status', [
                'in' => Mage::getSingleton('catalog/product_status')->getSaleableStatusIds()
            ]);

        if ($products = $this->_getProducts()) {
            $collection->addIdFilter($this->_getProducts(), true);
        }

        if ($this->getFirstShow()) {
            $collection->addIdFilter('-1');
            $this->setEmptyText($this->__('Please enter search conditions to view products.'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', [
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('sales')->__('Product Name'),
            'index'     => 'name',
            'column_css_class' => 'name'
        ]);

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ]
        );

        $this->addColumn('sku', [
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
            'column_css_class' => 'sku'
        ]);
        $this->addColumn('price', [
            'header'    => Mage::helper('sales')->__('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price'
        ]);

        $this->addColumn('is_selected', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_selected',
            'align'     => 'center',
            'values'    => $this->_getSelectedProducts(),
            'index'     => 'entity_id',
        ]);

        $this->addColumn('qty', [
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty to Add'),
            'name'      => 'qty',
            'inline_css' => 'qty',
            'align'     => 'right',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '130px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/bundle_selection/grid', ['index' => $this->getIndex(), 'productss' => implode(',', $this->_getProducts())]);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getSelectedProducts()
    {
        return $this->getRequest()->getPost('selected_products', []);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getProducts()
    {
        if ($products = $this->getRequest()->getPost('products', null)) {
            return $products;
        } elseif ($productss = $this->getRequest()->getParam('productss', null)) {
            return explode(',', $productss);
        } else {
            return [];
        }
    }

    /**
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Retrieve array of allowed product types for bundle selection product
     *
     * @return array
     */
    public function getAllowedSelectionTypes()
    {
        return Mage::helper('bundle')->getAllowedSelectionTypes();
    }
}
