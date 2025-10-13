<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer model
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Customer getResource()
 * @method Mage_Customer_Model_Resource_Customer _getResource()
 * @method Mage_Customer_Model_Resource_Customer_Collection getCollection()
 *
 * @method $this setChangePassword(int $value)
 * @method string getCompany()
 * @method bool getConfirmation()
 * @method $this setConfirmation(bool|null $value)
 * @method string getCreatedAt()
 * @method int getCustomerId()
 * @method $this setCustomerId(int|null $value)
 *
 * @method int|null getDefaultBilling()
 * @method $this setDefaultBilling(int|null $value)
 * @method $this unsetDefaultBilling()
 * @method int|null getDefaultShipping()
 * @method $this setDefaultShipping(int|null $value)
 * @method $this unsetDefaultShipping()
 * @method int getDisableAutoGroupChange()
 * @method string getDob()
 * @method $this setDob(string  $value)
 *
 * @method string getEmail()
 * @method $this setEmail(string $value)
 *
 * @method string getFirstname()
 * @method $this setFirstname(string $value)
 * @method bool getForceConfirmed()
 * @method $this setForceConfirmed(bool $value)
 *
 * @method string getGender()
 * @method $this setGroupId(int $value)
 *
 * @method bool getImportMode()
 * @method $this setImportMode(bool $value)
 * @method int getIncrementId()
 * @method bool getIsChangeEmail()
 * @method $this setIsChangeEmail(bool $value)
 * @method bool getIsChangePassword()
 * @method $this setIsChangePassword(bool $value)
 * @method bool getIsJustConfirmed()
 * @method $this setIsJustConfirmed(bool $value)
 * @method bool hasIsSubscribed()
 * @method bool getIsSubscribed()
 * @method $this setIsSubscribed(bool $value)
 * @method $this setItems(int $value)
 *
 * @method string getLastname()
 * @method $this setLastname(string $value)
 *
 * @method string getMiddlename()
 * @method string getMode()
 * @method $this setMode(bool $value)
 *
 * @method string getOldEmail()
 * @method $this setOldEmail(string $value)
 *
 * @method string getPasswordConfirm()
 * @method string getPasswordConfirmation()
 * @method $this setPasswordConfirmation(string $value)
 * @method int getPasswordCreatedAt()
 * @method string getPasswordHash()
 * @method $this setPasswordHash(string $value)
 * @method string getPrefix()
 *
 * @method $this setRpCustomerId(string $value)
 * @method string getRpToken()
 * @method $this setRpToken(string $value)
 * @method string getRpTokenCreatedAt()
 * @method $this setRpTokenCreatedAt(string $value)
 *
 * @method string getSendemailStoreId()
 * @method setSendemailStoreId(string $value)
 * @method bool hasSkipConfirmationIfEmail()
 * @method string getSkipConfirmationIfEmail()
 * @method bool hasStoreId()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getSuffix()
 *
 * @method int getTagId()
 * @method $this setTaxClassId(bool $value)
 * @method string getTaxvat()
 * @method $this setTotal(float $value)
 *
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 */
class Mage_Customer_Model_Customer extends Mage_Core_Model_Abstract
{
    /**
     * Configuration paths for email templates and identities
     */
    public const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';

    public const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';

    public const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';

    public const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'customer/password/forgot_email_template';

    public const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';

    public const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'customer/create_account/email_domain';

    public const XML_PATH_IS_CONFIRM                   = 'customer/create_account/confirm';

    public const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'customer/create_account/email_confirmation_template';

    public const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'customer/create_account/email_confirmed_template';

    public const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'customer/create_account/generate_human_friendly_id';

    public const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE = 'customer/changed_account/password_or_email_template';

    public const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY = 'customer/changed_account/password_or_email_identity';

    public const XML_PATH_PASSWORD_LINK_ACCOUNT_NEW_EMAIL_TEMPLATE = 'customer/password_link/account_new_email_template';

    public const XML_PATH_PASSWORD_LINK_EMAIL_TEMPLATE = 'customer/password_link/email_template';

    public const XML_PATH_PASSWORD_LINK_EMAIL_IDENTITY = 'customer/password_link/email_identity';

    /**
     * Codes of exceptions related to customer model
     */
    public const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;

    public const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;

    public const EXCEPTION_EMAIL_EXISTS              = 3;

    public const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;

    public const EXCEPTION_INVALID_RESET_PASSWORD_LINK_CUSTOMER_ID = 5;

    /**
     * Subscriptions
     */
    public const SUBSCRIBED_YES = 'yes';

    public const SUBSCRIBED_NO  = 'no';

    public const CACHE_TAG = 'customer';

    /**
     * Minimum Password Length
     * @deprecated Use getMinPasswordLength() method instead
     */
    public const MINIMUM_PASSWORD_LENGTH = Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH;

    /**
     * Configuration path for minimum length of password
     */
    public const XML_PATH_MIN_PASSWORD_LENGTH = 'customer/password/min_password_length';

    /**
     * Maximum Password Length
     */
    public const MAXIMUM_PASSWORD_LENGTH = 256;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'customer';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'customer';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Assoc array of customer attributes
     *
     * @var array|null
     */
    protected $_attributes;

    /**
     * Customer addresses array
     *
     * @var Mage_Customer_Model_Address[]|null
     * @deprecated after 1.4.0.0-rc1
     */
    protected $_addresses = null;

    /**
     * Customer addresses collection
     *
     * @var Mage_Customer_Model_Resource_Address_Collection|null
     */
    protected $_addressesCollection;

    /**
     * Is model deleteable
     *
     * @var bool
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var bool
     */
    protected $_isReadonly = false;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Confirmation requirement flag
     *
     * @var bool
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private static $_isConfirmationRequired;

    /**
     * Initialize customer model
     */
    public function _construct()
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
     * @throws Mage_Core_Exception
     * @return true
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED,
            );
        }

        if (!$this->validatePassword($password)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('customer')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD,
            );
        }

        Mage::dispatchEvent('customer_customer_authenticated', [
            'model'    => $this,
            'password' => $password,
        ]);

        return true;
    }

    /**
     * Load customer by email
     *
     * @param string $customerEmail
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByEmail($customerEmail)
    {
        $this->_getResource()->loadByEmail($this, $customerEmail);
        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if ($storeId === null) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    /**
     * Change customer password
     *
     * @param   string $newPassword
     * @return  $this
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Set time when password was changed to invalidate other sessions
     *
     * @param int $time
     * @return $this
     */
    public function setPasswordCreatedAt($time)
    {
        $this->setData('password_created_at', $time);
        if (session_status() === PHP_SESSION_ACTIVE) {
            Mage::getSingleton('checkout/session')->setValidatorSessionRenewTimestamp($time);
        }

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
        $config = Mage::getSingleton('eav/config');
        if ($config->getAttribute('customer', 'prefix')->getIsVisible() && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }

        $name .= $this->getFirstname();
        if ($config->getAttribute('customer', 'middlename')->getIsVisible() && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }

        $name .=  ' ' . $this->getLastname();
        if ($config->getAttribute('customer', 'suffix')->getIsVisible() && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }

        return $name;
    }

    /**
     * Add address to address collection
     *
     * @return $this
     * @throws Mage_Core_Exception
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
     * @param   int|null $addressId
     * @return  Mage_Customer_Model_Address
     */
    public function getAddressById($addressId)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if ($this->getId() == $address->getParentId()) {
            return $address;
        }

        return Mage::getModel('customer/address');
    }

    /**
     * Getting customer address object from collection by identifier
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     * @throws Mage_Core_Exception
     */
    public function getAddressItemById($addressId)
    {
        return $this->getAddressesCollection()->getItemById($addressId);
    }

    /**
     * Retrieve not loaded address collection
     *
     * @return Mage_Customer_Model_Resource_Address_Collection
     */
    public function getAddressCollection()
    {
        return Mage::getResourceModel('customer/address_collection');
    }

    /**
     * Customer addresses collection
     *
     * @return Mage_Customer_Model_Resource_Address_Collection
     * @throws Mage_Core_Exception
     */
    public function getAddressesCollection()
    {
        if ($this->_addressesCollection === null) {
            $this->_addressesCollection = $this->getAddressCollection()
                ->setCustomerFilter($this)
                ->addAttributeToSelect('*')
                ->setOrder('entity_id', 'desc');
            foreach ($this->_addressesCollection as $address) {
                $address->setCustomer($this);
                $address->setDataChanges(false);
            }
        }

        return $this->_addressesCollection;
    }

    /**
     * Retrieve customer address array
     *
     * @return Mage_Customer_Model_Address[]
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();
        }

        return $this->_attributes;
    }

    /**
     * Get customer attribute model object
     *
     * @param string $attributeCode
     * @return Mage_Customer_Model_Customer|null
     * @throws Mage_Core_Exception
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        return $this->_attributes[$attributeCode] ?? null;
    }

    public function getPassword(): string
    {
        return (string) $this->_getData('password');
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }

    /**
     * Hash customer password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->_getHelper('core');
        return $helper->getHash(trim($password), (bool) $salt ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }

    /**
     * Get helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $minPasswordLength = $this->getMinPasswordLength();
        if ($minPasswordLength > $length) {
            $length = $minPasswordLength;
        }

        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();
        if (!$hash) {
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
     * @param string $attributeCode address type attribute code
     * @return Mage_Customer_Model_Address|false
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getPrimaryAddress('default_billing');
    }

    /**
     * Get customer default billing address
     *
     * @return Mage_Customer_Model_Address
     * @throws Mage_Core_Exception
     */
    public function getDefaultBillingAddress()
    {
        return $this->getPrimaryBillingAddress();
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     * @throws Mage_Core_Exception
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getPrimaryAddress('default_shipping');
    }

    /**
     * Get default customer shipping address
     *
     * @return Mage_Customer_Model_Address
     * @throws Mage_Core_Exception
     */
    public function getDefaultShippingAddress()
    {
        return $this->getPrimaryShippingAddress();
    }

    /**
     * Retrieve ids of default addresses
     *
     * @return array
     */
    public function getPrimaryAddressIds()
    {
        $ids = [];
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
     * @return Mage_Customer_Model_Address[]
     * @throws Mage_Core_Exception
     */
    public function getPrimaryAddresses()
    {
        $addresses = [];
        $primaryBilling = $this->getPrimaryBillingAddress();
        if ($primaryBilling) {
            $addresses[] = $primaryBilling;
            $primaryBilling->setIsPrimaryBilling(true);
        }

        $primaryShipping = $this->getPrimaryShippingAddress();
        if ($primaryShipping) {
            if ($primaryBilling && $primaryBilling->getId() == $primaryShipping->getId()) {
                $primaryBilling->setIsPrimaryShipping(true);
            } else {
                $primaryShipping->setIsPrimaryShipping(true);
                $addresses[] = $primaryShipping;
            }
        }

        return $addresses;
    }

    /**
     * Retrieve not default addresses
     *
     * @return Mage_Customer_Model_Address[]
     * @throws Mage_Core_Exception
     */
    public function getAdditionalAddresses()
    {
        $addresses = [];
        $primatyIds = $this->getPrimaryAddressIds();
        foreach ($this->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $primatyIds)) {
                $addresses[] = $address;
            }
        }

        return $addresses;
    }

    /**
     * Check if address is primary
     *
     * @return bool
     */
    public function isAddressPrimary(Mage_Customer_Model_Address $address)
    {
        if (!$address->getId()) {
            return false;
        }

        return ($address->getId() == $this->getDefaultBilling()) || ($address->getId() == $this->getDefaultShipping());
    }

    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string|int $storeId
     * @param string $password
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0', $password = null)
    {
        $types = [
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        ];
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('customer')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        $this->_sendEmailTemplate(
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            ['customer' => $this, 'back_url' => $backUrl],
            $storeId,
        );
        $this->cleanPasswordsValidationData();

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

        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ?: null;
            self::$_isConfirmationRequired = Mage::getStoreConfigFlag(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }

    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new customer password
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function sendPasswordReminderEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_REMIND_EMAIL_TEMPLATE,
            self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            ['customer' => $this],
            $storeId,
        );

        return $this;
    }

    /**
     * Send info email about changed password or email
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function sendChangedPasswordOrEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE,
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY,
            ['customer' => $this],
            $storeId,
            $this->getOldEmail(),
        );

        return $this;
    }

    /**
     * Send corresponding email template
     *
     * @param string $template configuration path of email template
     * @param string $sender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string|null $customerEmail
     * @return $this
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = [], $storeId = null, $customerEmail = null)
    {
        $customerEmail = ($customerEmail) ? $customerEmail : $this->getEmail();
        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($customerEmail, $this->getName());

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_FORGOT_EMAIL_TEMPLATE,
            self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            ['customer' => $this],
            $storeId,
        );

        return $this;
    }

    /**
     * Send email with link to set password
     *
     * @bool $isNew Send welcome email?
     * @return $this
     */
    public function sendPasswordLinkEmail(bool $isNew = false)
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        /** @var Mage_Customer_Helper_Data $helper */
        $helper = Mage::helper('customer');
        $newResetPasswordLinkToken = $helper->generateResetPasswordLinkToken();
        $newResetPasswordLinkCustomerId = $helper->generateResetPasswordLinkCustomerId($this->getId());
        $this->changeResetPasswordLinkCustomerId($newResetPasswordLinkCustomerId);
        $this->changeResetPasswordLinkToken($newResetPasswordLinkToken);

        $template = $isNew
            ? self::XML_PATH_PASSWORD_LINK_ACCOUNT_NEW_EMAIL_TEMPLATE
            : self::XML_PATH_PASSWORD_LINK_EMAIL_TEMPLATE;

        $this->_sendEmailTemplate(
            $template,
            self::XML_PATH_PASSWORD_LINK_EMAIL_IDENTITY,
            ['customer' => $this],
            $storeId,
        );

        return $this;
    }

    /**
     * Retrieve customer group identifier
     *
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getGroupId()
    {
        if (!$this->hasData('group_id')) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
            $groupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, $storeId);
            $this->setData('group_id', $groupId);
        }

        return $this->getData('group_id');
    }

    /**
     * Retrieve customer tax class identifier
     *
     * @return int
     * @throws Mage_Core_Model_Store_Exception
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
     * @param Mage_Core_Model_Store|int $store
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        } else {
            $storeId = $store;
        }

        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    /**
     * Retrieve store where customer was created
     *
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve shared store ids
     *
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if ($ids === null) {
            $ids = [];
            if ((bool) $this->getSharingConfig()->isWebsiteScope()) {
                $ids = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $ids[] = $store->getId();
                }
            }

            $this->setData('shared_store_ids', $ids);
        }

        return $ids;
    }

    /**
     * Retrieve shared website ids
     *
     * @return array
     */
    public function getSharedWebsiteIds()
    {
        $ids = $this->_getData('shared_website_ids');
        if ($ids === null) {
            $ids = [];
            if ((bool) $this->getSharingConfig()->isWebsiteScope()) {
                $ids[] = $this->getWebsiteId();
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    $ids[] = $website->getId();
                }
            }

            $this->setData('shared_website_ids', $ids);
        }

        return $ids;
    }

    /**
     * Set store to customer
     *
     * @return $this
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());
        return $this;
    }

    /**
     * Validate customer attribute values.
     * For existing customer password + confirmation will be validated
     * only when password is set (i.e. its change is requested)
     *
     * @return array|true
     * @throws Mage_Core_Exception
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = [];
        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }

        $minPasswordLength = $this->getMinPasswordLength();
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', [$minPasswordLength])) {
            $errors[] = Mage::helper('customer')
                ->__('The minimum password length is %s', $minPasswordLength);
        }

        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', ['max' => self::MAXIMUM_PASSWORD_LENGTH])) {
            $errors[] = Mage::helper('customer')
                ->__('Please enter a password with at most %s characters.', self::MAXIMUM_PASSWORD_LENGTH);
        }

        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('customer');
        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && trim($this->getDob()) == '') {
            $errors[] = Mage::helper('customer')->__('The Date of Birth is required.');
        }

        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && trim($this->getTaxvat()) == '') {
            $errors[] = Mage::helper('customer')->__('The TAX/VAT number is required.');
        }

        $attribute = Mage::getModel('customer/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && trim($this->getGender()) == '') {
            $errors[] = Mage::helper('customer')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Validate customer password on reset
     * @return array|true
     * @throws Zend_Validate_Exception
     */
    public function validateResetPassword()
    {
        $errors   = [];
        $password = $this->getPassword();
        if (!Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('customer')->__('The password cannot be empty.');
        }

        $minPasswordLength = $this->getMinPasswordLength();
        if (!Zend_Validate::is($password, 'StringLength', [$minPasswordLength])) {
            $errors[] = Mage::helper('customer')
                ->__('The minimum password length is %s', $minPasswordLength);
        }

        if (!Zend_Validate::is($password, 'StringLength', ['max' => self::MAXIMUM_PASSWORD_LENGTH])) {
            $errors[] = Mage::helper('customer')
                ->__('Please enter a password with at most %s characters.', self::MAXIMUM_PASSWORD_LENGTH);
        }

        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Import customer data from text array
     *
     * @return $this|null
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function importFromTextArray(array $row)
    {
        $this->resetErrors();
        $line = $row['i'];
        $row = $row['row'];

        $regions = Mage::getResourceModel('directory/region_collection');

        $website = Mage::getModel('core/website')->load($row['website_code'], 'code');

        if (!$website->getId()) {
            $this->addError(Mage::helper('customer')->__('Invalid website, skipping the record, line: %s', $line));
        } else {
            $row['website_id'] = $website->getWebsiteId();
            $this->setWebsiteId($row['website_id']);
        }

        // Validate Email
        if (empty($row['email'])) {
            $this->addError(Mage::helper('customer')->__('Missing email, skipping the record, line: %s', $line));
        } else {
            $this->loadByEmail($row['email']);
        }

        if (empty($row['entity_id'])) {
            if ($this->getData('entity_id')) {
                $this->addError(Mage::helper('customer')->__(
                    'The customer email (%s) already exists, skipping the record, line: %s',
                    $row['email'],
                    $line,
                ));
            }
        } elseif ($row['entity_id'] != $this->getData('entity_id')) {
            $this->addError(Mage::helper('customer')->__(
                'The customer ID and email did not match, skipping the record, line: %s',
                $line,
            ));
        } else {
            $this->unsetData();
            $this->load($row['entity_id']);
            if (isset($row['store_view'])) {
                $storeId = Mage::app()->getStore($row['store_view'])->getId();
                if ($storeId) {
                    $this->setStoreId($storeId);
                }
            }
        }

        if (empty($row['website_code'])) {
            $this->addError(Mage::helper('customer')->__('Missing website, skipping the record, line: %s', $line));
        }

        if (empty($row['group'])) {
            $row['group'] = 'General';
        }

        if (empty($row['firstname'])) {
            $this->addError(Mage::helper('customer')->__('Missing first name, skipping the record, line: %s', $line));
        }

        if (empty($row['lastname'])) {
            $this->addError(Mage::helper('customer')->__('Missing last name, skipping the record, line: %s', $line));
        }

        if (!empty($row['password_new'])) {
            $this->setPassword($row['password_new']);
            unset($row['password_new']);
            if (!empty($row['password_hash'])) {
                unset($row['password_hash']);
            }
        }

        $errors = $this->getErrors();
        if ($errors) {
            $this->unsetData();
            $this->printError(implode('<br />', $errors));
            return null;
        }

        foreach ($row as $field => $value) {
            $this->setData($field, $value);
        }

        if (!$this->validateAddress($row, 'billing')) {
            $this->printError(Mage::helper('customer')->__('Invalid billing address for (%s)', $row['email']), $line);
        } else {
            // Handling billing address
            $billingAddress = $this->getPrimaryBillingAddress();
            if (!$billingAddress  instanceof Mage_Customer_Model_Address) {
                $billingAddress = Mage::getModel('customer/address');
            }

            $regions->addRegionNameFilter($row['billing_region'])->load();
            if ($regions) {
                foreach ($regions as $region) {
                    $regionId = (int) $region->getId();
                }
            }

            $billingAddress->setFirstname($row['firstname']);
            $billingAddress->setLastname($row['lastname']);
            $billingAddress->setCity($row['billing_city']);
            $billingAddress->setRegion($row['billing_region']);
            if (isset($regionId)) {
                $billingAddress->setRegionId($regionId);
            }

            $billingAddress->setCountryId($row['billing_country']);
            $billingAddress->setPostcode($row['billing_postcode']);
            if (isset($row['billing_street2'])) {
                $billingAddress->setStreet([$row['billing_street1'], $row['billing_street2']]);
            } else {
                $billingAddress->setStreet([$row['billing_street1']]);
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
            $this->printError(Mage::helper('customer')->__('Invalid shipping address for (%s)', $row['email']), $line);
        } else {
            // Handling shipping address
            $shippingAddress = $this->getPrimaryShippingAddress();
            if (!$shippingAddress instanceof Mage_Customer_Model_Address) {
                $shippingAddress = Mage::getModel('customer/address');
            }

            $regions->addRegionNameFilter($row['shipping_region'])->load();

            if ($regions) {
                foreach ($regions as $region) {
                    $regionId = (int) $region->getId();
                }
            }

            $shippingAddress->setFirstname($row['firstname']);
            $shippingAddress->setLastname($row['lastname']);
            $shippingAddress->setCity($row['shipping_city']);
            $shippingAddress->setRegion($row['shipping_region']);
            if (isset($regionId)) {
                $shippingAddress->setRegionId($regionId);
            }

            $shippingAddress->setCountryId($row['shipping_country']);
            $shippingAddress->setPostcode($row['shipping_postcode']);
            if (isset($row['shipping_street2'])) {
                $shippingAddress->setStreet([$row['shipping_street1'], $row['shipping_street2']]);
            } else {
                $shippingAddress->setStreet([$row['shipping_street1']]);
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
            $isSubscribed = (bool) strtolower($row['is_subscribed']) == self::SUBSCRIBED_YES;
            $this->setIsSubscribed($isSubscribed);
        }

        unset($row);
        return $this;
    }

    /**
     * Unset subscription
     *
     * @return $this
     */
    public function unsetSubscription()
    {
        if (isset($this->_isSubscribed)) {
            unset($this->_isSubscribed);
        }

        return $this;
    }

    /**
     * Clean all addresses
     *
     */
    public function cleanAllAddresses()
    {
        $this->_addressesCollection = null;
        $this->_addresses           = null;
    }

    /**
     * Add error
     *
     * @param string $error
     * @return $this
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retrieve errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Reset errors array
     *
     * @return $this
     */
    public function resetErrors()
    {
        $this->_errors = [];
        return $this;
    }

    /**
     * Print error
     *
     * @param null|string $error
     * @param string $line
     * @return false|void
     * @throws Exception
     */
    public function printError($error, $line = null)
    {
        if ($error == null) {
            return false;
        }

        $liStyle = 'background-color: #FDD; ';
        echo '<li style="' . $liStyle . '">';
        echo '<img src="' . Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif') . '" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>' . $line . '</b></small>';
        }

        echo '</li>';
    }

    /**
     * Validate address
     *
     * @param string $type
     * @return bool
     */
    public function validateAddress(array $data, $type = 'billing')
    {
        $fields = ['city', 'country', 'postcode', 'telephone', 'street1'];
        $usca   = ['US', 'CA'];
        $prefix = $type ? $type . '_' : '';

        if ($data) {
            foreach ($fields as $field) {
                if (!isset($data[$prefix . $field])) {
                    return false;
                }

                if ($field == 'country'
                    && in_array(strtolower($data[$prefix . $field]), ['US', 'CA'])
                ) {
                    if (!isset($data[$prefix . 'region'])) {
                        return false;
                    }

                    $region = Mage::getModel('directory/region')->loadByName($data[$prefix . 'region'], $data[$prefix . $field]);
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

    /**
     * Prepare customer for delete
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Get customer created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }

        return null;
    }

    /**
     * Reset all model data
     *
     * @return $this
     */
    public function reset()
    {
        $this->setData([]);
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }

    /**
     * Checks model is deleteable
     *
     * @return bool
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool) $value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param bool $value
     * @return $this
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool) $value;
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

    /**
     * Clone current object
     */
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

    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     * @throws Mage_Core_Exception
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     * @throws Mage_Core_Exception
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
     * Get either first store ID from a set website or the provided as default
     *
     * @param int|string|null $defaultStoreId
     * @return int
     * @throws Mage_Core_Exception
     */
    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }

        return $defaultStoreId;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param string $newResetPasswordLinkToken
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken)
    {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('customer')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN,
            );
        }

        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }

    /**
     * Change reset password link customer Id
     *
     * Stores new reset password link customer Id
     *
     * @param string $newResetPasswordLinkCustomerId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function changeResetPasswordLinkCustomerId($newResetPasswordLinkCustomerId)
    {
        if (!is_string($newResetPasswordLinkCustomerId) || empty($newResetPasswordLinkCustomerId)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('customer')->__('Invalid password reset customer Id.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_CUSTOMER_ID,
            );
        }

        $this->_getResource()->changeResetPasswordLinkCustomerId($this, $newResetPasswordLinkCustomerId);
        return $this;
    }

    /**
     * Check if current reset password link token is expired
     *
     * @return bool
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('customer')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $hoursDifference = floor(($currentTimestamp - $tokenTimestamp) / (60 * 60));

        return $hoursDifference >= $tokenExpirationPeriod;
    }

    /**
     * Clean password's validation data (password, password_confirmation)
     *
     * @return $this
     */
    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }

    /**
     * Retrieve minimum length of password
     *
     * @return int
     */
    public function getMinPasswordLength()
    {
        $minLength = Mage::getStoreConfigAsInt(self::XML_PATH_MIN_PASSWORD_LENGTH);
        $absoluteMinLength = Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH;
        return max($absoluteMinLength, $minLength);
    }
}
