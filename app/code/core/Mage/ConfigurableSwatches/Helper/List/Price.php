<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing price change for swatches in product listing pages
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Helper_List_Price extends Mage_Core_Helper_Abstract
{
    /**
     * Path to to check is it required to change prices
     */
    public const XML_PATH_SWATCH_PRICE = 'configswatches/general/product_list_price_change';

    protected $_moduleName = 'Mage_ConfigurableSwatches';

    /**
     * Set swatch_price on products where swatch option_id is set
     * Depends on following product data:
     * - product must have children products attached and be configurable by type
     *
     * @param int $storeId
     */
    public function attachConfigurableProductChildrenPricesMapping(array $products, $storeId = null)
    {
        $listSwatchAttrId = Mage::helper('configurableswatches/productlist')->getSwatchAttributeId();
        $result = [];

        foreach ($products as $product) {
            /** @var Mage_Catalog_Model_Product $product */
            if ($product->getTypeId() !== Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE
                && !is_array($product->getChildrenProducts())
            ) {
                continue;
            }

            /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
            $typeInstance = $product->getTypeInstance();
            $allowedAttributes = $typeInstance->getConfigurableAttributeCollection($product);
            foreach ($allowedAttributes as $attribute) {
                /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
                if ($attribute->getAttributeId() !== $listSwatchAttrId) {
                    continue;
                }

                foreach ($attribute->getPrices() as $attributePrice) {
                    $product->setConfigurablePrice(
                        $this->_getHelper()->preparePrice(
                            $product,
                            $attributePrice['pricing_value'],
                            $attributePrice['is_percent'],
                            $storeId,
                        ),
                    );
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        ['product' => $product],
                    );
                    $configurablePrice = $product->getConfigurablePrice();
                    $cofigurableSwatchesHelper = Mage::helper('configurableswatches');
                    $result[$cofigurableSwatchesHelper::normalizeKey($attributePrice['store_label'])] = [
                        'price' => $configurablePrice,
                        'oldPrice' => $this->_getHelper()->prepareOldPrice(
                            $product,
                            $attributePrice['pricing_value'],
                            $attributePrice['is_percent'],
                            $storeId,
                        ),
                    ];
                }
            }
            $product->setSwatchPrices($result);
        }
    }

    /**
     * Get helper for calculation purposes
     *
     * @return Mage_Catalog_Helper_Product_Type_Composite
     */
    protected function _getHelper()
    {
        return Mage::helper('catalog/product_type_composite');
    }

    /**
     * Check if option for swatches price change is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_SWATCH_PRICE);
    }
}
