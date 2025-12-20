<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Upsell products admin grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Link_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Upsell extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('up_sell_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }

        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retrieve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Add filter
     *
     * @param  Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() === 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->_getProduct()->getUpsellReadonly();
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product_link')->useUpSellLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = [0];
            }

            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->_getProduct()->getUpsellReadonly()) {
            $this->addColumn('in_products', [
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
            ]);
        }

        $this->addColumn('entity_id', [
            'header'    => Mage::helper('catalog')->__('ID'),
            'index'     => 'entity_id',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('type', [
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ]);

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', [
            'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ]);

        $this->addColumn('visibility', [
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku',
        ]);

        $this->addColumn('price', [
            'type'          => 'currency',
            'currency_code' => Mage_Directory_Helper_Data::getConfigCurrencyBase(),
        ]);

        $this->addColumn('position', [
            'header'                    => Mage::helper('catalog')->__('Position'),
            'name'                      => 'position',
            'type'                      => 'number',
            'width'                     => 60,
            'validate_class'            => 'validate-number',
            'index'                     => 'position',
            'editable'                  => !$this->_getProduct()->getUpsellReadonly(),
            'edit_only'                 => !$this->_getProduct()->getId(),
            'filter_condition_callback' => [$this, '_addLinkModelFilterCallback'],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ?: $this->getUrl('*/*/upsellGrid', ['_current' => true]);
    }

    /**
     * Retrieve selected upsell products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsUpsell();
        if (!is_array($products)) {
            return array_keys($this->getSelectedUpsellProducts());
        }

        return $products;
    }

    /**
     * Retrieve upsell products
     *
     * @return array
     */
    public function getSelectedUpsellProducts()
    {
        $products = [];
        foreach (Mage::registry('current_product')->getUpSellProducts() as $product) {
            $products[$product->getId()] = ['position' => $product->getPosition()];
        }

        return $products;
    }
}
