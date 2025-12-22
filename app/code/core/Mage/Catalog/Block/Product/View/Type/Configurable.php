<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog super product configurable part block
 *
 * @package    Mage_Catalog
 *
 * @method bool hasAllowProducts()
 */
class Mage_Catalog_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * Prices
     * @deprecated
     * @var array
     */
    protected $_prices      = [];

    /**
     * Prepared prices
     * @deprecated
     * @var array
     */
    protected $_resPrices   = [];

    /**
     * Get helper for calculation purposes
     *
     * @return Mage_Catalog_Helper_Product_Type_Composite
     */
    protected function _getHelper()
    {
        return $this->helper('catalog/product_type_composite');
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->getConfigurableAttributes($this->getProduct());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions()
    {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $this->getProduct()->getTypeInstance(true);
            $allProducts = $productType->getUsedProducts(null, $this->getProduct());
            foreach ($allProducts as $product) {
                if ($product->isSaleable()
                    || $skipSaleableCheck
                    || (!$product->getStockItem()->getIsInStock()
                        && Mage::helper('cataloginventory')->isShowOutOfStock())
                ) {
                    $products[] = $product;
                }
            }

            $this->setAllowProducts($products);
        }

        return $this->getData('allow_products');
    }

    /**
     * retrieve current store
     *
     * @return Mage_Core_Model_Store
     * @deprecated
     */
    public function getCurrentStore()
    {
        return $this->_getHelper()->getCurrentStore();
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        return [];
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $attributes = [];
        $options    = [];
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentProduct = $this->getProduct();

        $preconfiguredFlag = $currentProduct->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentProduct->getPreconfiguredValues();
            $defaultValues       = [];
        }

        $productStock = [];
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();
            $productStock[$productId] = $product->getStockItem()->getIsInStock();
            foreach ($this->getAllowAttributes() as $attribute) {
                $productAttribute   = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue     = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = [];
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = [];
                }

                $options[$productAttributeId][$attributeValue][] = $productId;
            }
        }

        $this->_resPrices = [
            $this->_preparePrice($currentProduct->getFinalPrice()),
        ];

        foreach ($this->getAllowAttributes() as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeId = $productAttribute->getId();
            $info = [
                'id'        => $productAttribute->getId(),
                'code'      => $productAttribute->getAttributeCode(),
                'label'     => $attribute->getLabel(),
                'options'   => [],
            ];

            $optionPrices = [];
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }

                    $currentProduct->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent']),
                    );
                    $currentProduct->setParentId(true);
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        ['product' => $currentProduct],
                    );
                    $configurablePrice = $currentProduct->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $productsIndexOptions = $options[$attributeId][$value['value_index']];
                        $productsIndex = [];
                        foreach ($productsIndexOptions as $productIndex) {
                            if ($productStock[$productIndex]) {
                                $productsIndex[] = $productIndex;
                            }
                        }
                    } else {
                        $productsIndex = [];
                    }

                    $info['options'][] = [
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
                        'products'  => $productsIndex,
                    ];
                    $optionPrices[] = $configurablePrice;
                }
            }

            if ($this->_validateAttributeInfo($info)) {
                $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getDefaultRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());

        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setProductClassId($currentProduct->getTaxClassId());

        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = [
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('catalog')->__('Incl. Tax'),
        ];

        $config = [
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentProduct->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentProduct->getPrice())),
            'productId'         => $currentProduct->getId(),
            'chooseText'        => Mage::helper('catalog')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig,
        ];

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Validating of super product option value
     *
     * @param  string $attributeId
     * @param  array  $value
     * @param  array  $options
     * @return bool
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if (isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of super product option
     *
     * @param  array $info
     * @return bool
     */
    protected function _validateAttributeInfo(&$info)
    {
        if (count($info['options']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Calculation real price
     *
     * @param  float $price
     * @param  bool  $isPercent
     * @return mixed
     * @deprecated
     */
    protected function _preparePrice($price, $isPercent = false)
    {
        return $this->_getHelper()->preparePrice($this->getProduct(), $price, $isPercent);
    }

    /**
     * Calculation price before special price
     *
     * @param  float $price
     * @param  bool  $isPercent
     * @return mixed
     * @deprecated
     */
    protected function _prepareOldPrice($price, $isPercent = false)
    {
        return $this->_getHelper()->prepareOldPrice($this->getProduct(), $price, $isPercent);
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param  float  $price
     * @return string
     * @deprecated
     */
    protected function _registerJsPrice($price)
    {
        return $this->_getHelper()->registerJsPrice($price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param  float $price
     * @param  bool  $round
     * @return float
     * @deprecated
     */
    protected function _convertPrice($price, $round = false)
    {
        return $this->_getHelper()->convertPrice($price, $round);
    }
}
