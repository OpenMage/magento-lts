<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales report invoiced collection
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Invoiced_Collection_Invoiced extends Mage_Sales_Model_Resource_Report_Invoiced_Collection_Order
{
    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/invoiced_aggregated');
        $this->setConnection($this->getResource()->getReadConnection());
    }
}
