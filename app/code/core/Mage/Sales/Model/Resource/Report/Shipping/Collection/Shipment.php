<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales report shipping collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Shipping_Collection_Shipment extends Mage_Sales_Model_Resource_Report_Shipping_Collection_Order
{
    public function __construct()
    {
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/shipping_aggregated');
        $this->setConnection($this->getResource()->getReadConnection());
    }
}
