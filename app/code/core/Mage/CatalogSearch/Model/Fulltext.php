<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog advanced search model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 *
 * @method Mage_CatalogSearch_Model_Resource_Fulltext _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Fulltext getResource()
 * @method Mage_CatalogSearch_Model_Resource_Fulltext_Collection getCollection()
 *
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getDataIndex()
 * @method $this setDataIndex(string $value)
 */
class Mage_CatalogSearch_Model_Fulltext extends Mage_Core_Model_Abstract
{
    public const SEARCH_TYPE_LIKE              = 1;
    public const SEARCH_TYPE_FULLTEXT          = 2;
    public const SEARCH_TYPE_COMBINE           = 3;
    public const XML_PATH_CATALOG_SEARCH_TYPE  = 'catalog/search/search_type';
    public const XML_PATH_CATALOG_SEARCH_SEPARATOR  = 'catalog/search/search_separator';

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges = true;

    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext');
    }

    /**
     * Regenerate all Stores index
     *
     * Examples:
     * (null, null) => Regenerate index for all stores
     * (1, null)    => Regenerate index for store Id=1
     * (1, 2)       => Regenerate index for product Id=2 and its store view Id=1
     * (null, 2)    => Regenerate index for all store views of product Id=2
     *
     * @param int|null $storeId Store View Id
     * @param int|array|null $productIds Product Entity Id
     *
     * @return $this
     */
    public function rebuildIndex($storeId = null, $productIds = null)
    {
        Mage::dispatchEvent('catalogsearch_index_process_start', [
            'store_id'      => $storeId,
            'product_ids'   => $productIds,
        ]);

        $this->getResource()->rebuildIndex($storeId, $productIds);

        Mage::dispatchEvent('catalogsearch_index_process_complete', []);

        return $this;
    }

    /**
     * Delete index data
     *
     * Examples:
     * (null, null) => Clean index of all stores
     * (1, null)    => Clean index of store Id=1
     * (1, 2)       => Clean index of product Id=2 and its store view Id=1
     * (null, 2)    => Clean index of all store views of product Id=2
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return $this
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        $this->getResource()->cleanIndex($storeId, $productId);
        return $this;
    }

    /**
     * Reset search results cache
     *
     * @return $this
     */
    public function resetSearchResults()
    {
        $this->getResource()->resetSearchResults();
        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Mage_CatalogSearch_Model_Query $query
     * @return $this
     */
    public function prepareResult($query = null)
    {
        if (!$query instanceof Mage_CatalogSearch_Model_Query) {
            $query = Mage::helper('catalogsearch')->getQuery();
        }
        $queryText = Mage::helper('catalogsearch')->getQueryText();
        if ($query->getSynonymFor()) {
            $queryText = $query->getSynonymFor();
        }
        $this->getResource()->prepareResult($this, $queryText, $query);
        return $this;
    }

    /**
     * Retrieve search type
     *
     * @param int $storeId
     * @return int
     */
    public function getSearchType($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CATALOG_SEARCH_TYPE, $storeId);
    }

    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     *
     * @param bool $value
     * @return $this
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Update category products indexes
     *
     * @deprecated after 1.6.2.0
     *
     * @param array $productIds
     * @param array $categoryIds
     *
     * @return $this
     */
    public function updateCategoryIndex($productIds, $categoryIds)
    {
        $this->getResource()->updateCategoryIndex($productIds, $categoryIds);
        return $this;
    }
}
