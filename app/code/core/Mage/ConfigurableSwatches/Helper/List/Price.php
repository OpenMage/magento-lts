<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * Class implementing price change for swatches in product listing pages
 *
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
     * @param  Mage_Catalog_Model_Product[]    $products
     * @param  int                             $storeId
     * @return void
     * @throws Mage_Core_Model_Store_Exception
     */
    public function attachConfigurableProductChildrenPricesMapping(array $products, $storeId = null)
    {
        $listSwatchAttrId = Mage::helper('configurableswatches/productlist')->getSwatchAttributeId();
        $result = [];

        foreach ($products as $product) {
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
                    $result[Mage_ConfigurableSwatches_Helper_Data::normalizeKey($attributePrice['store_label'])] = [
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
