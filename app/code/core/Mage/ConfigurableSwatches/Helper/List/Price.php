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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class implementing price change for swatches in product listing pages
 */
class Mage_ConfigurableSwatches_Helper_List_Price extends Mage_Core_Helper_Abstract
{
    /**
     * Path to to check is it required to change prices
     */
    const XML_PATH_SWATCH_PRICE = 'configswatches/general/product_list_price_change';

    /**
     * Set swatch_price on products where swatch option_id is set
     * Depends on following product data:
     * - product must have children products attached and be configurable by type
     *
     * @param array $products
     * @param int $storeId
     * @return void
     */
    public function attachConfigurableProductChildrenPricesMapping(array $products, $storeId = null)
    {
        $listSwatchAttrId = Mage::helper('configurableswatches/productlist')->getSwatchAttributeId();
        $result = array();

       foreach ($products as $product) {
           /** @var $product Mage_Catalog_Model_Product */
           if ($product->getTypeId() !== Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE
               && !is_array($product->getChildrenProducts())
           ) {
               continue;
           }

           /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
           $typeInstance = $product->getTypeInstance();
           $allowedAttributes = $typeInstance->getConfigurableAttributeCollection($product);
           foreach ($allowedAttributes as $attribute) {
               /** @var $attribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
               if ($attribute->getAttributeId() !== $listSwatchAttrId) {
                   continue;
               }

               foreach ($attribute->getPrices() as $attributePrice) {
                   $product->setConfigurablePrice(
                       $this->_getHelper()->preparePrice(
                           $product,
                           $attributePrice['pricing_value'],
                           $attributePrice['is_percent'],
                           $storeId
                       )
                   );
                   Mage::dispatchEvent(
                       'catalog_product_type_configurable_price',
                       array('product' => $product)
                   );
                   $configurablePrice = $product->getConfigurablePrice();
                   $cofigurableSwatchesHelper = Mage::helper('configurableswatches');
                   $result[$cofigurableSwatchesHelper::normalizeKey($attributePrice['store_label'])] = array(
                       'price' => $configurablePrice,
                       'oldPrice' => $this->_getHelper()->prepareOldPrice(
                               $product,
                               $attributePrice['pricing_value'],
                               $attributePrice['is_percent'],
                               $storeId
                           ),
                   );
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
