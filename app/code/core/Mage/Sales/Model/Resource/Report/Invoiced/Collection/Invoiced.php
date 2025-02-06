<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales report invoiced collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Report_Invoiced_Collection_Invoiced extends Mage_Sales_Model_Resource_Report_Invoiced_Collection_Order
{
    /**
     * Initialize custom resource model
     *
     */
    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init('sales/invoiced_aggregated');
        $this->setConnection($this->getResource()->getReadConnection());
    }
}
