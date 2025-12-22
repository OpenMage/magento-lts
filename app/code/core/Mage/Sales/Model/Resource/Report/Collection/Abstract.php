<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Report collection abstract model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Collection_Abstract extends Mage_Reports_Model_Resource_Report_Collection_Abstract
{
    /**
     * Order status
     *
     * @var null|string
     */
    protected $_orderStatus = null;

    /**
     * Set status filter
     *
     * @param  string $orderStatus
     * @return $this
     */
    public function addOrderStatusFilter($orderStatus)
    {
        $this->_orderStatus = $orderStatus;
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return $this
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }

        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = [$orderStatus];
        }

        $this->getSelect()->where('order_status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Order status filter is custom for this collection
     *
     * @return $this
     */
    protected function _applyCustomFilter()
    {
        return $this->_applyOrderStatusFilter();
    }
}
