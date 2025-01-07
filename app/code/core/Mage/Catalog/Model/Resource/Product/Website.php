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
 * @copyright  Copyright (c) 2017-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Website Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Product_Website extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_website', 'product_id');
    }

    /**
     * Get catalog product resource model
     *
     * @return Mage_Catalog_Model_Resource_Product
     */
    protected function _getProductResource()
    {
        return Mage::getResourceSingleton('catalog/product');
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return $this
     * @throws Exception
     */
    public function removeProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0
        ) {
            return $this;
        }

        $adapter   = $this->_getWriteAdapter();
        $whereCond = [
            $adapter->quoteInto('website_id IN(?)', $websiteIds),
            $adapter->quoteInto('product_id IN(?)', $productIds),
        ];
        $whereCond = implode(' AND ', $whereCond);

        $adapter->beginTransaction();
        try {
            $adapter->delete($this->getMainTable(), $whereCond);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return $this
     * @throws Exception
     */
    public function addProducts($websiteIds, $productIds)
    {
        if (!is_array($websiteIds) || !is_array($productIds)
            || count($websiteIds) == 0 || count($productIds) == 0
        ) {
            return $this;
        }

        $this->_getWriteAdapter()->beginTransaction();

        try {
            // Before adding of products we should remove it old rows with same ids
            $this->removeProducts($websiteIds, $productIds);

            foreach ($websiteIds as $websiteId) {
                foreach ($productIds as $productId) {
                    if (!$productId) {
                        continue;
                    }
                    $this->_getWriteAdapter()->insert($this->getMainTable(), [
                        'product_id' => (int) $productId,
                        'website_id' => (int) $websiteId,
                    ]);
                }

                // Refresh product enabled index
                $storeIds = Mage::app()->getWebsite($websiteId)->getStoreIds();
                foreach ($storeIds as $storeId) {
                    $store = Mage::app()->getStore($storeId);
                    $this->_getProductResource()->refreshEnabledIndex($store, $productIds);
                }
            }

            $this->_getWriteAdapter()->commit();
        } catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve product(s) website ids.
     *
     * @param array $productIds
     * @return array
     */
    public function getWebsites($productIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), ['product_id', 'website_id'])
            ->where('product_id IN (?)', $productIds);
        $rowset  = $this->_getReadAdapter()->fetchAll($select);

        $result = [];
        foreach ($rowset as $row) {
            $result[$row['product_id']][] = $row['website_id'];
        }

        return $result;
    }
}
