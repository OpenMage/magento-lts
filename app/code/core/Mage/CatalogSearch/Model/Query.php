<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Catalog search query model
 *
 * @package    Mage_CatalogSearch
 *
 * @method Mage_CatalogSearch_Model_Resource_Query _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query_Collection getCollection()
 * @method int getDisplayInTerms()
 * @method int getIsActive()
 * @method int getIsProcessed()
 * @method string getName()
 * @method int getNumResults()
 * @method int getPopularity()
 * @method string getQueryText()
 * @method string getRedirect()
 * @method Mage_CatalogSearch_Model_Resource_Query getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query_Collection getResourceCollection()
 * @method string getSynonymFor()
 * @method string getUpdatedAt()
 * @method $this setDisplayInTerms(int $value)
 * @method $this setIsActive(int $value)
 * @method $this setIsProcessed(int $value)
 * @method $this setNumResults(int $value)
 * @method $this setPopularity(int $value)
 * @method $this setQueryText(string $value)
 * @method $this setRatio(float $value)
 * @method $this setRedirect(string $value)
 * @method $this setSynonymFor(string $value)
 * @method $this setUpdatedAt(string $value)
 */
class Mage_CatalogSearch_Model_Query extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'catalogsearch_query';

    /**
     * @var string
     */
    protected $_eventObject = 'catalogsearch_query';

    public const CACHE_TAG                     = 'SEARCH_QUERY';

    public const XML_PATH_MIN_QUERY_LENGTH     = 'catalog/search/min_query_length';

    public const XML_PATH_MAX_QUERY_LENGTH     = 'catalog/search/max_query_length';

    public const XML_PATH_MAX_QUERY_WORDS      = 'catalog/search/max_query_words';

    public const XML_PATH_AJAX_SUGGESTION_COUNT = 'catalog/search/show_autocomplete_results_count';

    /**
     * Init resource model
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
     * @return int
     * @deprecated after 1.3.2.3 use getMinQueryLength() instead
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
    public function getMinQueryLength()
    {
        return $this->getMinQueryLenght();
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     * @deprecated after 1.3.2.3 use getMaxQueryLength() instead
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
