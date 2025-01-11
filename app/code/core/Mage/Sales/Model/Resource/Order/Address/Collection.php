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
 * Flat sales order payment collection
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Address getItemById(int $value)
 * @method Mage_Sales_Model_Order_Address[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Address_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_address_collection';

    protected function _construct()
    {
        $this->_init('sales/order_address');
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        Mage::dispatchEvent($this->_eventPrefix . '_load_after', [
            $this->_eventObject => $this,
        ]);

        return $this;
    }
}
