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
 * API2 class for order items (customer)
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Api2_Order_Item_Rest_Customer_V1 extends Mage_Sales_Model_Api2_Order_Item_Rest
{
    /**
     * Load order by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Sales_Model_Order
     */
    protected function _loadOrderById($id)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($id);
        if (!$order->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        // check order owner
        if ($this->getApiUser()->getUserId() != $order->getCustomerId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $order;
    }
}
