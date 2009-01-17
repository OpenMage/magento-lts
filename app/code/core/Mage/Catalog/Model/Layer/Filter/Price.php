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

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'price';
    }

    /**
     * Retrieve price range for build filter
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

    public function getMaxPriceInt()
    {
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $maxPrice = Mage::getSingleton('catalogindex/price')->getMaxValue($this->getAttributeModel(), $this->_getBaseCollectionSql());
            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }
        return $maxPrice;
    }

    public function getRangeItemCounts($range)
    {
        $items = $this->getData('range_item_counts_'.$range);
        if (is_null($items)) {
            //$items = Mage::getSingleton('catalogindex/price')->getCount($this->getAttributeModel(), $range, $this->_getFilterEntityIds());
            $items = Mage::getSingleton('catalogindex/price')->getCount($this->getAttributeModel(), $range, $this->_getBaseCollectionSql());
            $this->setData('range_item_counts_'.$range, $items);
        }
        return $items;
    }

    protected function _renderItemLabel($range, $value)
    {
        $store = Mage::app()->getStore();
        return $store->formatPrice(($value-1)*$range) . ' - ' . $store->formatPrice($value*$range);
    }

    /**
     * Retrieve filter items
     *
     * @return array
     */
    protected function _initItems()
    {
        $range      = $this->getPriceRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        $items = array();

        foreach ($dbRanges as $index=>$count) {
            $value = $index . ',' . $range;
        	$items[] = $this->_createItem($this->_renderItemLabel($range, $index), $value, $count);
        }

        $this->_items = $items;
        return $this;
    }

    /**
     * Apply price filter to collection
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
            $entityIds = Mage::getSingleton('catalogindex/price')->getFilteredEntities($this->getAttributeModel(), $range, $index, $this->_getFilterEntityIds());
            if ($entityIds) {
                $this->getLayer()->getProductCollection()
                    ->addFieldToFilter('entity_id', $entityIds);

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_renderItemLabel($range, $index), $filter)
                );
                $this->_items = array();
            }
        }
        return $this;
    }
}
