<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product in category grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Link_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Group constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('super_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/superGroup', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
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
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $allowProductTypes = [];
        $allowProductTypeNodes = Mage::getConfig()
            ->getNode('global/catalog/product/type/grouped/allow_product_types')->children();
        foreach ($allowProductTypeNodes as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $collection = Mage::getModel('catalog/product_link')->useGroupedLinks()
            ->getProductCollection()
            ->setProduct($this->_getProduct())
            ->addAttributeToSelect('*')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowProductTypes);

        if ($this->getIsReadonly() === true) {
            $collection->addFieldToFilter('entity_id', ['in' => $this->_getSelectedProducts()]);
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
        $this->addColumn('in_products', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
        ]);

        $this->addColumn('entity_id', [
            'header'    => Mage::helper('catalog')->__('ID'),
            'index'     => 'entity_id',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name',
        ]);
        $this->addColumn('sku', [
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku',
        ]);
        $this->addColumn('price', [
            'type'      => 'currency',
            'currency_code' => Mage_Directory_Helper_Data::getConfigCurrencyBase(),
        ]);

        $this->addColumn('qty', [
            'header'    => Mage::helper('catalog')->__('Default Qty'),
            'name'      => 'qty',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'editable'  => true,
            'filter_condition_callback' => [$this, '_addLinkModelFilterCallback'],
        ]);

        $this->addColumn('position', [
            'header'    => Mage::helper('catalog')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getProduct()->getId(),
            'filter_condition_callback' => [$this, '_addLinkModelFilterCallback'],
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => [
                [
                    'caption' => Mage::helper('catalog')->__('Edit'),
                    'id'      => 'editlink',
                    'field'   => 'id',
                    'onclick' => "popWin(this.href,'win','width=1000,height=700,resizable=1,scrollbars=1');return false;",
                    'url'     => [
                        'base'   => 'adminhtml/catalog_product/edit',
                        'params' => [
                            'store' => $this->getRequest()->getParam('store'),
                            'popup' => 1,
                        ],
                    ],
                ],
            ],
            'index' => 'stores',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ?: $this->getUrl('*/*/superGroupGridOnly', ['_current' => true]);
    }

    /**
     * Retrieve selected grouped products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsGrouped();
        if (!is_array($products)) {
            return array_keys($this->getSelectedGroupedProducts());
        }

        return $products;
    }

    /**
     * Retrieve grouped products
     *
     * @return array
     */
    public function getSelectedGroupedProducts()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance(true)
            ->getAssociatedProducts(Mage::registry('current_product'));
        $products = [];
        foreach ($associatedProducts as $product) {
            $products[$product->getId()] = [
                'qty'       => $product->getQty(),
                'position'  => $product->getPosition(),
            ];
        }

        return $products;
    }

    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Associated Products');
    }

    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Associated Products');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
