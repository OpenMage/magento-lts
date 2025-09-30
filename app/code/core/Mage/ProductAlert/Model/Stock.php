<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert for back in stock model
 *
 * @package    Mage_ProductAlert
 *
 * @method Mage_ProductAlert_Model_Resource_Stock _getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock_Collection getCollection()
 *
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method string getAddDate()
 * @method $this setAddDate(string $value)
 * @method string getSendDate()
 * @method $this setSendDate(string $value)
 * @method int getSendCount()
 * @method $this setSendCount(int $value)
 * @method int getStatus()
 * @method $this setStatus(int $value)
 */
class Mage_ProductAlert_Model_Stock extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    /**
     * @return Mage_ProductAlert_Model_Resource_Stock_Customer_Collection
     */
    public function getCustomerCollection()
    {
        return Mage::getResourceModel('productalert/stock_customer_collection');
    }

    /**
     * @return $this
     */
    public function loadByParam()
    {
        if (!is_null($this->getProductId()) && !is_null($this->getCustomerId()) && !is_null($this->getWebsiteId())) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     */
    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
