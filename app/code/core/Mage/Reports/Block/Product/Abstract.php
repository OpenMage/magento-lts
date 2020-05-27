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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports Recently Products Abstract Block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Reports_Block_Product_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Product Index model name
     *
     * @var string
     */
    protected $_indexName;

    /**
     * Product Index model instance
     *
     * @var Mage_Reports_Model_Product_Index_Abstract
     */
    protected $_indexModel;

    /**
     * Product Index Collection
     *
     * @var Mage_Reports_Model_Mysql4_Product_Index_Collection_Abstract
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
        return array();
    }

    /**
     * Retrieve Product Index model instance
     *
     * @return Mage_Reports_Model_Product_Index_Abstract
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
     * @return Mage_Reports_Model_Product_Index_Abstract
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
     * @param bool $use
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
     * @deprecated
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getRecentProductsCollection()
    {
        return $this->getItemsCollection();
    }
}
