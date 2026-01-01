<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Flat sales order payment collection
 *
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Order_Payment   getItemById(int $value)
 * @method Mage_Sales_Model_Order_Payment[] getItems()
 */
class Mage_Sales_Model_Resource_Order_Payment_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_payment_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'order_payment_collection';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Unserialize additional_information in each item
     *
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }

        return parent::_afterLoad();
    }
}
