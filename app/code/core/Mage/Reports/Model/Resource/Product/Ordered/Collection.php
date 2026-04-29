<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Products Ordered (Bestsellers) Report collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Product_Ordered_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Join fields
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    #[Override]
    protected function _joinFields($dateFrom = '', $dateTo = '')
    {
        $this->addAttributeToSelect('*')
            ->addOrderedQty($dateFrom, $dateTo)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);

        return $this;
    }

    /**
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function setDateRange($dateFrom, $dateTo)
    {
        $this->_reset()
            ->_joinFields($dateFrom, $dateTo);
        return $this;
    }

    /**
     * Set store ids
     *
     * @param  array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $storeId = array_pop($storeIds);
        $this->setStoreId($storeId);
        $this->addStoreFilter($storeId);
        return $this;
    }
}
