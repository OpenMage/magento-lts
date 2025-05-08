<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer entity resource model
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Resource_Customer extends Mage_Eav_Model_Entity_Abstract
{
    public function __construct()
    {
        $this->setType('customer');
        $this->setConnection('customer_read', 'customer_write');
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return [
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id',
        ];
    }

    /**
     * Check customer scope, email and confirmation key before saving
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);

        if (!$customer->getEmail()) {
            throw Mage::exception('Mage_Customer', Mage::helper('customer')->__('Customer email is required'));
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = ['email' => $customer->getEmail()];

        $select = $adapter->select()
            ->from($this->getEntityTable(), [$this->getEntityIdField()])
            ->where('email = :email');
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $bind['website_id'] = (int) $customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }
        if ($customer->getId()) {
            $bind['entity_id'] = (int) $customer->getId();
            $select->where('entity_id != :entity_id');
        }

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            throw Mage::exception(
                'Mage_Customer',
                Mage::helper('customer')->__('This customer email already exists'),
                Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS,
            );
        }

        // set confirmation key logic
        if ($customer->getForceConfirmed()) {
            $customer->setConfirmation(null);
        } elseif (!$customer->getId() && $customer->isConfirmationRequired()) {
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
     * @return Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        $this->_saveAddresses($customer);
        return parent::_afterSave($customer);
    }

    /**
     * Save/delete customer address
     *
     * @return $this
     */
    protected function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        $defaultBillingId  = $customer->getData('default_billing');
        $defaultShippingId = $customer->getData('default_shipping');
        foreach ($customer->getAddresses() as $address) {
            if ($address->getData('_deleted')) {
                if ($address->getId() == $defaultBillingId) {
                    $customer->setData('default_billing', null);
                }
                if ($address->getId() == $defaultShippingId) {
                    $customer->setData('default_shipping', null);
                }
                $address->delete();
            } else {
                if ($address->getParentId() != $customer->getId()) {
                    $address->setParentId($customer->getId());
                }

                if ($address->hasDataChanges()) {
                    $address->setStoreId($customer->getStoreId())
                        ->setIsCustomerSaveTransaction(true)
                        ->save();
                } else {
                    $address->setStoreId($customer->getStoreId())
                        ->setIsCustomerSaveTransaction(true);
                }

                if (($address->getIsPrimaryBilling() || $address->getIsDefaultBilling())
                    && $address->getId() != $defaultBillingId
                ) {
                    $customer->setData('default_billing', $address->getId());
                }
                if (($address->getIsPrimaryShipping() || $address->getIsDefaultShipping())
                    && $address->getId() != $defaultShippingId
                ) {
                    $customer->setData('default_shipping', $address->getId());
                }
            }
        }
        if ($customer->dataHasChangedFor('default_billing')) {
            $this->saveAttribute($customer, 'default_billing');
        }
        if ($customer->dataHasChangedFor('default_shipping')) {
            $this->saveAttribute($customer, 'default_shipping');
        }

        return $this;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param Mage_Customer_Model_Customer $object
     * @param mixed $rowId
     * @return Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = parent::_getLoadRowSelect($object, $rowId);
        if ($object->getWebsiteId() && $object->getSharingConfig()->isWebsiteScope()) {
            $select->where('website_id =?', (int) $object->getWebsiteId());
        }

        return $select;
    }

    /**
     * Load customer by email
     *
     * @throws Mage_Core_Exception
     *
     * @param string $email
     * @param bool $testOnly
     * @return $this
     */
    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly = false)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = ['customer_email' => $email];
        $select  = $adapter->select()
            ->from($this->getEntityTable(), [$this->getEntityIdField()])
            ->where('email = :customer_email');

        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                Mage::throwException(
                    Mage::helper('customer')->__('Customer website ID must be specified when using the website scope'),
                );
            }
            $bind['website_id'] = (int) $customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $customerId = $adapter->fetchOne($select, $bind);
        if ($customerId) {
            $this->load($customer, $customerId);
        } else {
            $customer->setData([]);
        }

        return $this;
    }

    /**
     * Change customer password
     *
     * @param string $newPassword
     * @return $this
     */
    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword)
    {
        $customer->setPassword($newPassword)->setPasswordCreatedAt(time());
        $this->saveAttribute($customer, 'password_hash');
        $this->saveAttribute($customer, 'password_created_at');
        return $this;
    }

    /**
     * Check whether there are email duplicates of customers in global scope
     *
     * @return bool
     */
    public function findEmailDuplicates()
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), ['email', 'cnt' => 'COUNT(*)'])
            ->group('email')
            ->order('cnt DESC')
            ->limit(1);
        $lookup = $adapter->fetchRow($select);
        if (empty($lookup)) {
            return false;
        }
        return $lookup['cnt'] > 1;
    }

    /**
     * Check customer by id
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = ['entity_id' => (int) $customerId];
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), 'entity_id')
            ->where('entity_id = :entity_id')
            ->limit(1);

        $result = $adapter->fetchOne($select, $bind);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Get customer website id
     *
     * @param int $customerId
     * @return string
     */
    public function getWebsiteId($customerId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = ['entity_id' => (int) $customerId];
        $select  = $adapter->select()
            ->from($this->getTable('customer/entity'), 'website_id')
            ->where('entity_id = :entity_id');

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Custom setter of increment ID if its needed
     *
     * @return $this
     */
    public function setNewIncrementId(Varien_Object $object)
    {
        if (Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_GENERATE_HUMAN_FRIENDLY_ID)) {
            parent::setNewIncrementId($object);
        }
        return $this;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param string $newResetPasswordLinkToken
     * @return $this
     */
    public function changeResetPasswordLinkToken(Mage_Customer_Model_Customer $customer, $newResetPasswordLinkToken)
    {
        if (is_string($newResetPasswordLinkToken) && !empty($newResetPasswordLinkToken)) {
            $customer->setRpToken($newResetPasswordLinkToken);
            $currentDate = Varien_Date::now();
            $customer->setRpTokenCreatedAt($currentDate);
            $this->saveAttribute($customer, 'rp_token');
            $this->saveAttribute($customer, 'rp_token_created_at');
        }
        return $this;
    }

    /**
     * Change reset password link customer Id
     *
     * Stores new reset password link customer Id
     *
     * @param string $newResetPasswordLinkCustomerId
     * @return $this
     * @throws Exception
     */
    public function changeResetPasswordLinkCustomerId(
        Mage_Customer_Model_Customer $customer,
        $newResetPasswordLinkCustomerId
    ) {
        if (is_string($newResetPasswordLinkCustomerId) && !empty($newResetPasswordLinkCustomerId)) {
            $customer->setRpCustomerId($newResetPasswordLinkCustomerId);
            $this->saveAttribute($customer, 'rp_customer_id');
        }
        return $this;
    }

    /**
     * Get password created at timestamp for a customer by id
     * If attribute password_created_at is empty, return created_at timestamp
     *
     * @param int $customerId
     * @return int|false
     */
    public function getPasswordTimestamp($customerId)
    {
        $field = $this->_getReadAdapter()->getIfNullSql('t2.value', 't0.created_at');
        $select = $this->_getReadAdapter()->select()
            ->from(['t0' => $this->getEntityTable()], ['password_created_at' => $field])
            ->joinLeft(
                ['t1' => $this->getTable('eav/attribute')],
                't0.entity_type_id = t1.entity_type_id',
                [],
            )
            ->joinLeft(
                ['t2' => $this->getTable(['customer/entity', 'int'])],
                't1.attribute_id = t2.attribute_id AND t2.entity_id = t0.entity_id',
                [],
            )
            ->where('t0.entity_id = ?', $customerId)
            ->where('t1.attribute_code = ?', 'password_created_at');

        $value = $this->_getReadAdapter()->fetchOne($select);
        if ($value && !is_numeric($value)) { // Convert created_at string to unix timestamp
            $value = Varien_Date::toTimestamp($value);
        }
        return $value;
    }

    /**
     * Get email by customer ID.
     *
     * @param int $customerId
     * @return string|false
     */
    public function getEmail($customerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'email')
            ->where('entity_id = ?', $customerId);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
