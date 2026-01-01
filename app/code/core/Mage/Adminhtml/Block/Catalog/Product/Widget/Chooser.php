<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Product Chooser for "Product Link" Cms Widget Plugin
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_selectedProducts = [];

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments = [])
    {
        parent::__construct($arguments);
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param  Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/catalog_product_widget/chooser', [
            'uniq_id' => $uniqId,
            'use_massaction' => false,
        ]);

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $productId = false;
            if (isset($value[0], $value[1]) && $value[0] === 'product') {
                $productId = $value[1];
            }

            $categoryId = $value[2] ?? false;
            $label = '';
            if ($categoryId) {
                $label = Mage::getResourceSingleton('catalog/category')
                    ->getAttributeRawValue($categoryId, 'name', Mage::app()->getStore()) . '/';
            }

            if ($productId) {
                $label .= Mage::getResourceSingleton('catalog/product')
                    ->getAttributeRawValue($productId, 'name', Mage::app()->getStore());
            }

            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, element) {
                $(grid.containerId).fire('product:changed', {element: element});
            }";
        }

        return '';
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var productId = trElement.down("td").innerHTML;
                    var productName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = productName;
                    var optionValue = "product/" + productId.replace(/^\s+|\s+$/g,"");
                    if (grid.categoryId) {
                        optionValue += "/" + grid.categoryId;
                    }
                    if (grid.categoryName) {
                        optionLabel = grid.categoryName + " / " + optionLabel;
                    }
                    ' . $chooserJsObject . '.setElementValue(optionValue);
                    ' . $chooserJsObject . '.setElementLabel(optionLabel);
                    ' . $chooserJsObject . '.close();
                }
            ';
        }

        return '';
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getCategoryClickListenerJs()
    {
        $js = '
            function (node, e) {
                {jsObject}.addVarToUrl("category_id", node.attributes.id);
                {jsObject}.reload({jsObject}.url);
                {jsObject}.categoryId = node.attributes.id != "none" ? node.attributes.id : false;
                {jsObject}.categoryName = node.attributes.id != "none" ? node.text : false;
            }
        ';
        return str_replace('{jsObject}', $this->getJsObjectName(), $js);
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param  Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_products') {
            $selected = $this->getSelectedProducts();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare products collection, defined collection filters (category, product type)
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(0)
            ->addAttributeToSelect('name');

        if ($categoryId = $this->getCategoryId()) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category->getId()) {
                // $collection->addCategoryFilter($category);
                $productIds = $category->getProductsPosition();
                $productIds = array_keys($productIds);
                if (empty($productIds)) {
                    $productIds = 0;
                }

                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
        }

        if ($productTypeId = $this->getProductTypeId()) {
            $collection->addAttributeToFilter('type_id', $productTypeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for products grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn('in_products', [
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_products',
                'values'    => $this->getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'use_index' => true,
            ]);
        }

        $this->addColumn('entity_id', [
            'header'    => Mage::helper('catalog')->__('ID'),
            'index'     => 'entity_id',
        ]);
        $this->addColumn('chooser_sku', [
            'header'    => Mage::helper('catalog')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku',
        ]);
        $this->addColumn('chooser_name', [
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'name'      => 'chooser_name',
            'index'     => 'name',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/catalog_product_widget/chooser', [
            'products_grid' => true,
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
            'product_type_id' => $this->getProductTypeId(),
        ]);
    }

    /**
     * Setter
     *
     * @param  array $selectedProducts
     * @return $this
     */
    public function setSelectedProducts($selectedProducts)
    {
        $this->_selectedProducts = $selectedProducts;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        if ($selectedProducts = $this->getRequest()->getParam('selected_products', null)) {
            $this->setSelectedProducts($selectedProducts);
        }

        return $this->_selectedProducts;
    }
}
