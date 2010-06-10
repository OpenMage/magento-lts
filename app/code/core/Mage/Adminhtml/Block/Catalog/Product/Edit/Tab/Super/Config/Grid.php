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
 * Adminhtml super product links grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('super_product_links');

        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }

            $createdProducts = $this->_getCreatedProducts();

            $existsProducts = $productIds; // Only for "Yes" Filter we will add created products

            if(count($createdProducts)>0) {
                if(!is_array($existsProducts)) {
                    $existsProducts = $createdProducts;
                } else {
                    $existsProducts = array_merge($createdProducts);
                }
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$existsProducts));
            }
            else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getCreatedProducts()
    {
        $products = $this->getRequest()->getPost('new_products', null);
        if (!is_array($products)) {
            $products = array();
        }

        return $products;
    }

    protected function _prepareCollection()
    {
        $allowProductTypes = array();
        foreach (Mage::getConfig()->getNode('global/catalog/product/type/configurable/allow_product_types')->children() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions();

        Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);

        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('nin'=>array(null)));
        }

        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }

        parent::_prepareCollection();
        return $this;
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
        if (!is_array($products)) {
            $products = $this->_getProduct()->getTypeInstance(true)->getUsedProductIds($this->_getProduct());
        }
        return $products;
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
         return $this->_getProduct()->getCompositeReadonly();
    }

    protected function _prepareColumns()
    {
        $product = $this->_getProduct();
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_checkbox',
                'attributes' => $attributes
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));


        $sets = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter($this->_getProduct()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('is_saleable', array(
            'header'    => Mage::helper('catalog')->__('Inventory'),
            'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_inventory',
            'filter'    => 'adminhtml/catalog_product_edit_tab_super_config_grid_filter_inventory',
            'index'     => 'is_saleable'
        ));

        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $productAttribute->getSource();
            $this->addColumn($productAttribute->getAttributeCode(), array(
                'header'    => $productAttribute->getFrontend()->getLabel(),
                'index'     => $productAttribute->getAttributeCode(),
                'type'      => $productAttribute->getSourceModel() ? 'options' : 'number',
                'options'   => $productAttribute->getSourceModel() ? $this->getOptions($attribute) : ''
            ));
        }

         $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => $this->getEditParamsForAssociated(),
                        'field'   => 'id',
                        'onclick'  => 'superProduct.createPopup(this.href);return false;'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getEditParamsForAssociated()
    {
        return array(
            'base'      =>  '*/*/edit',
            'params'    =>  array(
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId()
            )
        );
    }

    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        foreach ($this->_getProduct()->getTypeInstance(true)->getConfigurableAttributes($this->_getProduct()) as $attribute) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    public function getOptions($attribute) {
        $result = array();
        foreach ($attribute->getProductAttribute()->getSource()->getAllOptions() as $option) {
            if($option['value']!='') {
                $result[$option['value']] = $option['label'];
            }
        }

        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/superConfig', array('_current'=>true));
    }

}
