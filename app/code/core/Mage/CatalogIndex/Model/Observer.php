<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Event observer and indexer running application
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_parentProductIds = [];

    protected $_productIdsMassupdate = [];

    /**
     * @inheritDoc
     */
    protected function _construct() {}

    /**
     * Get indexer object
     *
     * @return Mage_CatalogIndex_Model_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('catalogindex/indexer');
    }

    /**
     * Get aggregation object
     *
     * @return Mage_CatalogIndex_Model_Aggregation
     */
    protected function _getAggregator()
    {
        return Mage::getSingleton('catalogindex/aggregation');
    }

    /**
     * Clear aggregated layered navigation data
     *
     * @return $this
     */
    public function cleanCache(Varien_Event_Observer $observer)
    {
        $tagsArray = $observer->getEvent()->getTags();
        $tagName = Mage_CatalogIndex_Model_Aggregation::CACHE_FLAG_NAME;

        if (empty($tagsArray) || in_array($tagName, $tagsArray)) {
            $this->_getAggregator()->clearCacheData();
        }

        return $this;
    }
}
