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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Layer price filter
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    const MIN_RANGE_POWER = 10;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'price';
    }

    /**
     * Get price range for building filter steps
     *
     * @return int
     */
    public function getPriceRange()
    {
        $range = $this->getData('price_range');
        if (is_null($range)) {
            $maxPrice = $this->getMaxPriceInt();
            $index = 1;
            do {
                $range = pow(10, (strlen(floor($maxPrice))-$index));
                $items = $this->getRangeItemCounts($range);
                $index++;
            }
            while($range>self::MIN_RANGE_POWER && count($items)<2);

            $this->setData('price_range', $range);
        }
        return $range;
    }

    /**
     * Get maximum price from layer products set
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $maxPrice = Mage::getSingleton('catalogindex/price')->getMaxValue(
                $this->getAttributeModel(),
                $this->_getBaseCollectionSql()
            );

            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }
        return $maxPrice;
    }

    /**
     * Get information about products count in range
     *
     * @param   int $range
     * @return  int
     */
    public function getRangeItemCounts($range)
    {
        $items = $this->getData('range_item_counts_'.$range);
        if (is_null($items)) {
            $items = Mage::getSingleton('catalogindex/price')->getCount(
                $this->getAttributeModel(),
                $range,
                $this->_getBaseCollectionSql()
            );
            $this->setData('range_item_counts_'.$range, $items);
        }
        return $items;
    }

    /**
     * Prepare text of item label
     *
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($range, $value)
    {
        $store      = Mage::app()->getStore();
        $fromPrice  = $store->formatPrice(($value-1)*$range);
        $toPrice    = $store->formatPrice($value*$range);
        return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
    }

    /**
     * Get price aggreagation data cache key
     *
     * @return string
     */
    protected function _getCacheKey()
    {
        $key = $this->getLayer()->getStateKey()
            . '_PRICES_GRP_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_CURR_' . Mage::app()->getStore()->getCurrentCurrencyCode()
            . '_LOC_'
            ;
        $taxReq = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        $key.= $taxReq->__toString(array(), '_');
        return $key;
    }

    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $key = $this->_getCacheKey();

        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $range      = $this->getPriceRange();
            $dbRanges   = $this->getRangeItemCounts($range);
            $data       = array();

            foreach ($dbRanges as $index=>$count) {
                $data[] = array(
                    'label' => $this->_renderItemLabel($range, $index),
                    'value' => $index . ',' . $range,
                    'count' => $count,
                );
            }

            $tags = array(
                Mage_Catalog_Model_Product_Type_Price::CACHE_TAG,
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Apply price range filter to collection
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        /**
         * Filter must be string: $index,$range
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return $this;
        }

        list($index, $range) = $filter;

        if ((int)$index && (int)$range) {
            $this->setPriceRange((int)$range);
            $entityIds = Mage::getSingleton('catalogindex/price')->getFilteredEntities(
                $this->getAttributeModel(),
                $range,
                $index,
                $this->_getFilterEntityIds()
            );

            if ($entityIds) {
                $this->getLayer()->getProductCollection()
                    ->addFieldToFilter('entity_id', array('in'=>$entityIds));

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabel($range, $index), $filter)
                );
                $this->_items = array();
            }
        }
        return $this;
    }
}
