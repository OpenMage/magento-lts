<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order shipment tracks collection
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Shipment_Track getItemById(int $value)
 * @method Mage_Sales_Model_Order_Shipment_Track[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Shipment_Track_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_track_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_track_collection';

    /**
     * Order field
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    protected function _construct()
    {
        $this->_init('sales/order_shipment_track');
    }

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return $this
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addFieldToFilter('parent_id', $shipmentId);
        return $this;
    }
}
