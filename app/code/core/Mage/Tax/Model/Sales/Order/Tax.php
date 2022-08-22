<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax _getResource()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax getResource()
 * @method Mage_Tax_Model_Resource_Sales_Order_Tax_Collection getCollection()
 *
 * @method int getOrderId()
 * @method $this setOrderId(int $value)
 * @method string getCode()
 * @method $this setCode(string $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method float getPercent()
 * @method $this setPercent(float $value)
 * @method float getAmount()
 * @method $this setAmount(float $value)
 * @method int getPriority()
 * @method $this setPriority(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method float getBaseAmount()
 * @method $this setBaseAmount(float $value)
 * @method int getProcess()
 * @method $this setProcess(int $value)
 * @method float getBaseRealAmount()
 * @method $this setBaseRealAmount(float $value)
 * @method int getHidden()
 * @method $this setHidden(int $value)
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Sales_Order_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/sales_order_tax');
    }
}
