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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search query model
 *
 * @method Mage_CatalogSearch_Model_Resource_Query _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query getResource()
 * @method string getQueryText()
 * @method Mage_CatalogSearch_Model_Query setQueryText(string $value)
 * @method int getNumResults()
 * @method Mage_CatalogSearch_Model_Query setNumResults(int $value)
 * @method int getPopularity()
 * @method Mage_CatalogSearch_Model_Query setPopularity(int $value)
 * @method string getRedirect()
 * @method Mage_CatalogSearch_Model_Query setRedirect(string $value)
 * @method string getSynonymFor()
 * @method Mage_CatalogSearch_Model_Query setSynonymFor(string $value)
 * @method int getDisplayInTerms()
 * @method Mage_CatalogSearch_Model_Query setDisplayInTerms(int $value)
 * @method int getIsActive()
 * @method Mage_CatalogSearch_Model_Query setIsActive(int $value)
 * @method int getIsProcessed()
 * @method Mage_CatalogSearch_Model_Query setIsProcessed(int $value)
 * @method string getUpdatedAt()
 * @method Mage_CatalogSearch_Model_Query setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Query extends Mage_Core_Model_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'catalogsearch_query';

    /**
     * Event object key name
     *
     * @var string
     */
    protected $_eventObject = 'catalogsearch_query';

    const CACHE_TAG                     = 'SEARCH_QUERY';
    const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';
    const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';
    const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';
    const XML_PATH_AJAX_SUGGESTION_COUNT = 'catalog/search/show_autocomplete_results_count';

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/query');
    }

    /**
     * Retrieve search collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Search_Collection
     */
    public function getSearchCollection()
    {
        return Mage::getResourceModel('catalogsearch/search_collection');
    }

    /**
     * Retrieve collection of search results
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getResultCollection()
    {
        $collection = $this->getData('result_collection');
        if (is_null($collection)) {
            $collection = $this->getSearchCollection();

            $text = $this->getSynonymFor();
            if (!$text) {
                $text = $this->getQueryText();
            }

            $collection->addSearchFilter($text)
                ->addStoreFilter()
                ->addMinimalPrice()
                ->addTaxPercents();
            $this->setData('result_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve collection of suggest queries
     *
     * @return Mage_CatalogSearch_Model_Resource_Query_Collection
     */
    public function getSuggestCollection()
    {
        $collection = $this->getData('suggest_collection');
        if (is_null($collection)) {
            $collection = Mage::getResourceModel('catalogsearch/query_collection')
                ->setStoreId($this->getStoreId())
                ->setQueryFilter($this->getQueryText());
            $this->setData('suggest_collection', $collection);
        }
        return $collection;
    }

    /**
     * Load Query object by query string
     *
     * @param string $text
     * @return $this
     */
    public function loadByQuery($text)
    {
        $this->_getResource()->loadByQuery($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Load Query object only by query text (skip 'synonym For')
     *
     * @param string $text
     * @return $this
     */
    public function loadByQueryText($text)
    {
        $this->_getResource()->loadByQueryText($this, $text);
        $this->_afterLoad();
        $this->setOrigData();
        return $this;
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$storeId = $this->getData('store_id')) {
            $storeId = Mage::app()->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * Prepare save query for result
     *
     * @return $this
     */
    public function prepare()
    {
        if (!$this->getId()) {
            $this->setIsActive(0);
            $this->setIsProcessed(0);
            $this->save();
            $this->setIsActive(1);
        }

        return $this;
    }

    /**
     * Retrieve minimum query length
     *
     * @deprecated after 1.3.2.3 use getMinQueryLength() instead
     * @return int
     */
    public function getMinQueryLenght()
    {
        return Mage::getStoreConfig(self::XML_PATH_MIN_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve minimum query length
     *
     * @return int
     */
    public function getMinQueryLength(){
        return $this->getMinQueryLenght();
    }

    /**
     * Retrieve maximum query length
     *
     * @deprecated after 1.3.2.3 use getMaxQueryLength() instead
     * @return int
     */
    public function getMaxQueryLenght()
    {
        return 0;
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     */
    public function getMaxQueryLength()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAX_QUERY_LENGTH, $this->getStoreId());
    }

    /**
     * Retrieve maximum query words for like search
     *
     * @return int
     */
    public function getMaxQueryWords()
    {
        return Mage::getStoreConfig(self::XML_PATH_MAX_QUERY_WORDS, $this->getStoreId());
    }
}
