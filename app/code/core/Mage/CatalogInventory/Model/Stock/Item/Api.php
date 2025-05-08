<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/**
 * Catalog inventory api
 *
 * @package    Mage_CatalogInventory
 */
class Mage_CatalogInventory_Model_Stock_Item_Api extends Mage_Catalog_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function items($productIds)
    {
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }

        $product = Mage::getModel('catalog/product');

        foreach ($productIds as &$productId) {
            if ($newId = $product->getIdBySku($productId)) {
                $productId = $newId;
            }
        }

        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->setFlag('require_stock_items', true)
            ->addFieldToFilter('entity_id', ['in' => $productIds]);

        $result = [];

        foreach ($collection as $product) {
            if ($product->getStockItem()) {
                $result[] = [
                    'product_id'    => $product->getId(),
                    'sku'           => $product->getSku(),
                    'qty'           => $product->getStockItem()->getQty(),
                    'is_in_stock'   => $product->getStockItem()->getIsInStock(),
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $productId
     * @param array $data
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function update($productId, $data)
    {
        $product = Mage::getModel('catalog/product');

        if ($newId = $product->getIdBySku($productId)) {
            $productId = $newId;
        }

        $product->setStoreId($this->_getStoreId())
            ->load($productId);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        if (!$stockData = $product->getStockData()) {
            $stockData = [];
        }

        if (isset($data['qty'])) {
            $stockData['qty'] = $data['qty'];
        }

        if (isset($data['is_in_stock'])) {
            $stockData['is_in_stock'] = $data['is_in_stock'];
        }

        if (isset($data['manage_stock'])) {
            $stockData['manage_stock'] = $data['manage_stock'];
        }

        if (isset($data['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = $data['use_config_manage_stock'];
        }

        if (isset($data['use_config_backorders'])) {
            $stockData['use_config_backorders'] = $data['use_config_backorders'];
        }

        if (isset($data['backorders'])) {
            $stockData['backorders'] = $data['backorders'];
        }

        if (isset($data['min_sale_qty'])) {
            $stockData['min_sale_qty'] = $data['min_sale_qty'];
        }

        $product->setStockData($stockData);

        try {
            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }

        return true;
    }
}
