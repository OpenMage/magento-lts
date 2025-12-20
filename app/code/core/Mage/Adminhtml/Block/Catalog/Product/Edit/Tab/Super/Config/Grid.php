<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml super product links grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Config attribute codes
     *
     * @var null|array
     */
    protected $_configAttributeCodes = null;

    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('super_product_links');

        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
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

    /**
     * @param  Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     * @throws Exception
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() === 'in_products') {
            $productIds = $this->_getSelectedProducts();

            if (empty($productIds)) {
                $productIds = 0;
            }

            $createdProducts = $this->_getCreatedProducts();

            $existsProducts = $productIds; // Only for "Yes" Filter we will add created products

            if (count($createdProducts) > 0) {
                if (!is_array($existsProducts)) {
                    $existsProducts = $createdProducts;
                } else {
                    $existsProducts = array_merge($createdProducts);
                }
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $existsProducts]);
            } elseif ($productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getCreatedProducts()
    {
        $products = $this->getRequest()->getPost('new_products', null);
        if (!is_array($products)) {
            return [];
        }

        return $products;
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = [];
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id', $product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

        if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
            Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
        }

        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $product->getTypeInstance(true);

        foreach ($productType->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), ['notnull' => 1]);
        }

        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', ['in' => $this->_getSelectedProducts()]);
        }

        parent::_prepareCollection();
        return $this;
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
        if (!is_array($products)) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $this->_getProduct()->getTypeInstance(true);
            $products = $productType->getUsedProductIds($this->_getProduct());
        }

        return $products;
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        if ($this->hasData('is_readonly')) {
            return $this->getData('is_readonly');
        }

        return $this->_getProduct()->getCompositeReadonly();
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareColumns()
    {
        $product = $this->_getProduct();
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $product->getTypeInstance(true);
        $attributes = $productType->getConfigurableAttributes($product);

        if (!$this->isReadonly()) {
            $this->addColumn('in_products', [
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_checkbox',
                'attributes' => $attributes,
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

        $sets = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter($this->_getProduct()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ],
        );

        $this->addColumn('sku', [
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
        ]);

        $this->addColumn('price', [
            'type'      => 'currency',
            'currency_code' => Mage_Directory_Helper_Data::getConfigCurrencyBase(),
        ]);

        $this->addColumn('is_saleable', [
            'header'    => Mage::helper('catalog')->__('Inventory'),
            'renderer'  => 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_inventory',
            'filter'    => 'adminhtml/catalog_product_edit_tab_super_config_grid_filter_inventory',
            'index'     => 'is_saleable',
        ]);

        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $productAttribute->getSource();
            $this->addColumn($productAttribute->getAttributeCode(), [
                'header'    => $productAttribute->getFrontend()->getLabel(),
                'index'     => $productAttribute->getAttributeCode(),
                'type'      => $productAttribute->getSourceModel() ? 'options' : 'number',
                'options'   => $productAttribute->getSourceModel() ? $this->getOptions($attribute) : '',
            ]);
        }

        $this->addColumn(
            'action',
            [
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => $this->getEditParamsForAssociated(),
                        'field'   => 'id',
                        'onclick'  => 'superProduct.createPopup(this.href);return false;',
                    ],
                ],
            ],
        );

        return parent::_prepareColumns();
    }

    /**
     * @return array
     */
    public function getEditParamsForAssociated()
    {
        return [
            'base'      =>  '*/*/edit',
            'params'    =>  [
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId(),
            ],
        ];
    }

    /**
     * Retrieve Required attributes Ids (comma separated)
     *
     * @return string
     */
    protected function _getRequiredAttributesIds()
    {
        $attributesIds = [];
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        foreach ($productType->getConfigurableAttributes($this->_getProduct()) as $attribute) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    public function getOptions($attribute)
    {
        $result = [];
        foreach ($attribute->getProductAttribute()->getSource()->getAllOptions() as $option) {
            if ($option['value'] != '') {
                $result[$option['value']] = $option['label'];
            }
        }

        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/superConfig', ['_current' => true]);
    }

    /**
     * Retrieving configurable attributes
     *
     * @return array
     */
    protected function _getConfigAttributeCodes()
    {
        if (is_null($this->_configAttributeCodes)) {
            $product = $this->_getProduct();
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $product->getTypeInstance(true);
            $attributes = $productType->getConfigurableAttributes($product);
            $attributeCodes = [];
            foreach ($attributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeCodes[] = $productAttribute->getAttributeCode();
            }

            $this->_configAttributeCodes = $attributeCodes;
        }

        return $this->_configAttributeCodes;
    }

    /**
     * Retrieve item row configurable attribute data
     *
     * @return array
     */
    protected function _retrieveRowData(Varien_Object $item)
    {
        $attributeValues = [];
        foreach ($this->_getConfigAttributeCodes() as $attributeCode) {
            $data = $item->getData($attributeCode);
            if ($data) {
                $attributeValues[$attributeCode] = $data;
            }
        }

        return $attributeValues;
    }

    /**
     * Checking the data contains the same value of data after collection
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();

        $attributeCodes = $this->_getConfigAttributeCodes();
        if (!$attributeCodes) {
            return $this;
        }

        $disableMultiSelect = false;
        $ids = [];
        foreach ($this->_collection as $item) {
            $ids[] = $item->getId();
            $needleAttributeValues = $this->_retrieveRowData($item);
            foreach ($this->_collection as $item2) {
                // Skip the data if already checked
                if (in_array($item2->getId(), $ids)) {
                    continue;
                }

                $attributeValues = $this->_retrieveRowData($item2);
                $disableMultiSelect = ($needleAttributeValues == $attributeValues);
                if ($disableMultiSelect) {
                    break;
                }
            }

            if ($disableMultiSelect) {
                break;
            }
        }

        // Disable multiselect column
        if ($disableMultiSelect) {
            $selectAll = $this->getColumn('in_products');
            if ($selectAll) {
                $selectAll->setDisabled(true);
            }
        }

        return $this;
    }
}
