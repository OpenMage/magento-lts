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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
{
    const XML_PATH_REGISTER_EMAIL_TEMPLATE      = 'customer/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY      = 'customer/create_account/email_identity';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE        = 'customer/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY        = 'customer/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'customer/create_account/email_domain';
    const XML_PATH_IS_CONFIRM                   = 'customer/create_account/confirm';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'customer/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'customer/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'customer/create_account/generate_human_friendly_id';

    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;

    const SUBSCRIBED_YES = 'yes';
    const SUBSCRIBED_NO  = 'no';

    protected $_eventPrefix = 'customer';
    protected $_eventObject = 'customer';
    protected $_errors    = array();
    protected $_attributes;

    /**
     * Customer addresses array
     *
     * @var array
     * @deprecated after 1.4.0.0-rc1
     */
    protected $_addresses = null;

    /**
     * Customer addresses collection
     *
     * @var Mage_Customer_Model_Entity_Address_Collection
     */
    protected $_addressesCollection;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    private static $_isConfirmationRequired;

    function _construct()
    {
        $this->_init('customer/customer');
    }

    /**
     * Retrieve customer sharing configuration model
     *
     * @return Mage_Customer_Model_Config_Share
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('customer/config_share');

    }

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @return true
     * @throws Exception
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }
        Mage::dispatchEvent('customer_customer_authenticated', array(
           'model'    => $this,
           'password' => $password,
        ));
        return true;
    }

    /**
     * Load customer by email
     *
     * @param   string $customerEmail
     * @return  Mage_Customer_Model_Customer
     */
    public function loadByEmail($customerEmail)
    {
        $this->_getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }


    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  this
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        if ($this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }
        $name .= $this->getFirstname();
        if ($this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }
        $name .=  ' ' . $this->getLastname();
        if ($this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        return $name;
    }

    /**
     * Add address to address collection
     *
     * @param   Mage_Customer_Model_Address $address
     * @return  Mage_Customer_Model_Customer
     */
    public function addAddress(Mage_Customer_Model_Address $address)
    {
        $this->getAddressesCollection()->addItem($address);
        $this->getAddresses();
        $this->_addresses[] = $address;
        return $this;
    }

    /**
     * Retrieve customer address by address id
     *
     * @param   int $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddressById($addressId)
    {
        return Mage::getModel('customer/address')
            ->load($addressId);
    }

    /**
     * Getting customer address object from collection by identifier
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     */
    public function getAddressItemById($addressId)
    {
        return $this->getAddressesCollection()->getItemById($addressId);
    }

    /**
     * Retrieve not loaded address collection
     *
     * @return Mage_Customer_Model_Entity_Address_Collection
     */
    public function getAddressCollection()
    {
        return Mage::getResourceModel('customer/address_collection');
    }

    /**
     * Customer addresses collection
     *
     * @return Mage_Customer_Model_Entity_Address_Collection
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addressesCollection)) {
            $this->_addressesCollection = $this->getAddressCollection()
                ->setCustomerFilter($this)
                ->addAttributeToSelect('*')
                ->load();
        }

        return $this->_addressesCollection;
    }

    /**
     * Retrieve customer address array
     *
     * @return array
     */
    public function getAddresses()
    {
        $this->_addresses = $this->getAddressesCollection()->getItems();
        return $this->_addresses;
    }

    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (null === $this->_attributes) {
            $this->_attributes = $this->_getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Get customer attribute model object
     *
     * @param   string $attributeCode
     * @return  Mage_Customer_Model_Entity_Attribute || null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Mage_Customer_Model_Customer
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @return  string
     */
    public function hashPassword($password, $salt=null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length=6)
    {
        return substr(md5(uniqid(rand(), true)), 0, $length);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        if (!($hash = $this->getPasswordHash())) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }


    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

    /**
     * Retrieve default address by type(attribute)
     *
     * @param   string $attributeCode address type attribute code
     * @return  Mage_Customer_Model_Address
     */
    public function getPrimaryAddress($attributeCode)
    {
        $primaryAddress = $this->getAddressesCollection()->getItemById($this->getData($attributeCode));

        return $primaryAddress ? $primaryAddress : false;
    }

    /**
     * Get customer default billing address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getPrimaryAddress('default_billing');
    }

    /**
     * Get customer default billing address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getDefaultBillingAddress()
    {
        return $this->getPrimaryBillingAddress();
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getPrimaryAddress('default_shipping');
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     */
    public function getDefaultShippingAddress()
    {
        return $this->getPrimaryShippingAddress();
    }

    /**
     * Retrieve ids of default addresses
     *
     * @return unknown
     */
    public function getPrimaryAddressIds()
    {
        $ids = array();
        if ($this->getDefaultBilling()) {
            $ids[] = $this->getDefaultBilling();
        }
        if ($this->getDefaultShipping()) {
            $ids[] = $this->getDefaultShipping();
        }
        return $ids;
    }

    /**
     * Retrieve all customer default addresses
     *
     * @return array
     */
    public function getPrimaryAddresses()
    {
        $addresses = array();
        $primaryBilling = $this->getPrimaryBillingAddress();
        if ($primaryBilling) {
            $addresses[] = $primaryBilling;
            $primaryBilling->setIsPrimaryBilling(true);
        }

        $primaryShipping = $this->getPrimaryShippingAddress();
        if ($primaryShipping) {
            if ($primaryBilling->getId() == $primaryShipping->getId()) {
                $primaryBilling->setIsPrimaryShipping(true);
            }
            else {
                $primaryShipping->setIsPrimaryShipping(true);
                $addresses[] = $primaryShipping;
            }
        }
        return $addresses;
    }

    /**
     * Retrieve not default addresses
     *
     * @return array
     */
    public function getAdditionalAddresses()
    {
        $addresses = array();
        $primatyIds = $this->getPrimaryAddressIds();
        foreach ($this->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $primatyIds)) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    public function isAddressPrimary(Mage_Customer_Model_Address $address)
    {
        if (!$address->getId()) {
            return false;
        }
        return ($address->getId() == $this->getDefaultBilling()) || ($address->getId() == $this->getDefaultShipping());
    }

    /**
     * Send email with new account specific information
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE,  // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,   // email with confirmation link
        );
        if (!isset($types[$type])) {
            throw new Exception(Mage::helper('customer')->__('Wrong transactional account email type.'));
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $storeId = ($storeId == '0')?$this->getSendemailStoreId():$storeId;
        if ($this->getWebsiteId() != '0' && $storeId == '0') {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $storeId = current($storeIds);
        }

        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
            ->sendTransactional(
                Mage::getStoreConfig($types[$type], $storeId),
                Mage::getStoreConfig(self::XML_PATH_REGISTER_EMAIL_IDENTITY, $storeId),
                $this->getEmail(),
                $this->getName(),
                array('customer' => $this, 'back_url' => $backUrl));

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Check if accounts confirmation is required in config
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (null === self::$_isConfirmationRequired) {
            self::$_isConfirmationRequired = 1 == Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, ($this->getStoreId() ? $this->getStoreId() : null));
        }
        return self::$_isConfirmationRequired;
    }

    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new customer password
     *
     * @return Mage_Customer_Model_Customer
     */
    public function sendPasswordReminderEmail()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $storeId = $this->getStoreId();
        if ($this->getWebsiteId() != '0' && $storeId == '0') {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $storeId = current($storeIds);
        }

        Mage::getModel('core/email_template')
            ->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY),
                $this->getEmail(),
                $this->getName(),
                array('customer'=>$this)
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     */
    public function getGroupId()
    {
        if (!$this->getData('group_id')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
            $this->setData('group_id', Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $storeId));
        }
        return $this->getData('group_id');
    }

    /**
     * Retrieve customer tax class identifier
     *
     * @return int
     */
    public function getTaxClassId()
    {
        if (!$this->getData('tax_class_id')) {
            $this->setTaxClassId(Mage::getModel('customer/group')->getTaxClassId($this->getGroupId()));
        }
        return $this->getData('tax_class_id');
    }

    /**
     * Check store availability for customer
     *
     * @param   mixed $store
     * @return  bool
     */
    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        }
        else {
            $storeId = $store;
        }
        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    /**
     * Retrieve store where customer was created
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve shared store ids
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if (is_null($ids)) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids = $this->getStore()->getWebsite()->getStoreIds();
            }
            else {
                foreach (Mage::app()->getStores() as $store) {
                    $ids[] = $store->getId();
                }
            }
            $this->setData('shared_store_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrive shared website ids
     *
     * @return array
     */
    public function getSharedWebsiteIds()
    {
        $ids = $this->_getData('shared_website_ids');
        if (is_null($ids)) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids[] = $this->getWebsiteId();
            }
            else {
                foreach (Mage::app()->getWebsites() as $website) {
                    $ids[] = $website->getId();
                }
            }
            $this->setData('shared_website_ids', $ids);
        }
        return $ids;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Customer_Model_Customer
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());
        return $this;
    }

    /**
     * Validate customer attribute values
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('First name can\'t be empty');
        }

        if (!Zend_Validate::is( trim($this->getLastname()) , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Last name can\'t be empty');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s"', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('Password can\'t be empty');
        }
        if ($password && !Zend_Validate::is($password, 'StringLength', array(6))) {
            $errors[] = Mage::helper('customer')->__('Password minimal length must be more %s', 6);
        }
        $confirmation = $this->getConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        if (('req' === Mage::helper('customer/address')->getConfig('dob_show'))
            && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('customer')->__('Date of Birth is required.');
        }
        if (('req' === Mage::helper('customer/address')->getConfig('taxvat_show'))
            && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('customer')->__('TAX/VAT number is required.');
        }
        if (('req' === Mage::helper('customer/address')->getConfig('gender_show'))
            && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('customer')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Importing customer data from text array
     *
     * @param array $row
     * @return uMage_Customer_Model_Customer
     */
    public function importFromTextArray(array $row)
    {
        $this->resetErrors();
        $hlp = Mage::helper('customer');
        $line = $row['i'];
        $row = $row['row'];

        $regions = Mage::getResourceModel('directory/region_collection');
//        $config = Mage::getSingleton('eav/config')->getEntityType('customer');

        $website = Mage::getModel('core/website')->load($row['website_code'], 'code');

        if (!$website->getId()) {
            $this->addError($hlp->__('Invalid website, skipping the record, line: %s', $line));

        } else {
            $row['website_id'] = $website->getWebsiteId();
            $this->setWebsiteId($row['website_id']);
        }

        // Validate Email
        if (empty($row['email'])) {
            $this->addError($hlp->__('Missing email, skipping the record, line: %s', $line));
        } else {
            $this->loadByEmail($row['email']);
        }

        if (empty($row['entity_id'])) {
            if ($this->getData('entity_id')) {
                $this->addError($hlp->__('Customer email (%s) already exists, skipping the record , line: %s', $row['email'], $line));
            }
        } else {
            if ($row['entity_id'] != $this->getData('entity_id')) {
                $this->addError($hlp->__('CustomerID and email didn\'t match, skipping the record , line: %s', $line));
            } else {
                $this->unsetData();
                $this->load($row['entity_id']);
                if (isset($row['store_view'])) {
                    $storeId = Mage::app()->getStore($row['store_view'])->getId();
                    if ($storeId) $this->setStoreId($storeId);
                }
            }
        }

        if (empty($row['website_code'])) {
            $this->addError($hlp->__('Missing website, skipping the record, line: %s', $line));
        }

        if (empty($row['group'])) {
            $row['group'] = 'General';
        }

        if (empty($row['firstname'])) {
            $this->addError($hlp->__('Missing firstname, skipping the record, line: %s', $line));
        }
        if (empty($row['lastname'])) {
            $this->addError($hlp->__('Missing lastname, skipping the record, line: %s', $line));
        }

        if (!empty($row['password_new'])) {
            $this->setPassword($row['password_new']);
            unset($row['password_new']);
            if (!empty($row['password_hash'])) unset($row['password_hash']);
        }

        if ($errors = $this->getErrors()) {
            $this->unsetData();
            $this->printError(join("<br />",$errors));
            return;
        }
//        $entity = $this->getResource();
        foreach ($row as $field=>$value) {

//            $attribute = $entity->getAttribute($field);
//            if (!$attribute) {
//                echo $field;
//                continue;
//            }
//            if ($attribute->usesSource()) {
//                $source = $attribute->getSource();
//                $optionId = $config->getSourceOptionId($source, $value);
//                if (is_null($optionId)) {
//                    $this->printError($hlp->__("Invalid attribute option specified for attribute attribute %s (%s)", $field, $value), $line);
//                }
//                $value = $optionId;
//            }

            $this->setData($field, $value);
        }

        if (!$this->validateAddress($row, 'billing')) {
            $this->printError($hlp->__('Invalid billing address for (%s)', $row['email']), $line);
        } else {
            // Handling billing address
            $billingAddress = $this->getPrimaryBillingAddress();
            if (!$billingAddress  instanceof Mage_Customer_Model_Address) {
                $billingAddress = new Mage_Customer_Model_Address();
            }

            $regions->addRegionNameFilter($row['billing_region'])->load();
            if ($regions) foreach($regions as $region) {
                $regionId = $region->getId();
            }

            $billingAddress->setFirstname($row['firstname']);
            $billingAddress->setLastname($row['lastname']);
            $billingAddress->setCity($row['billing_city']);
            $billingAddress->setRegion($row['billing_region']);
            if (isset($regionId)) $billingAddress->setRegionId($regionId);
            $billingAddress->setCountryId($row['billing_country']);
            $billingAddress->setPostcode($row['billing_postcode']);
            if (isset($row['billing_street2'])) {
                $billingAddress->setStreet(array($row['billing_street1'],$row['billing_street2']));
            } else {
                $billingAddress->setStreet(array($row['billing_street1']));
            }
            if (isset($row['billing_telephone'])) {
                $billingAddress->setTelephone($row['billing_telephone']);
            }

            if (!$billingAddress->getId()) {
                $billingAddress->setIsDefaultBilling(true);
                if ($this->getDefaultBilling()) {
                    $this->setData('default_billing', '');
                }
                $this->addAddress($billingAddress);
            } // End handling billing address
        }

        if (!$this->validateAddress($row, 'shipping')) {
            $this->printError($hlp->__('Invalid shipping address for (%s)', $row['email']), $line);
        } else {
            // Handling shipping address
            $shippingAddress = $this->getPrimaryShippingAddress();
            if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
                $shippingAddress = new Mage_Customer_Model_Address();
            }

            $regions->addRegionNameFilter($row['shipping_region'])->load();

            if ($regions) foreach($regions as $region) {
               $regionId = $region->getId();
            }

            $shippingAddress->setFirstname($row['firstname']);
            $shippingAddress->setLastname($row['lastname']);
            $shippingAddress->setCity($row['shipping_city']);
            $shippingAddress->setRegion($row['shipping_region']);
            if (isset($regionId)) $shippingAddress->setRegionId($regionId);
            $shippingAddress->setCountryId($row['shipping_country']);
            $shippingAddress->setPostcode($row['shipping_postcode']);
            if (isset($row['shipping_street2'])) {
                $shippingAddress->setStreet(array($row['shipping_street1'], $row['shipping_street2']));
            } else {
                $shippingAddress->setStreet(array($row['shipping_street1']));
            }
            if (!empty($row['shipping_telephone'])) {
                $shippingAddress->setTelephone($row['shipping_telephone']);
            }

            if (!$shippingAddress->getId()) {
               $shippingAddress->setIsDefaultShipping(true);
               $this->addAddress($shippingAddress);
            }
            // End handling shipping address
        }
        if (!empty($row['is_subscribed'])) {
            $this->setIsSubscribed(strtolower($row['is_subscribed'])==self::SUBSCRIBED_YES ? 1 : 0);
        }
        unset($row);
        return $this;
    }

    function unsetSubscription()
    {
        if (isset($this->_isSubscribed)) {
            unset($this->_isSubscribed);
        }
    }

    function cleanAllAddresses() {
        $this->_addressesCollection = null;
        $this->_addresses           = null;
    }

    function addError($error)
    {
        $this->_errors[] = $error;
    }

    function getErrors()
    {
        return $this->_errors;
    }

    function resetErrors()
    {
        $this->_errors = array();
    }

    function printError($error, $line = null)
    {
        if ($error == null) return false;
        $img = 'error_msg_icon.gif';
        $liStyle = 'background-color:#FDD; ';
        echo '<li style="'.$liStyle.'">';
        echo '<img src="'.Mage::getDesign()->getSkinUrl('images/'.$img).'" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>'.$line.'</b></small>';
        }
        echo "</li>";
    }

    function validateAddress(array $data, $type = 'billing')
    {
        $fields = array('city',
            'country', 'postcode',
            'telephone', 'street1');
        $usca = array('US', 'CA');
        $prefix = $type ? $type.'_':'';

        if ($data) {
            foreach($fields as $field) {
                if (!isset($data[$prefix.$field])) {
                    return false;
                }
                if ($field == 'country'
                    && in_array(strtolower($data[$prefix.$field]), array('US', 'CA'))) {

                    if (!isset($data[$prefix.'region'])) {
                        return false;
                    }

                    $region = Mage::getModel('directory/region')
                        ->loadByName($data[$prefix.'region']);
                    if (!$region->getId()) {
                        return false;
                    }
                    unset($region);
                }
            }
            unset($data);
            return true;
        }
        return false;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Get customer created at date timestamp
     *
     * @return int
     */
    public function getCreatedAtTimestamp()
    {
        if ($date = $this->getCreatedAt()) {
            return $this->_getResource()->mktime($date);
        }
        return null;
    }

    /**
     * Reset all model data
     *
     * @return Mage_Customer_Model_Customer
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }

    /**
     * Checks model is deleteable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (boolean) $value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Customer_Model_Customer
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (boolean) $value;
        return $this;
    }

    /**
     * Check whether confirmation may be skipped when registering using certain email address
     *
     * @return bool
     */
    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
            && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    }

    public function __clone()
    {
        $newAddressCollection = $this->getPrimaryAddresses();
        $newAddressCollection = array_merge($newAddressCollection, $this->getAdditionalAddresses());
        $this->setId(null);
        $this->cleanAllAddresses();
        foreach ($newAddressCollection as $address) {
            $this->addAddress(clone $address);
        }
    }
}
