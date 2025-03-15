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
 * Abstract API2 class for order items rest
 *
 * @category   Mage
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Api2_Order_Item_Rest extends Mage_Sales_Model_Api2_Order_Item
{
    /**
     * Parameters in request used in model (usually specified in route)
     */
    public const PARAM_ORDER_ID = 'id';

    /**
     * Get order items list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = [];
        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($this->_getCollectionForRetrieve() as $item) {
            $itemData = $item->getData();
            $itemData['status'] = $item->getStatus();
            $data[] = $itemData;
        }
        return $data;
    }
    /**
     * Retrieve order items collection
     *
     * @return Mage_Sales_Model_Resource_Order_Item_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $order = $this->_loadOrderById(
            $this->getRequest()->getParam(self::PARAM_ORDER_ID),
        );

        /** @var Mage_Sales_Model_Resource_Order_Item_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_item_collection');
        $collection->setOrderFilter($order->getId());
        $this->_applyCollectionModifiers($collection);
        return $collection;
    }

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
        return $order;
    }
}
