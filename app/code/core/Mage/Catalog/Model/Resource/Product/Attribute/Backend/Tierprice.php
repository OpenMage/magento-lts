<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product tier price backend attribute model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Resource_Product_Attribute_Backend_Groupprice_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_attribute_tier_price', 'value_id');
    }

    /**
     * Add qty column
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        $columns = parent::_loadPriceDataColumns($columns);
        $columns['price_qty'] = 'qty';
        return $columns;
    }

    /**
     * Order by qty
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _loadPriceDataSelect($select)
    {
        $select->order('qty');
        return $select;
    }

    /**
     * Load product tier prices
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return array
     * @deprecated since 1.3.2.3
     */
    public function loadProductPrices($product, $attribute)
    {
        $websiteId = null;
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } elseif ($product->getStoreId()) {
            $websiteId = Mage::app()->getStore($product->getStoreId())->getWebsiteId();
        }

        return $this->loadPriceData($product->getId(), $websiteId);
    }

    /**
     * Delete product tier price data from storage
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return $this
     * @deprecated since 1.3.2.3
     */
    public function deleteProductPrices($product, $attribute)
    {
        $websiteId = null;
        if (!$attribute->isScopeGlobal()) {
            $storeId = $product->getProductId();
            if ($storeId) {
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            }
        }

        $this->deletePriceData($product->getId(), $websiteId);

        return $this;
    }

    /**
     * Insert product Tier Price to storage
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     * @return $this
     * @deprecated since 1.3.2.3
     */
    public function insertProductPrice($product, $data)
    {
        $priceObject = new Varien_Object($data);
        $priceObject->setEntityId($product->getId());

        return $this->savePriceData($priceObject);
    }
}
