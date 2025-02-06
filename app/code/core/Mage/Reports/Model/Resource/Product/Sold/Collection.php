<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/**
 * Report Sold Products collection
 *
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Sold_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
        // skip adding stock information to collection for performance reasons
        $this->setFlag('no_stock_data', true);
    }
    /**
     * Set Date range to collection
     *
     * @param int $from
     * @param int $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addOrderedQty($from, $to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);
        return $this;
    }

    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()->where('order_items.store_id IN (?)', (array) $storeIds);
        }
        return $this;
    }

    /**
     * Add website product limitation
     *
     * @return $this
     */
    protected function _productLimitationJoinWebsite()
    {
        $filters     = $this->_productLimitationFilters;
        $conditions  = ['product_website.product_id=e.entity_id'];
        if (isset($filters['website_ids'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id IN(?)', $filters['website_ids']);

            $subQuery = $this->getConnection()->select()
                ->from(
                    ['product_website' => $this->getTable('catalog/product_website')],
                    ['product_website.product_id'],
                )
                ->where(implode(' AND ', $conditions));
            $this->getSelect()->where('e.entity_id IN( ' . $subQuery . ' )');
        }

        return $this;
    }
}
