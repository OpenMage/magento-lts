<?php

declare(strict_types=1);

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
 * @method Mage_ProductAlert_Model_Resource_Stock            _getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock_Collection getCollection()
 * @method Mage_ProductAlert_Model_Resource_Stock            getResource()
 * @method Mage_ProductAlert_Model_Resource_Stock_Collection getResourceCollection()
 */
class Mage_ProductAlert_Model_Stock extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    public function getAddDate(): string
    {
        return (string) $this->_getData('add_date');
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function getProductId(): int
    {
        return (int) $this->_getData('product_id');
    }

    public function getSendCount(): int
    {
        return (int) $this->_getData('send_count');
    }

    public function getSendDate(): ?string
    {
        $v = $this->_getData('send_date');
        return $v !== null ? (string) $v : null;
    }

    public function getStatus(): int
    {
        return (int) $this->_getData('status');
    }

    public function getWebsiteId(): int
    {
        return (int) $this->_getData('website_id');
    }

    public function setAddDate(string $value): static
    {
        return $this->setData('add_date', $value);
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function setProductId(int $value): static
    {
        return $this->setData('product_id', $value);
    }

    public function setSendCount(int $value): static
    {
        return $this->setData('send_count', $value);
    }

    public function setSendDate(?string $value): static
    {
        return $this->setData('send_date', $value);
    }

    public function setStatus(int $value): static
    {
        return $this->setData('status', $value);
    }

    public function setWebsiteId(int $value): static
    {
        return $this->setData('website_id', $value);
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
     * @param  int   $customerId
     * @param  int   $websiteId
     * @return $this
     */
    public function deleteCustomer($customerId, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerId, $websiteId);
        return $this;
    }
}
