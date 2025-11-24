<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert for changed price model
 *
 * @package    Mage_ProductAlert
 *
 * @method Mage_ProductAlert_Model_Resource_Price _getResource()
 * @method string getAddDate()
 * @method Mage_ProductAlert_Model_Resource_Price_Collection getCollection()
 * @method int getCustomerId()
 * @method string getLastSendDate()
 * @method float getPrice()
 * @method int getProductId()
 * @method Mage_ProductAlert_Model_Resource_Price getResource()
 * @method Mage_ProductAlert_Model_Resource_Price_Collection getResourceCollection()
 * @method int getSendCount()
 * @method int getStatus()
 * @method int getWebsiteId()
 * @method $this setAddDate(string $value)
 * @method $this setCustomerId(int $value)
 * @method $this setLastSendDate(string $value)
 * @method $this setPrice(float $value)
 * @method $this setProductId(int $value)
 * @method $this setSendCount(int $value)
 * @method $this setStatus(int $value)
 * @method $this setWebsiteId(int $value)
 */
class Mage_ProductAlert_Model_Price extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('productalert/price');
    }

    /**
     * @return Mage_ProductAlert_Model_Resource_Price_Customer_Collection
     */
    public function getCustomerCollection()
    {
        return Mage::getResourceModel('productalert/price_customer_collection');
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
