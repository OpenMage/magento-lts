<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog super product configurable tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setProductId($this->getRequest()->getParam('id'));
        $this->setTemplate('catalog/product/edit/super/config.phtml');
        $this->setId('config_super_product');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Retrieve Tab class (for loading)
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Check block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return (bool) $this->_getProduct()->getCompositeReadonly();
    }

    /**
     * Check whether attributes of configurable products can be editable
     *
     * @return bool
     */
    public function isAttributesConfigurationReadonly()
    {
        return (bool) $this->_getProduct()->getAttributesConfigurationReadonly();
    }

    /**
     * Check whether prices of configurable products can be editable
     *
     * @return bool
     */
    public function isAttributesPricesReadonly()
    {
        return $this->_getProduct()->getAttributesConfigurationReadonly() ||
            (Mage::helper('catalog')->isPriceGlobal() && $this->isReadonly());
    }

    /**
     * Prepare Layout data
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'adminhtml/catalog_product_edit_tab_super_config_grid',
                'admin.product.edit.tab.super.config.grid',
            ),
        );

        $this->setChild(
            'create_empty',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('catalog')->__('Create Empty'),
                    'class' => 'add',
                    'onclick' => 'superProduct.createEmptyProduct()',
                ]),
        );

        if ($this->_getProduct()->getId()) {
            $this->setChild(
                'simple',
                $this->getLayout()->createBlock(
                    'adminhtml/catalog_product_edit_tab_super_config_simple',
                    'catalog.product.edit.tab.super.config.simple',
                ),
            );

            $this->setChild(
                'create_from_configurable',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label' => Mage::helper('catalog')->__('Copy From Configurable'),
                        'class' => 'add',
                        'onclick' => 'superProduct.createNewProduct()',
                    ]),
            );
        }

        return parent::_prepareLayout();
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
     * Retrieve attributes data in JSON format
     *
     * @return string
     */
    public function getAttributesJson()
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        $attributes = $productType->getConfigurableAttributesAsArray($this->_getProduct());
        if (!$attributes) {
            return '[]';
        } else {
            // Hide price if needed
            foreach ($attributes as &$attribute) {
                $attribute['label'] = $this->escapeHtml($attribute['label']);
                $attribute['frontend_label'] = $this->escapeHtml($attribute['frontend_label']);
                $attribute['store_label'] = $this->escapeHtml($attribute['store_label']);
                if (isset($attribute['values']) && is_array($attribute['values'])) {
                    foreach ($attribute['values'] as &$attributeValue) {
                        if (!$this->getCanReadPrice()) {
                            $attributeValue['pricing_value'] = '';
                            $attributeValue['is_percent'] = 0;
                        }
                        $attributeValue['can_edit_price'] = $this->getCanEditPrice();
                        $attributeValue['can_read_price'] = $this->getCanReadPrice();
                    }
                }
            }
        }
        return Mage::helper('core')->jsonEncode($attributes);
    }

    /**
     * Retrieve Links in JSON format
     *
     * @return string
     */
    public function getLinksJson()
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        $products = $productType->getUsedProducts(null, $this->_getProduct());
        if (!$products) {
            return '{}';
        }
        $data = [];
        foreach ($products as $product) {
            $data[$product->getId()] = $this->getConfigurableSettings($product);
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Retrieve configurable settings
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getConfigurableSettings($product)
    {
        $data = [];
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->_getProduct()->getTypeInstance(true);
        $attributes = $productType->getUsedProductAttributes($this->_getProduct());
        foreach ($attributes as $attribute) {
            $data[] = [
                'attribute_id' => $attribute->getId(),
                'label'        => $product->getAttributeText($attribute->getAttributeCode()),
                'value_index'  => $product->getData($attribute->getAttributeCode()),
            ];
        }

        return $data;
    }

    /**
     * Retrieve Grid child HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Retrieve Grid JavaScript object name
     *
     * @return string
     */
    public function getGridJsObject()
    {
        return $this->getChild('grid')->getJsObjectName();
    }

    /**
     * Retrieve Create New Empty Product URL
     *
     * @return string
     */
    public function getNewEmptyProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            [
                'set'      => $this->_getProduct()->getAttributeSetId(),
                'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
            ],
        );
    }

    /**
     * Retrieve Create New Product URL
     *
     * @return string
     */
    public function getNewProductUrl()
    {
        return $this->getUrl(
            '*/*/new',
            [
                'set'      => $this->_getProduct()->getAttributeSetId(),
                'type'     => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId(),
            ],
        );
    }

    /**
     * Retrieve Quick create product URL
     *
     * @return string
     */
    public function getQuickCreationUrl()
    {
        return $this->getUrl(
            '*/*/quickCreate',
            [
                'product'  => $this->_getProduct()->getId(),
            ],
        );
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
        $configurableAttributes = $productType->getConfigurableAttributes($this->_getProduct());
        foreach ($configurableAttributes as $attribute) {
            $attributesIds[] = $attribute->getProductAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    /**
     * Escape JavaScript string
     *
     * @param string $string
     * @return string
     */
    public function escapeJs($string)
    {
        return addcslashes($string, "'\r\n\\");
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Associated Products');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Associated Products');
    }

    /**
     * Can show tab flag
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check is a hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Show "Use default price" checkbox
     *
     * @return bool
     */
    public function getShowUseDefaultPrice()
    {
        return !Mage::helper('catalog')->isPriceGlobal()
            && $this->_getProduct()->getStoreId();
    }
}
