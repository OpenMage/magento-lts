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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order invoice collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Invoice_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_invoice_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    protected function _construct()
    {
        $this->_init('sales/order_invoice');
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');
        return $this;
    }
}
