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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer entity resource model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Customer extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customer');
        $this->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }

    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email=?', $customer->getEmail());
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $select->where('website_id=?', (int) $customer->getWebsiteId());
        }
        if ($customer->getId()) {
            $select->where('entity_id !=?', $customer->getId());
        }

        if ($this->_getWriteAdapter()->fetchOne($select)) {
            Mage::throwException(Mage::helper('customer')->__('Customer email already exists'));
        }

        // set confirmation key logic
        if ($customer->getForceConfirmed()) {
            $customer->setConfirmation(null);
        }
        elseif ((!$customer->getId()) && ($customer->isConfirmationRequired())) {
            $customer->setConfirmation($customer->getRandomConfirmationKey());
        }
        // remove customer confirmation key from database, if empty
        if (!$customer->getConfirmation()) {
            $customer->setConfirmation(null);
        }

        return $this;
    }

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param   Varien_Object $customer
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        $this->_saveAddresses($customer);
        return parent::_afterSave($customer);
    }


    protected function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        foreach ($customer->getAddresses() as $address) {
            if ($address->getData('_deleted')) {
                $address->delete();
            }
            else {
                $address->setParentId($customer->getId())
                    ->setStoreId($customer->getStoreId())
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = parent::_getLoadRowSelect($object, $rowId);
        if ($object->getWebsiteId() && $object->getSharingConfig()->isWebsiteScope()) {
            $select->where('website_id=?', (int) $object->getWebsiteId());
        }
        return $select;
    }

    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly=false)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            //->where('email=?', $email);
            ->where('email=:customer_email');
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(Mage::helper('customer')->__('Customer website id must be specified, when using website scope.'));
            }
            $select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('customer_email' => $email))) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }

    /**
     * Change customer password
     * $data = array(
     *      ['password']
     *      ['confirmation']
     *      ['current_password']
     * )
     *
     * @param   Mage_Customer_Model_Customer
     * @param   array $data
     * @param   bool $checkCurrent
     * @return  this
     */
    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword, $checkCurrent=true)
    {
        $customer->setPassword($newPassword);
        $this->saveAttribute($customer, 'password_hash');
        return $this;
    }
}
