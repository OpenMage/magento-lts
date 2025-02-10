<?php
/**
 * Sales report refunded collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Refunded_Collection_Refunded extends Mage_Sales_Model_Resource_Report_Refunded_Collection_Order
{
    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/refunded_aggregated');
        $this->setConnection($this->getResource()->getReadConnection());
    }
}
