<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Admin user model
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_User            _getResource()
 * @method string                                    getCreated()
 * @method string                                    getEmail()
 * @method array                                     getExtra()
 * @method string                                    getFirstname()
 * @method int                                       getIsActive()
 * @method string                                    getLastname()
 * @method string                                    getLogdate()
 * @method int                                       getLognum()
 * @method string                                    getModified()
 * @method string                                    getNewPassword()
 * @method string                                    getPassword()
 * @method string                                    getPasswordConfirmation()
 * @method int                                       getReloadAclFlag()
 * @method Mage_Admin_Model_Resource_User            getResource()
 * @method Mage_Admin_Model_Resource_User_Collection getResourceCollection()
 * @method int                                       getRoleId()
 * @method array                                     getRoleIds()
 * @method string                                    getRpToken()
 * @method string                                    getRpTokenCreatedAt()
 * @method int                                       getUserId()
 * @method string                                    getUsername()
 * @method bool                                      hasNewPassword()
 * @method bool                                      hasPassword()
 * @method bool                                      hasPasswordConfirmation()
 * @method $this                                     setCreated(string $value)
 * @method $this                                     setEmail(string $value)
 * @method $this                                     setExtra(string $value)
 * @method $this                                     setFirstname(string $value)
 * @method $this                                     setIsActive(int $value)
 * @method $this                                     setLastname(string $value)
 * @method $this                                     setLogdate(string $value)
 * @method $this                                     setLognum(int $value)
 * @method $this                                     setModified(string $value)
 * @method $this                                     setNewPassword(string $value)
 * @method $this                                     setPassword(string $value)
 * @method $this                                     setPasswordConfirmation(string $value)
 * @method $this                                     setReloadAclFlag(int $value)
 * @method $this                                     setRoleId(int $value)
 * @method $this                                     setRoleIds(array $value)
 * @method $this                                     setRoleUserId(int $value)
 * @method $this                                     setRpToken(string $value)
 * @method $this                                     setRpTokenCreatedAt(string $value)
 * @method $this                                     setUserId(int $value)
 * @method $this                                     setUsername(string $value)
 * @method $this                                     unsNewPassword()
 * @method $this                                     unsPasswordConfirmation()
 */
class Mage_Admin_Model_User extends Mage_Core_Model_Abstract
{
    /**
     * Configuration paths for email templates and identities
     */
    public const XML_PATH_FORGOT_EMAIL_TEMPLATE    = 'admin/emails/forgot_email_template';

    public const XML_PATH_FORGOT_EMAIL_IDENTITY    = 'admin/emails/forgot_email_identity';

    public const XML_PATH_STARTUP_PAGE             = 'admin/startup/page';

    /** Configuration paths for notifications */
    public const XML_PATH_ADDITIONAL_EMAILS             = 'general/additional_notification_emails/admin_user_create';

    public const XML_PATH_NOTIFICATION_EMAILS_TEMPLATE  = 'admin/emails/admin_notification_email_template';

    /**
     * Minimum length of admin password
     * @deprecated Use getMinAdminPasswordLength() method instead
     */
    public const MIN_PASSWORD_LENGTH = 14;

    /**
     * Configuration path for minimum length of admin password
     */
    public const XML_PATH_MIN_ADMIN_PASSWORD_LENGTH = 'admin/security/min_admin_password_length';

    /**
     * Length of salt
     */
    public const HASH_SALT_LENGTH = 32;

    /**
     * Empty hash salt
     */
    public const HASH_SALT_EMPTY = null;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'admin_user';

    /**
     * Admin role
     *
     * @var Mage_Admin_Model_Roles
     */
    protected $_role;

    /**
     * Available resources flag
     *
     * @var bool
     */
    protected $_hasAvailableResources = true;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/user');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        $data = [
            'firstname' => $this->getFirstname(),
            'lastname'  => $this->getLastname(),
            'email'     => $this->getEmail(),
            'modified'  => $this->_getDateNow(),
            'extra'     => serialize($this->getExtra()),
        ];

        if ($this->getId() > 0) {
            $data['user_id'] = $this->getId();
        }

        if ($this->getUsername()) {
            $data['username'] = $this->getUsername();
        }

        if ($this->getNewPassword()) {
            // Change user password
            $data['password'] = $this->_getEncodedPassword($this->getNewPassword());
            $data['new_password'] = $data['password'];
            $sessionUser = $this->getSession()->getUser();
            if ($sessionUser && $sessionUser->getId() == $this->getId()) {
                $this->getSession()->setUserPasswordChanged(true);
            }
        } elseif ($this->getPassword() && $this->getPassword() != $this->getOrigData('password')) {
            // New user password
            $data['password'] = $this->_getEncodedPassword($this->getPassword());
        } elseif (!$this->getPassword() && $this->getOrigData('password') // Change user data
            || $this->getPassword() == $this->getOrigData('password')     // Retrieve user password
        ) {
            $data['password'] = $this->getOrigData('password');
        }

        $this->cleanPasswordsValidationData();

        if (!is_null($this->getIsActive())) {
            $data['is_active'] = (int) $this->getIsActive();
        }

        $this->addData($data);

        return parent::_beforeSave();
    }

    /**
     * @return Mage_Admin_Model_Session
     */
    protected function getSession()
    {
        return  Mage::getSingleton('admin/session');
    }

    /**
     * Save admin user extra data (like configuration sections state)
     *
     * @param  array|string $data
     * @return $this
     */
    public function saveExtra($data)
    {
        if (is_array($data)) {
            $data = serialize($data);
        }

        $this->_getResource()->saveExtra($this, $data);
        return $this;
    }

    /**
     * Save user roles
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function saveRelations()
    {
        $this->_getResource()->_saveRelations($this);
        return $this;
    }

    /**
     * Retrieve user roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->_getResource()->getRoles($this);
    }

    /**
     * Get admin role model
     *
     * @return Mage_Admin_Model_Roles
     */
    public function getRole()
    {
        if ($this->_role === null) {
            $this->_role = Mage::getModel('admin/roles');
            $roles = $this->getRoles();
            if ($roles && isset($roles[0]) && $roles[0]) {
                $this->_role->load($roles[0]);
            }
        }

        return $this->_role;
    }

    /**
     * Unassign user from his current role
     *
     * @return $this
     */
    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    /**
     * Check if such combination role/user exists
     *
     * @return bool
     */
    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return is_array($result) && $result !== [];
    }

    /**
     * Assign user to role
     *
     * @return $this
     */
    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    /**
     * Check if user exists based on its id, username and email
     *
     * @return bool
     */
    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return is_array($result) && $result !== [];
    }

    /**
     * Retrieve admin user collection
     *
     * @return Mage_Admin_Model_Resource_User_Collection
     */
    public function getCollection()
    {
        return Mage::getResourceModel('admin/user_collection');
    }

    /**
     * Send email with new user password
     *
     * @return $this
     * @deprecated deprecated since version 1.6.1.0
     */
    public function sendNewPasswordEmail()
    {
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return $this
     */
    public function sendPasswordResetConfirmationEmail()
    {
        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY));
        $mailer->setStoreId(0);
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE));
        $mailer->setTemplateParams([
            'user' => $this,
        ]);
        $mailer->send();

        return $this;
    }

    /**
     * Retrieve user name
     *
     * @param  string $separator
     * @return string
     */
    public function getName($separator = ' ')
    {
        return $this->getFirstname() . $separator . $this->getLastname();
    }

    /**
     * Retrieve user identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->getUserId();
    }

    /**
     * Get user ACL role
     *
     * @return string
     */
    public function getAclRole()
    {
        return 'U' . $this->getUserId();
    }

    /**
     * Authenticate username and password and save loaded record
     *
     * @param  string              $username
     * @param  string              $password
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function authenticate($username, $password)
    {
        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $password = new Mage_Core_Model_Security_Obfuscated($password);

        $config = Mage::getStoreConfigFlag('admin/security/use_case_sensitive_login');
        $result = false;

        try {
            Mage::dispatchEvent('admin_user_authenticate_before', [
                'username' => $username,
                'user'     => $this,
            ]);
            $this->loadByUsername($username);
            $sensitive = ($config) ? $username == $this->getUsername() : true;

            if ($sensitive && $this->getId() && $this->validatePasswordHash($password, $this->getPassword())) {
                if ($this->getIsActive() != '1') {
                    Mage::throwException(Mage::helper('adminhtml')->__('This account is inactive.'));
                }

                if (!$this->hasAssigned2Role($this->getId())) {
                    Mage::throwException(Mage::helper('adminhtml')->__('Access denied.'));
                }

                $result = true;
            }

            Mage::dispatchEvent('admin_user_authenticate_after', [
                'username' => $username,
                'password' => $password,
                'user'     => $this,
                'result'   => $result,
            ]);
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->unsetData();
            throw $mageCoreException;
        }

        if (!$result) {
            $this->unsetData();
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function validatePasswordHash(string $string1, string $string2): bool
    {
        return Mage::helper('core')->validateHash($string1, $string2);
    }

    /**
     * Login user
     *
     * @param  string              $username
     * @param  string              $password
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function login($username, $password)
    {
        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $password = new Mage_Core_Model_Security_Obfuscated($password);

        if ($this->authenticate($username, $password)) {
            $this->getResource()->recordLogin($this);
            Mage::getSingleton('core/session')->renewFormKey();
        }

        return $this;
    }

    /**
     * Reload current user
     *
     * @return $this
     */
    public function reload()
    {
        $id = $this->getId();
        $oldPassword = $this->getPassword();
        $this->setId(null);
        $this->load($id);
        $isUserPasswordChanged = $this->getSession()->getUserPasswordChanged();
        if (!$isUserPasswordChanged && $this->getPassword() !== $oldPassword) {
            $this->setId(null);
        } elseif ($isUserPasswordChanged) {
            $this->getSession()->setUserPasswordChanged(false);
        }

        return $this;
    }

    /**
     * Load user by its username
     *
     * @param  string $username
     * @return $this
     */
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }

    /**
     * Check if user is assigned to any role
     *
     * @param  int|Mage_Admin_Model_User $user
     * @return null|array
     */
    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    /**
     * Retrieve encoded password
     *
     * @param  string $password
     * @return string
     */
    protected function _getEncodedPassword($password)
    {
        return Mage::helper('core')->getHash($password, self::HASH_SALT_LENGTH);
    }

    /**
     * Returns helper instance
     *
     * @param  string                    $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Find first menu item that user is able to access
     *
     * @param  Mage_Core_Model_Config_Element|Varien_Simplexml_Element $parent
     * @param  string                                                  $path
     * @param  int                                                     $level
     * @return string
     */
    public function findFirstAvailableMenu($parent = null, $path = '', $level = 0)
    {
        if ($parent == null) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }

        foreach ($parent->children() as $childName => $child) {
            $aclResource = 'admin/' . $path . $childName;
            if (Mage::getSingleton('admin/session')->isAllowed($aclResource)) {
                if (!$child->children) {
                    return (string) $child->action;
                }

                $action = $this->findFirstAvailableMenu($child->children, $path . $childName . '/', $level + 1);
                return $action ?: (string) $child->action;
            }
        }

        $this->_hasAvailableResources = false;
        return '*/*/denied';
    }

    /**
     * Check if user has available resources
     *
     * @return bool
     */
    public function hasAvailableResources()
    {
        return $this->_hasAvailableResources;
    }

    /**
     * Find admin start page url
     *
     * @return string
     * @deprecated Please use getStartupPageUrl() method instead
     * @see getStartupPageUrl()
     * @codeCoverageIgnore
     */
    public function getStatrupPageUrl()
    {
        return $this->getStartupPageUrl();
    }

    /**
     * Find admin start page url
     *
     * @return string
     */
    public function getStartupPageUrl()
    {
        $startupPage = Mage::getStoreConfig(self::XML_PATH_STARTUP_PAGE);
        $aclResource = 'admin/' . $startupPage;
        if (Mage::getSingleton('admin/session')->isAllowed($aclResource)) {
            $nodePath = 'menu/' . implode('/children/', explode('/', $startupPage)) . '/action';
            $url = (string) Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode($nodePath);
            if ($url) {
                return $url;
            }
        }

        return $this->findFirstAvailableMenu();
    }

    /**
     * Validate user attribute values.
     * Returns TRUE or array of errors.
     *
     * @return array|true
     */
    public function validate()
    {
        $validator  = $this->getValidationHelper();
        $violations = new ArrayObject();
        $errors     = new ArrayObject();

        $violations->append($validator->validateNotEmpty(
            value: $this->getUsername(),
            message: Mage::helper('adminhtml')->__('User Name is required field.'),
        ));

        $violations->append($validator->validateNotEmpty(
            value: $this->getFirstname(),
            message: Mage::helper('adminhtml')->__('First Name is required field.'),
        ));

        $violations->append($validator->validateNotEmpty(
            value: $this->getLastname(),
            message: Mage::helper('adminhtml')->__('Last Name is required field.'),
        ));

        $violations->append($validator->validateEmail(
            value: $this->getEmail(),
            message: Mage::helper('adminhtml')->__('Please enter a valid email.'),
        ));

        foreach ($violations as $violation) {
            foreach ($violation as $error) {
                $errors->append($error->getMessage());
            }
        }

        if ($this->hasNewPassword()) {
            $password = $this->getNewPassword();
        } elseif ($this->hasPassword()) {
            $password = $this->getPassword();
        }

        if (isset($password)) {
            $violations = new ArrayObject();
            $violations->append($this->getPasswordValidator(value: $password));

            if ($this->hasPasswordConfirmation()) {
                $violations->append($validator->validateIdentical(
                    value: $this->getPasswordConfirmation(),
                    compare: $password,
                    message: Mage::helper('adminhtml')->__('Password confirmation must be same as password.'),
                ));
            }

            foreach ($violations as $violation) {
                foreach ($violation as $error) {
                    $errors->append($error->getMessage());
                }
            }

            Mage::dispatchEvent('admin_user_validate', [
                'user' => $this,
                'errors' => $errors,
            ]);
        }

        if ($this->userExists()) {
            $errors->append(Mage::helper('adminhtml')->__('A user with the same user name or email already exists.'));
        }

        if (count($errors) === 0) {
            return true;
        }

        return (array) $errors;
    }

    public function getPasswordValidator(mixed $value): ConstraintViolationListInterface
    {
        $min = $this->getMinAdminPasswordLength();
        $validator  = $this->getValidationHelper();

        return $validator->validatePassword(
            value: $value,
            min: $min,
            minMessage: Mage::helper('adminhtml')->__('Password must be at least of %d characters.', $min),
            regexMessage: Mage::helper('adminhtml')->__('Password must include both numeric and alphabetic characters.'),
        );
    }

    /**
     * Validate password against current user password
     * Returns TRUE or array of errors.
     *
     * @param  string     $password
     * @return array|true
     * @throws Exception
     */
    public function validateCurrentPassword($password)
    {
        $validator  = $this->getValidationHelper();
        $result     = [];

        if ($validator->validateNotEmpty($password)->count() > 0) {
            $result[] = $this->_getHelper('adminhtml')->__('Current password field cannot be empty.');
        } elseif (is_null($this->getId()) || !Mage::helper('core')->validateHash($password, $this->getPassword())) {
            $result[] = $this->_getHelper('adminhtml')->__('Invalid current password.');
        }

        if (empty($result)) {
            return true;
        }

        return $result;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param  string              $newResetPasswordLinkToken
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken)
    {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Invalid password reset token.'));
        }

        $this->setRpToken($newResetPasswordLinkToken);
        $currentDate = Varien_Date::now();
        $this->setRpTokenCreatedAt($currentDate);

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

        $tokenExpirationPeriod = Mage::helper('admin')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $hoursDifference = floor(($currentTimestamp - $tokenTimestamp) / (60 * 60));
        if ($hoursDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Clean password's validation data (password, current_password, new_password, password_confirmation)
     *
     * @return $this
     */
    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('current_password', null);
        $this->setData('new_password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }

    /**
     * Simple sql format date
     *
     * @param  bool|string $dayOnly
     * @return string
     */
    protected function _getDateNow($dayOnly = false)
    {
        return Varien_Date::now($dayOnly);
    }

    /**
     * Send notification to general Contact and additional emails when new admin user created.
     * You can declare additional emails in Mage_Core general/additional_notification_emails/admin_user_create node.
     *
     * @param  Mage_Admin_Model_User $user
     * @return $this
     */
    public function sendAdminNotification($user)
    {
        // define general contact Name and Email
        $generalContactName = Mage::getStoreConfig('trans_email/ident_general/name');
        $generalContactEmail = Mage::getStoreConfig('trans_email/ident_general/email');

        // collect general and additional emails
        $emails = $this->getUserCreateAdditionalEmail();
        $emails[] = $generalContactEmail;

        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer    = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo(array_filter($emails), $generalContactName);

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender([
            'name'  => $generalContactName,
            'email' => $generalContactEmail,
        ]);
        $mailer->setStoreId(0);
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_EMAILS_TEMPLATE));
        $mailer->setTemplateParams([
            'user' => $user,
        ]);
        $mailer->send();

        return $this;
    }

    /**
     * Get additional emails for notification from config.
     *
     * @return array
     */
    public function getUserCreateAdditionalEmail()
    {
        $emails = str_replace(' ', '', Mage::getStoreConfig(self::XML_PATH_ADDITIONAL_EMAILS));
        return explode(',', $emails);
    }

    /**
     * Retrieve minimum length of admin password
     *
     * @return int
     */
    public function getMinAdminPasswordLength()
    {
        $minLength = Mage::getStoreConfigAsInt(self::XML_PATH_MIN_ADMIN_PASSWORD_LENGTH);
        $absoluteMinLength = Mage_Core_Model_App::ABSOLUTE_MIN_PASSWORD_LENGTH;
        return max($absoluteMinLength, $minLength);
    }
}
