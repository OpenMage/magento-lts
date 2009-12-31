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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search query collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Query_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
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
     * @return Mage_CatalogSearch_Model_Mysql4_Query_Collection
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
     * @return Mage_CatalogSearch_Model_Mysql4_Query_Collection
     */
    public function setQueryFilter($query)
    {
        $this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
            ->from(
                array('main_table' => $this->getTable('catalogsearch/search_query')),
                array('query' => "IF(IFNULL(synonym_for,'')<>'', synonym_for, query_text)", 'num_results')
            )
            ->where('num_results>0 AND display_in_terms=1 AND query_text LIKE ?', $query.'%')
            ->order('popularity desc');
        if ($this->getStoreId()) {
            $this->getSelect()
                ->where('store_id=?', $this->getStoreId());
        }
        return $this;
    }

    /**
     * Set Popular Search Query Filter
     *
     * @param int|array $storeIds
     * @return Mage_CatalogSearch_Model_Mysql4_Query_Collection
     */
    public function setPopularQueryFilter($storeIds = null)
    {
        $this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
            ->from(
                array('main_table'=>$this->getTable('catalogsearch/search_query')),
                array('name'=>"if(ifnull(synonym_for,'')<>'', synonym_for, query_text)", 'num_results')
            );
        if ($storeIds) {
            $this->addStoreFilter($storeIds);
            $this->getSelect()->where('num_results > 0');
        }
        elseif (null === $storeIds) {
            $this->addStoreFilter(Mage::app()->getStore()->getId());
            $this->getSelect()->where('num_results > 0');
        }

        $this->getSelect()->order(array('popularity desc','name'));

        return $this;
    }

    /**
     * Set Recent Queries Order
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Query_Collection
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
     * @return Mage_CatalogSearch_Model_Mysql4_Query_Collection
     */
    public function addStoreFilter($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }
}
