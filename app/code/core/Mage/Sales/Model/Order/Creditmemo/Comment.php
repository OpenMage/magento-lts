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
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Comment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo_Comment getResource()
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method $this setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method $this setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method $this setComment(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method $this setStoreId(int $value)
 */
class Mage_Sales_Model_Order_Creditmemo_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Creditmemo instance
     *
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    protected function _construct()
    {
        $this->_init('sales/order_creditmemo_comment');
    }

    /**
     * Declare Creditmemo instance
     *
     * @return  Mage_Sales_Model_Order_Creditmemo_Comment
     */
    public function setCreditmemo(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Retrieve Creditmemo instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_creditmemo;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getCreditmemo()) {
            return $this->getCreditmemo()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getCreditmemo()) {
            $this->setParentId($this->getCreditmemo()->getId());
        }

        return $this;
    }
}
