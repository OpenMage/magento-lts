<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Resource_Order_Tax _getResource()
 * @method float getAmount()
 * @method float getBaseAmount()
 * @method float getBaseRealAmount()
 * @method string getCode()
 * @method Mage_Sales_Model_Resource_Order_Tax_Collection getCollection()
 * @method int getHidden()
 * @method int getOrderId()
 * @method float getPercent()
 * @method int getPosition()
 * @method int getPriority()
 * @method int getProcess()
 * @method Mage_Sales_Model_Resource_Order_Tax getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax_Collection getResourceCollection()
 * @method string getTitle()
 * @method $this setAmount(float $value)
 * @method $this setBaseAmount(float $value)
 * @method $this setBaseRealAmount(float $value)
 * @method $this setCode(string $value)
 * @method $this setHidden(int $value)
 * @method $this setOrderId(int $value)
 * @method $this setPercent(float $value)
 * @method $this setPosition(int $value)
 * @method $this setPriority(int $value)
 * @method $this setProcess(int $value)
 * @method $this setTitle(string $value)
 */
class Mage_Sales_Model_Order_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }
}
