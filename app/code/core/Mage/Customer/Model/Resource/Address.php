<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer address entity resource model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Address extends Mage_Eav_Model_Entity_Abstract
{
    protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write'),
        );
    }

    /**
     * Set default shipping to address
     *
     * @return $this
     */
    protected function _afterSave(Varien_Object $address)
    {
        if ($address->getIsCustomerSaveTransaction()) {
            return $this;
        }
        if ($address->getId() && ($address->getIsDefaultBilling() || $address->getIsDefaultShipping())) {
            $customer = Mage::getModel('customer/customer')
                ->load($address->getCustomerId());

            if ($address->getIsDefaultBilling()) {
                $customer->setDefaultBilling($address->getId());
            }
            if ($address->getIsDefaultShipping()) {
                $customer->setDefaultShipping($address->getId());
            }
            $customer->save();
        }
        return $this;
    }

    /**
     * Return customer id
     * @deprecated
     *
     * @param Mage_Customer_Model_Address $object
     * @return int
     */
    public function getCustomerId($object)
    {
        return $object->getData('customer_id') ? $object->getData('customer_id') : $object->getParentId();
    }

    /**
     * Set customer id
     * @deprecated
     *
     * @param Mage_Customer_Model_Address $object
     * @param int $id
     * @return $this
     */
    public function setCustomerId($object, $id)
    {
        return $this;
    }
}
