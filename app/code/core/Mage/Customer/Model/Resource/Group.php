<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer group resource model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/customer_group', 'customer_group_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            [
                'field' => 'customer_group_code',
                'title' => Mage::helper('customer')->__('Customer Group'),
            ]];

        return $this;
    }

    /**
     * Check if group uses as default
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $group)
    {
        /** @var Mage_Customer_Model_Group $group */
        if ($group->usesAsDefault()) {
            Mage::throwException(Mage::helper('customer')->__('The group "%s" cannot be deleted', $group->getCode()));
        }

        return parent::_beforeDelete($group);
    }

    /**
     * Method set default group id to the customers collection
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $group)
    {
        $customerCollection = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('group_id', $group->getId())
            ->load();
        /** @var Mage_Customer_Model_Customer $customer */
        foreach ($customerCollection as $customer) {
            $defaultGroupId = Mage::helper('customer')->getDefaultCustomerGroupId($customer->getStoreId());
            $customer->setGroupId($defaultGroupId);
            $customer->save();
        }

        return parent::_afterDelete($group);
    }
}
