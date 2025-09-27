<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Layer price filter
 *
 * @package    Mage_Catalog
 *
 * @method array getInterval()
 * @method $this setInterval(array $value)
 * @method array getPriorIntervals()
 * @method $this setPriorIntervals(array $value)
 */
class Mage_Catalog_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    /**
     * XML configuration paths for Price Layered Navigation
     */
    public const XML_PATH_RANGE_CALCULATION       = 'catalog/layered_navigation/price_range_calculation';
    public const XML_PATH_RANGE_STEP              = 'catalog/layered_navigation/price_range_step';
    public const XML_PATH_RANGE_MAX_INTERVALS     = 'catalog/layered_navigation/price_range_max_intervals';
    public const XML_PATH_ONE_PRICE_INTERVAL      = 'catalog/layered_navigation/one_price_interval';
    public const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';

    /**
     * Price layered navigation modes: Automatic (equalize price ranges), Automatic (equalize product counts), Manual
     */
    public const RANGE_CALCULATION_AUTO     = 'auto'; // equalize price ranges
    public const RANGE_CALCULATION_IMPROVED = 'improved'; // equalize product counts
    public const RANGE_CALCULATION_MANUAL   = 'manual';

    /**
     * Minimal size of the range
     */
    public const MIN_RANGE_POWER = 10;

    /**
     * Resource instance
     *
     * @var Mage_Catalog_Model_Resource_Layer_Filter_Price|null
     */
    protected $_resource;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'price';
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Price
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('catalog/layer_filter_price');
        }
        return $this->_resource;
    }

    /**
     * Get price range for building filter steps
     *
     * @return int
     */
    public function getPriceRange()
    {
        $range = $this->getData('price_range');
        if (!$range) {
            $currentCategory = Mage::registry('current_category_filter');
            if ($currentCategory) {
                $range = $currentCategory->getFilterPriceRange();
            } else {
                $range = $this->getLayer()->getCurrentCategory()->getFilterPriceRange();
            }

            $maxPrice = $this->getMaxPriceInt();
            if (!$range) {
                $calculation = Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION);
                if ($calculation == self::RANGE_CALCULATION_AUTO) {
                    $index = 1;
                    do {
                        $range = 10 ** (strlen(floor($maxPrice)) - $index);
                        $items = $this->getRangeItemCounts($range);
                        $index++;
                    } while ($range > self::MIN_RANGE_POWER && count($items) < 2);
                } else {
                    $range = (float) Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_STEP);
                }
            }

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
            $maxPrice = $this->getLayer()->getProductCollection()->getMaxPrice();
            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }

        return $maxPrice;
    }

    /**
     * Get information about products count in range
     *
     * @param   int $range
     * @return  array
     */
    public function getRangeItemCounts($range)
    {
        $rangeKey = 'range_item_counts_' . $range;
        $items = $this->getData($rangeKey);
        if (is_null($items)) {
            $items = $this->_getResource()->getCount($this, $range);
            // checking max number of intervals
            $i = 0;
            $lastIndex = null;
            $maxIntervalsNumber = $this->getMaxIntervalsNumber();
            $calculation = Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION);
            foreach ($items as $k => $v) {
                ++$i;
                if ($calculation == self::RANGE_CALCULATION_MANUAL && $i > 1 && $i > $maxIntervalsNumber) {
                    $items[$lastIndex] += $v;
                    unset($items[$k]);
                } else {
                    $lastIndex = $k;
                }
            }
            $this->setData($rangeKey, $items);
        }

        return $items;
    }

    /**
     * Prepare text of item label
     *
     * @deprecated since 1.7.0.0
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($range, $value)
    {
        $store      = Mage::app()->getStore();
        $fromPrice  = $store->formatPrice(($value - 1) * $range);
        $toPrice    = $store->formatPrice($value * $range);

        return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
    }

    /**
     * Prepare text of range label
     *
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return string
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $store      = Mage::app()->getStore();
        $formattedFromPrice  = $store->formatPrice($fromPrice);
        if ($toPrice === '') {
            return Mage::helper('catalog')->__('%s and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && Mage::app()->getStore()->getConfig(self::XML_PATH_ONE_PRICE_INTERVAL)) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice = (float) $toPrice - .01;
            }
            return Mage::helper('catalog')->__('%s - %s', $formattedFromPrice, $store->formatPrice($toPrice));
        }
    }

    /**
     * Get price aggreagation data cache key
     * @deprecated after 1.4
     * @return string
     */
    protected function _getCacheKey()
    {
        $key = $this->getLayer()->getStateKey()
            . '_PRICES_GRP_' . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_CURR_' . Mage::app()->getStore()->getCurrentCurrencyCode()
            . '_ATTR_' . $this->getAttributeModel()->getAttributeCode()
            . '_LOC_'
        ;
        $taxReq = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();

        return $key . implode('_', $taxReq->getData());
    }

    /**
     * Get additional request param data
     *
     * @return string
     */
    protected function _getAdditionalRequestData()
    {
        $result = '';
        $appliedInterval = $this->getInterval();
        if ($appliedInterval) {
            $result = ',' . $appliedInterval[0] . '-' . $appliedInterval[1];
            $priorIntervals = $this->getResetValue();
            if ($priorIntervals) {
                $result .= ',' . $priorIntervals;
            }
        }

        return $result;
    }

    /**
     * Get data generated by algorithm for build price filter items
     *
     * @return array
     */
    protected function _getCalculatedItemsData()
    {
        /** @var Mage_Catalog_Model_Layer_Filter_Price_Algorithm $algorithmModel */
        $algorithmModel = Mage::getSingleton('catalog/layer_filter_price_algorithm');
        $collection = $this->getLayer()->getProductCollection();
        $appliedInterval = $this->getInterval();
        if ($appliedInterval
            && $collection->getPricesCount() <= $this->getIntervalDivisionLimit()
        ) {
            return [];
        }
        $algorithmModel->setPricesModel($this)->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getPricesCount(),
        );

        if ($appliedInterval) {
            if ($appliedInterval[0] == $appliedInterval[1] || $appliedInterval[1] === '0') {
                return [];
            }
            $algorithmModel->setLimits($appliedInterval[0], $appliedInterval[1]);
        }

        $items = [];
        foreach ($algorithmModel->calculateSeparators() as $separator) {
            $items[] = [
                'label' => $this->_renderRangeLabel($separator['from'], $separator['to']),
                'value' => (($separator['from'] == 0) ? '' : $separator['from'])
                    . '-' . $separator['to'] . $this->_getAdditionalRequestData(),
                'count' => $separator['count'],
            ];
        }

        return $items;
    }

    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
            return $this->_getCalculatedItemsData();
        } elseif ($this->getInterval()) {
            return [];
        }

        $range      = $this->getPriceRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        $data       = [];

        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];

            foreach ($dbRanges as $index => $count) {
                $fromPrice = ($index == 1) ? '' : (($index - 1) * $range);
                $toPrice = ($index == $lastIndex) ? '' : ($index * $range);

                $data[] = [
                    'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                    'value' => $fromPrice . '-' . $toPrice,
                    'count' => $count,
                ];
            }
        }

        return $data;
    }

    /**
     * Apply price range filter to collection
     *
     * @return $this
     */
    protected function _applyPriceRange()
    {
        $this->_getResource()->applyPriceRange($this);
        return $this;
    }

    /**
     * Validate and parse filter request param
     *
     * @param string $filter
     * @return array|bool
     */
    protected function _validateFilter($filter)
    {
        $filter = explode('-', $filter);
        if (count($filter) != 2) {
            return false;
        }
        foreach ($filter as $v) {
            if (($v !== '' && $v !== '0' && (float) $v <= 0) || is_infinite((float) $v)) {
                return false;
            }
        }

        return $filter;
    }

    /**
     * Apply price range filter
     *
     * @param null $filterBlock deprecated
     * @return $this
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        [$from, $to] = $filter;

        $this->setInterval([$from, $to]);

        $priorFilters = [];
        $counter = count($filterParams);
        for ($i = 1; $i < $counter; ++$i) {
            $priorFilter = $this->_validateFilter($filterParams[$i]);
            if ($priorFilter) {
                $priorFilters[] = $priorFilter;
            } else {
                //not valid data
                $priorFilters = [];
                break;
            }
        }
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }

        $this->_applyPriceRange();
        $this->getLayer()->getState()->addFilter($this->_createItem(
            $this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
            $filter,
        ));

        return $this;
    }

    /**
     * Apply filter value to product collection based on filter range and selected value
     *
     * @deprecated since 1.7.0.0
     * @param int $range
     * @param int $index
     * @return $this
     */
    protected function _applyToCollection($range, $index)
    {
        $this->_getResource()->applyFilterToCollection($this, $range, $index);
        return $this;
    }

    /**
     * Retrieve active customer group id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerGroupId = $this->_getData('customer_group_id');
        if (is_null($customerGroupId)) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $customerGroupId;
    }

    /**
     * Set active customer group id for filter
     *
     * @param int $customerGroupId
     * @return $this
     */
    public function setCustomerGroupId($customerGroupId)
    {
        return $this->setData('customer_group_id', $customerGroupId);
    }

    /**
     * Retrieve active currency rate for filter
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        $rate = $this->_getData('currency_rate');
        if (is_null($rate)) {
            $rate = Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyRate();
        }
        if (!$rate) {
            $rate = 1;
        }
        return $rate;
    }

    /**
     * Set active currency rate for filter
     *
     * @param float $rate
     * @return $this
     */
    public function setCurrencyRate($rate)
    {
        return $this->setData('currency_rate', $rate);
    }

    /**
     * Get maximum number of intervals
     *
     * @return int
     */
    public function getMaxIntervalsNumber()
    {
        return (int) Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_MAX_INTERVALS);
    }

    /**
     * Get interval division limit
     *
     * @return int
     */
    public function getIntervalDivisionLimit()
    {
        return (int) Mage::app()->getStore()->getConfig(self::XML_PATH_INTERVAL_DIVISION_LIMIT);
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return null|string
     */
    public function getResetValue()
    {
        $priorIntervals = $this->getPriorIntervals();
        $value = [];
        if ($priorIntervals) {
            foreach ($priorIntervals as $priorInterval) {
                $value[] = implode('-', $priorInterval);
            }
            return implode(',', $value);
        }
        return parent::getResetValue();
    }

    /**
     * Get 'clear price' link text
     *
     * @return false|string
     */
    public function getClearLinkText()
    {
        if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED
            && $this->getPriorIntervals()
        ) {
            return Mage::helper('catalog')->__('Clear Price');
        }

        return parent::getClearLinkText();
    }

    /**
     * Load range of product prices
     *
     * @param int $limit
     * @param int|null $offset
     * @param float|null $lowerPrice
     * @param float|null $upperPrice
     * @return array
     */
    public function loadPrices($limit, $offset = null, $lowerPrice = null, $upperPrice = null)
    {
        $prices = $this->_getResource()->loadPrices($this, $limit, $offset, $lowerPrice, $upperPrice);
        if ($prices) {
            $prices = array_map('\floatval', $prices);
        }

        return $prices;
    }

    /**
     * Load range of product prices, preceding the price
     *
     * @param float $price
     * @param int $index
     * @param float|null $lowerPrice
     * @return array|false
     */
    public function loadPreviousPrices($price, $index, $lowerPrice = null)
    {
        $prices = $this->_getResource()->loadPreviousPrices($this, $price, $index, $lowerPrice);
        if ($prices) {
            $prices = array_map('\floatval', $prices);
        }

        return $prices;
    }

    /**
     * Load range of product prices, next to the price
     *
     * @param float $price
     * @param int $rightIndex
     * @param float|null $upperPrice
     * @return array|false
     */
    public function loadNextPrices($price, $rightIndex, $upperPrice = null)
    {
        $prices = $this->_getResource()->loadNextPrices($this, $price, $rightIndex, $upperPrice);
        if ($prices) {
            $prices = array_map('\floatval', $prices);
        }

        return $prices;
    }
}
