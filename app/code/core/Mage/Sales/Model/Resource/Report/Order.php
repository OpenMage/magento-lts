<?php
/**
 * Order entity resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Order extends Mage_Sales_Model_Resource_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('sales/report_order_createdat')->aggregate($from, $to);
        Mage::getResourceModel('sales/report_order_updatedat')->aggregate($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);

        return $this;
    }
}
