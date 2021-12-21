<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @method Mage_Sales_Model_Resource_Order_Tax _getResource()
 * @method Mage_Sales_Model_Resource_Order_Tax getResource()
 * @method int getOrderId()
 * @method Mage_Sales_Model_Order_Tax setOrderId(int $value)
 * @method string getCode()
 * @method Mage_Sales_Model_Order_Tax setCode(string $value)
 * @method string getTitle()
 * @method Mage_Sales_Model_Order_Tax setTitle(string $value)
 * @method int getPriority()
 * @method Mage_Sales_Model_Order_Tax setPriority(int $value)
 * @method int getPosition()
 * @method Mage_Sales_Model_Order_Tax setPosition(int $value)
 * @method int getProcess()
 * @method Mage_Sales_Model_Order_Tax setProcess(int $value)
 * @method int getHidden()
 * @method Mage_Sales_Model_Order_Tax setHidden(int $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Tax extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/order_tax');
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        return (float) $this->_getData('percent');
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return (float) $this->_getData('amount');
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return (float) $this->_getData('base_amount');
    }

    /**
     * @return float
     */
    public function getBaseRealAmount()
    {
        return (float) $this->_getData('base_real_amount');
    }

    /**
     * @return $this
     */
    public function setPercent($value)
    {
        return $this->setData('percent', (float) $value);
    }

    /**
     * @return $this
     */
    public function setAmount($value)
    {
        return $this->setData('amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseAmount($value)
    {
        return $this->setData('base_amount', (float) $value);
    }

    /**
     * @return $this
     */
    public function setBaseRealAmount($value)
    {
        return $this->setData('base_real_amount', (float) $value);
    }
}
