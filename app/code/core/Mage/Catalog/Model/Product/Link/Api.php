<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product link api
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Link_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * Product link type mapping, used for references and validation
     *
     * @var array
     */
    protected $_typeMap = [
        'related'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'up_sell'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'cross_sell'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
        'grouped'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
    ];

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * Retrieve product link associations
     *
     * @param string $type
     * @param int|string $productId
     * @param  string $identifierType
     * @return array
     */
    public function items($type, $productId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $result = [];

        foreach ($collection as $linkedProduct) {
            $row = [
                'product_id' => $linkedProduct->getId(),
                'type'       => $linkedProduct->getTypeId(),
                'set'        => $linkedProduct->getAttributeSetId(),
                'sku'        => $linkedProduct->getSku(),
            ];

            foreach ($link->getAttributes() as $attribute) {
                $row[$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Add product link association
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param  string $identifierType
     * @return bool
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
            if (isset($data[$attribute['code']])) {
                $links[(int) $linkedProductId][$attribute['code']] = $data[$attribute['code']];
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

    /**
     * Update product link association info
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param  string $identifierType
     * @return bool
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
            if (isset($data[$attribute['code']])) {
                $links[(int) $linkedProductId][$attribute['code']] = $data[$attribute['code']];
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

    /**
     * Remove product link association
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param  string $identifierType
     * @return bool
     */
    public function remove($type, $productId, $linkedProductId, $identifierType = null)
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

        if (isset($links[$linkedProductId])) {
            unset($links[$linkedProductId]);
        }

        try {
            $link->getResource()->saveProductLinks($product, $links, $typeId);
        } catch (Exception $e) {
            $this->_fault('not_removed');
        }

        return true;
    }

    /**
     * Retrieve attribute list for specified type
     *
     * @param string $type
     * @return array
     */
    public function attributes($type)
    {
        $typeId = $this->_getTypeId($type);

        $attributes = Mage::getModel('catalog/product_link')
            ->getAttributes($typeId);

        $result = [];

        foreach ($attributes as $attribute) {
            $result[] = [
                'code'  => $attribute['code'],
                'type'  => $attribute['type'],
            ];
        }

        return $result;
    }

    /**
     * Retrieve link types
     *
     * @return array
     */
    public function types()
    {
        return array_keys($this->_typeMap);
    }

    /**
     * Retrieve link type id by code
     *
     * @param string $type
     * @return int
     */
    protected function _getTypeId($type)
    {
        if (!isset($this->_typeMap[$type])) {
            $this->_fault('type_not_exists');
        }

        return $this->_typeMap[$type];
    }

    /**
     * Initialize and return product model
     *
     * @param int $productId
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct($productId, $identifierType = null)
    {
        $product = Mage::helper('catalog/product')->getProduct($productId, null, $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }

    /**
     * Initialize and return linked products collection
     *
     * @param Mage_Catalog_Model_Product_Link $link
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    protected function _initCollection($link, $product)
    {
        return $link
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($product);
    }

    /**
     * Export collection to editable array
     *
     * @param Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return array
     */
    protected function _collectionToEditableArray($collection)
    {
        $result = [];

        foreach ($collection as $linkedProduct) {
            $result[$linkedProduct->getId()] = [];

            foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
                $result[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }
        }

        return $result;
    }
}
