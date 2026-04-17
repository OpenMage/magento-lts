<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Order entity resource model
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Order extends Mage_Sales_Model_Resource_Report_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param  null|string $dateFrom
     * @param  null|string $dateTo
     * @return $this
     */
    public function aggregate($dateFrom = null, $dateTo = null)
    {
        Mage::getResourceModel('sales/report_order_createdat')->aggregate($dateFrom, $dateTo);
        Mage::getResourceModel('sales/report_order_updatedat')->aggregate($dateFrom, $dateTo);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);

        return $this;
    }
}
