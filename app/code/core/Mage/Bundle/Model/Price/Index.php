<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Product Price Index
 *
 * @package    Mage_Bundle
 *
 * @method Mage_Bundle_Model_Resource_Price_Index _getResource()
 * @method int getCustomerGroupId()
 * @method float getMaxPrice()
 * @method float getMinPrice()
 * @method Mage_Bundle_Model_Resource_Price_Index getResource()
 * @method int getWebsiteId()
 * @method $this setCustomerGroupId(int $value)
 * @method $this setEntityId(int $value)
 * @method $this setMaxPrice(float $value)
 * @method $this setMinPrice(float $value)
 * @method $this setWebsiteId(int $value)
 */
class Mage_Bundle_Model_Price_Index extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('bundle/price_index');
    }

    /**
     * Reindex Product price
     *
     * @param int $productId
     * @param int $priceType
     * @return $this
     */
    protected function _reindexProduct($productId, $priceType)
    {
        $this->_getResource()->reindexProduct($productId, $priceType);
        return $this;
    }

    /**
     * Reindex Bundle product Price Index
     *
     * @param array|int|Mage_Catalog_Model_Product|Mage_Catalog_Model_Product_Condition_Interface $products
     * @return $this
     */
    public function reindex($products = null)
    {
        $this->_getResource()->reindex($products);
        return $this;
    }

    /**
     * Add bundle price range index to Product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function addPriceIndexToCollection($collection)
    {
        $productObjects = [];
        $productIds     = [];
        foreach ($collection->getItems() as $product) {
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $productIds[] = $product->getEntityId();
                $productObjects[$product->getEntityId()] = $product;
            }
        }

        $websiteId  = Mage::app()->getStore($collection->getStoreId())
            ->getWebsiteId();
        $groupId    = Mage::getSingleton('customer/session')
            ->getCustomerGroupId();

        $addOptionsToResult = false;
        $prices = $this->_getResource()->loadPriceIndex($productIds, $websiteId, $groupId);
        foreach ($productIds as $productId) {
            if (isset($prices[$productId])) {
                $productObjects[$productId]
                    ->setData('_price_index', true)
                    ->setData('_price_index_min_price', $prices[$productId]['min_price'])
                    ->setData('_price_index_max_price', $prices[$productId]['max_price']);
            } else {
                $addOptionsToResult = true;
            }
        }

        if ($addOptionsToResult) {
            $collection->addOptionsToResult();
        }

        return $this;
    }

    /**
     * Add price index to bundle product after load
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function addPriceIndexToProduct($product)
    {
        $websiteId  = $product->getStore()->getWebsiteId();
        $groupId    = Mage::getSingleton('customer/session')
            ->getCustomerGroupId();
        $prices = $this->_getResource()
            ->loadPriceIndex($product->getId(), $websiteId, $groupId);
        if (isset($prices[$product->getId()])) {
            $product->setData('_price_index', true)
                ->setData('_price_index_min_price', $prices[$product->getId()]['min_price'])
                ->setData('_price_index_max_price', $prices[$product->getId()]['max_price']);
        }

        return $this;
    }
}
