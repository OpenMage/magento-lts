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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order shipment collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection extends Mage_Sales_Model_Resource_Order_Shipment_Collection
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_grid_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_shipment_grid_collection';

    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales/shipment_grid');
    }
}
