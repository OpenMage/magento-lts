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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer group resource model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
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
        $this->_uniqueFields = array(
            array(
                'field' => 'customer_group_code',
                'title' => Mage::helper('customer')->__('Customer Group')
            ));

        return $this;
    }

    /**
     * Check if group uses as default
     *
     * @param  Mage_Core_Model_Abstract $group
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Resource_Db_Abstract
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
     * @param Mage_Core_Model_Abstract $group
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
