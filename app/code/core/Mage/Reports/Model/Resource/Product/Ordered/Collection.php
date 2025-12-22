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
     * @param  string $from
     * @param  string $to
     * @return $this
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this->addAttributeToSelect('*')
            ->addOrderedQty($from, $to)
            ->setOrder('ordered_qty', self::SORT_ORDER_DESC);

        return $this;
    }

    /**
     * @param  string $from
     * @param  string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->_joinFields($from, $to);
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
