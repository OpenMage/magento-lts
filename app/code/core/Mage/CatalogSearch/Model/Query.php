<?php

declare(strict_types=1);

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
 * @method Mage_CatalogSearch_Model_Resource_Query            _getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query_Collection getCollection()
 * @method Mage_CatalogSearch_Model_Resource_Query            getResource()
 * @method Mage_CatalogSearch_Model_Resource_Query_Collection getResourceCollection()
 * @method $this                                              setRatio(float $value)
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
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogsearch/query');
    }

    public function getDisplayInTerms(): int
    {
        return (int) $this->_getData('display_in_terms');
    }

    public function getIsActive(): int
    {
        return (int) $this->_getData('is_active');
    }

    public function getIsProcessed(): int
    {
        return (int) $this->_getData('is_processed');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getNumResults(): int
    {
        return (int) $this->_getData('num_results');
    }

    public function getPopularity(): int
    {
        return (int) $this->_getData('popularity');
    }

    public function getQueryText(): string
    {
        return (string) $this->_getData('query_text');
    }

    public function getRedirect(): ?string
    {
        $v = $this->_getData('redirect');
        return $v !== null ? (string) $v : null;
    }

    public function getSynonymFor(): ?string
    {
        $v = $this->_getData('synonym_for');
        return $v !== null ? (string) $v : null;
    }

    public function setDisplayInTerms(int $value): static
    {
        return $this->setData('display_in_terms', $value);
    }

    public function setIsActive(int $value): static
    {
        return $this->setData('is_active', $value);
    }

    public function setIsProcessed(int $value): static
    {
        return $this->setData('is_processed', $value);
    }

    public function setNumResults(int $value): static
    {
        return $this->setData('num_results', $value);
    }

    public function setPopularity(int $value): static
    {
        return $this->setData('popularity', $value);
    }

    public function setQueryText(string $value): static
    {
        return $this->setData('query_text', $value);
    }

    public function setRedirect(?string $value): static
    {
        return $this->setData('redirect', $value);
    }

    public function setSynonymFor(?string $value): static
    {
        return $this->setData('synonym_for', $value);
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
        $collection = $this->getDataByKey('result_collection');
        if (is_null($collection)) {
            $collection = $this->getSearchCollection();

            $text = $this->getSynonymFor();
            if (!$text) {
                $text = $this->getQueryText();
            }

            $collection->addSearchFilter($text)
                ->addStoreFilter()
                ->addPriceData()
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
        $collection = $this->getDataByKey('suggest_collection');
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
     * @param  string $text
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
     * @param  string $text
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
        if (!$storeId = $this->getDataByKey('store_id')) {
            return Mage::app()->getStore()->getId();
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
     */
    public function getMinQueryLength()
    {
        return Mage::getStoreConfig(self::XML_PATH_MIN_QUERY_LENGTH, $this->getStoreId());
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
