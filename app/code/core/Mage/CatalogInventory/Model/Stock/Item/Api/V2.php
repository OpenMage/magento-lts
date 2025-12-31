<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Catalog inventory api V2
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Stock_Item_Api_V2 extends Mage_CatalogInventory_Model_Stock_Item_Api
{
    /**
     * Update product stock data
     *
     * @param  string $productId
     * @param  array  $data
     * @return bool
     */
    public function update($productId, $data)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        $idBySku = $product->getIdBySku($productId);
        $productId = $idBySku ? $idBySku : $productId;

        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item')
            ->setStoreId($this->_getStoreId())
            ->loadByProduct($productId);

        if (!$stockItem->getId()) {
            $this->_fault('not_exists');
        }

        $stockData = array_replace($stockItem->getData(), (array) $data);
        $stockItem->setData($stockData);

        try {
            $stockItem->save();
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_fault('not_updated', $mageCoreException->getMessage());
        }

        return true;
    }

    /**
     * Update stock data of multiple products at once
     *
     * @param  array $productIds
     * @param  array $productData
     * @return bool
     */
    public function multiUpdate($productIds, $productData)
    {
        if (count($productIds) != count($productData)) {
            $this->_fault('multi_update_not_match');
        }

        $productData = (array) $productData;

        foreach ($productIds as $index => $productId) {
            $this->update($productId, $productData[$index]);
        }

        return true;
    }
}
