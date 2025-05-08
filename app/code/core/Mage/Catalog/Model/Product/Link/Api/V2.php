<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product link api V2
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Link_Api_V2 extends Mage_Catalog_Model_Product_Link_Api
{
    /**
     * Add product link association
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param string|null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function assign($type, $productId, $linkedProductId, $data = [], $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);
        $idBySku = $product->getIdBySku($linkedProductId);
        if ($idBySku) {
            $linkedProductId = $idBySku;
        }

        $links = $this->_collectionToEditableArray($collection);

        $links[(int) $linkedProductId] = [];
        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data->{$attribute['code']})) {
                $links[(int) $linkedProductId][$attribute['code']] = $data->{$attribute['code']};
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($product, $links, $typeId);
            } else {
                $link->getResource()->saveProductLinks($product, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('catalog/product_link');
            $_linkInstance->saveProductRelations($product);

            $indexerStock = Mage::getModel('cataloginventory/stock_status');
            $indexerStock->updateStatus($productId);

            $indexerPrice = Mage::getResourceModel('catalog/product_indexer_price');
            $indexerPrice->reindexProductIds($productId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
            //$this->_fault('data_invalid', Mage::helper('catalog')->__('Link product does not exist.'));
        }

        return true;
    }

    /**
     * Update product link association info
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param string|null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function update($type, $productId, $linkedProductId, $data = [], $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $links = $this->_collectionToEditableArray($collection);

        $idBySku = $product->getIdBySku($linkedProductId);
        if ($idBySku) {
            $linkedProductId = $idBySku;
        }

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data->{$attribute['code']})) {
                $links[(int) $linkedProductId][$attribute['code']] = $data->{$attribute['code']};
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($product, $links, $typeId);
            } else {
                $link->getResource()->saveProductLinks($product, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('catalog/product_link');
            $_linkInstance->saveProductRelations($product);

            $indexerStock = Mage::getModel('cataloginventory/stock_status');
            $indexerStock->updateStatus($productId);

            $indexerPrice = Mage::getResourceModel('catalog/product_indexer_price');
            $indexerPrice->reindexProductIds($productId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Link product does not exist.'));
        }

        return true;
    }
}
