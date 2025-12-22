<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports Recently Products Abstract Block
 *
 * @package    Mage_Reports
 *
 * @method int   getCustomerId()
 * @method array getProductIds()
 */
abstract class Mage_Reports_Block_Product_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Product Index model name
     *
     * @var null|string
     */
    protected $_indexName;

    /**
     * Product Index model instance
     *
     * @var null|Mage_Core_Model_Abstract|Mage_Reports_Model_Product_Index_Abstract
     */
    protected $_indexModel;

    /**
     * Product Index Collection
     *
     * @var null|Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    protected $_collection;

    /**
     * Defines whether specified products ids order should be used
     *
     * @var bool
     */
    protected $_useProductIdsOrder = false;

    /**
     * Default product amount per row
     *
     * @var int
     */
    protected $_defaultColumnCount = 5;

    /**
     * Retrieve page size
     *
     * @return int
     */
    public function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }

        return 5;
    }

    /**
     * Retrieve product ids, that must not be included in collection
     *
     * @return array
     */
    protected function _getProductsToSkip()
    {
        return [];
    }

    /**
     * Retrieve Product Index model instance
     *
     * @return Mage_Core_Model_Abstract|Mage_Reports_Model_Product_Index_Abstract
     */
    protected function _getModel()
    {
        if (is_null($this->_indexModel)) {
            if (is_null($this->_indexName)) {
                Mage::throwException(Mage::helper('reports')->__('Index model name must be defined'));
            }

            $this->_indexModel = Mage::getModel($this->_indexName);
        }

        return $this->_indexModel;
    }

    /**
     * Public method for retrieve Product Index model
     *
     * @return Mage_Core_Model_Abstract|Mage_Reports_Model_Product_Index_Abstract
     */
    public function getModel()
    {
        return $this->_getModel();
    }

    /**
     * Retrieve Index Product Collection
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (is_null($this->_collection)) {
            $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();

            $this->_collection = $this->_getModel()
                ->getCollection()
                ->addAttributeToSelect($attributes);

            if ($this->getCustomerId()) {
                $this->_collection->setCustomerId($this->getCustomerId());
            }

            $this->_collection->excludeProductIds($this->_getModel()->getExcludeProductIds())
                    ->addUrlRewrite()
                    ->setPageSize($this->getPageSize())
                    ->setCurPage(1);

            /* Price data is added to consider item stock status using price index */
            $this->_collection->addPriceData();

            $ids = $this->getProductIds();
            if (empty($ids)) {
                $this->_collection->addIndexFilter();
            } else {
                $this->_collection->addFilterByIds($ids);
            }

            $this->_collection->setAddedAtOrder();
            if ($this-> _useProductIdsOrder && is_array($ids)) {
                $this->_collection->setSortIds($ids);
            }

            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_collection);
        }

        return $this->_collection;
    }

    /**
     * Set flag that defines whether products ids order should be used
     *
     * @param  bool                                $use
     * @return Mage_Reports_Block_Product_Abstract
     */
    public function useProductIdsOrder($use = true)
    {
        $this->_useProductIdsOrder = $use;
        return $this;
    }

    /**
     * Retrieve count of product index items
     *
     * @return int
     */
    public function getCount()
    {
        if (!$this->_getModel()->getCount()) {
            return 0;
        }

        return $this->getItemsCollection()->count();
    }

    /**
     * Get products collection and apply recent events log to it
     *
     * @return Mage_Reports_Model_Resource_Product_Index_Collection_Abstract
     * @deprecated
     */
    protected function _getRecentProductsCollection()
    {
        return $this->getItemsCollection();
    }
}
