<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address model
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Customer            _getResource()
 * @method Mage_Customer_Model_Resource_Customer_Collection getCollection()
 * @method string                                           getEmail()
 * @method int                                              getPostIndex()
 * @method Mage_Customer_Model_Resource_Customer            getResource()
 * @method Mage_Customer_Model_Resource_Customer_Collection getResourceCollection()
 * @method bool                                             hasEmail()
 */
class Mage_Customer_Model_Address extends Mage_Customer_Model_Address_Abstract
{
    protected $_customer;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('customer/address');
    }

    /**
     * Retrieve address customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getData('customer_id') ? $this->_getData('customer_id') : $this->getParentId();
    }

    /**
     * Declare address customer identifier
     *
     * @param  int   $id
     * @return $this
     */
    public function setCustomerId($id)
    {
        $this->setParentId($id);
        $this->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve address customer
     *
     * @return false|Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }

        if (empty($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load($this->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Specify address customer
     *
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        if ($this->getCustomerId() != $customer->getId()) {
            $this->setCustomerId($customer->getId());
        }

        return $this;
    }

    /**
     * Delete customer address
     *
     * @return $this
     */
    public function delete()
    {
        parent::delete();
        $this->setData([]);
        return $this;
    }

    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
            $this->setData('attributes', $attributes);
        }

        return $attributes;
    }

    public function __clone()
    {
        $this->setId(null);
    }

    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }

        return $entityTypeId;
    }

    /**
     * Return Region ID
     *
     * @return int
     */
    public function getRegionId()
    {
        return (int) $this->getData('region_id');
    }

    /**
     * Set Region ID. $regionId is automatically converted to integer
     *
     * @param  int   $regionId
     * @return $this
     */
    public function setRegionId($regionId)
    {
        $this->setData('region_id', (int) $regionId);
        return $this;
    }
}
