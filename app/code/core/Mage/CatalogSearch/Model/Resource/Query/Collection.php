<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search query collection
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Query_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store for filter
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Init model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/query');
    }

    /**
     * Set Store ID for filter
     *
     * @param mixed $store
     * @return $this
     */
    public function setStoreId($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Retrieve Store ID Filter
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Set search query text to filter
     *
     * @param string $query
     * @return $this
     */
    public function setQueryFilter($query)
    {
        /** @var Mage_Core_Model_Resource_Helper_Mysql4 $helper */
        $helper = Mage::getResourceHelper('core');

        $ifSynonymFor = $this->getConnection()
            ->getIfNullSql('synonym_for', 'query_text');
        $this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
            ->from(
                ['main_table' => $this->getTable('catalogsearch/search_query')],
                ['query'      => $ifSynonymFor, 'num_results']
            )
            ->where(
                'num_results > 0 AND display_in_terms = 1 AND query_text LIKE ?',
                $helper->addLikeEscape($query, ['position' => 'start'])
            )
            ->order('popularity ' . Varien_Db_Select::SQL_DESC);
        if ($this->getStoreId()) {
            $this->getSelect()
                ->where('store_id = ?', (int)$this->getStoreId());
        }
        return $this;
    }

    /**
     * Set Popular Search Query Filter
     *
     * @param int|array $storeIds
     * @return $this
     */
    public function setPopularQueryFilter($storeIds = null)
    {
        $ifSynonymFor = new Zend_Db_Expr($this->getConnection()
            ->getCheckSql("synonym_for IS NOT NULL AND synonym_for != ''", 'synonym_for', 'query_text'));

        $this->getSelect()
            ->reset(Zend_Db_Select::FROM)
            ->reset(Zend_Db_Select::COLUMNS)
            ->distinct(true)
            ->from(
                ['main_table' => $this->getTable('catalogsearch/search_query')],
                ['name' => $ifSynonymFor, 'num_results', 'popularity', 'query_id']
            );
        if ($storeIds) {
            $this->addStoreFilter($storeIds);
            $this->getSelect()->where('num_results > 0');
        } elseif ($storeIds === null) {
            $this->addStoreFilter(Mage::app()->getStore()->getId());
            $this->getSelect()->where('num_results > 0');
        }

        $this->getSelect()->order(['popularity desc','name']);

        return $this;
    }

    /**
     * Set Recent Queries Order
     *
     * @return $this
     */
    public function setRecentQueryFilter()
    {
        $this->setOrder('updated_at', 'desc');
        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
