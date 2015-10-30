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
 * @package     Mage_Admin
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin user model
 *
 * @method Mage_Admin_Model_Resource_User _getResource()
 * @method Mage_Admin_Model_Resource_User getResource()
 * @method string getFirstname()
 * @method Mage_Admin_Model_User setFirstname(string $value)
 * @method string getLastname()
 * @method Mage_Admin_Model_User setLastname(string $value)
 * @method string getEmail()
 * @method Mage_Admin_Model_User setEmail(string $value)
 * @method string getUsername()
 * @method Mage_Admin_Model_User setUsername(string $value)
 * @method string getPassword()
 * @method Mage_Admin_Model_User setPassword(string $value)
 * @method string getCreated()
 * @method Mage_Admin_Model_User setCreated(string $value)
 * @method string getModified()
 * @method Mage_Admin_Model_User setModified(string $value)
 * @method string getLogdate()
 * @method Mage_Admin_Model_User setLogdate(string $value)
 * @method int getLognum()
 * @method Mage_Admin_Model_User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method Mage_Admin_Model_User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method Mage_Admin_Model_User setIsActive(int $value)
 * @method string getExtra()
 * @method Mage_Admin_Model_User setExtra(string $value)
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_User extends Mage_Core_Model_Abstract
{
    /**#@+
     * Configuration paths for email templates and identities
     */
    const XML_PATH_FORGOT_EMAIL_TEMPLATE    = 'admin/emails/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY    = 'admin/emails/forgot_email_identity';
    const XML_PATH_STARTUP_PAGE             = 'admin/startup/page';
    /**#@-*/

    /**
     * Minimum length of admin password
     */
    const MIN_PASSWORD_LENGTH = 7;

    /**
     * Length of salt
     */
    const HASH_SALT_LENGTH = 32;

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
     * @var boolean
     */
    protected $_hasAvailableResources = true;

    /**
     * Initialize user model
     */
    protected function _construct()
    {
        $this->_init('admin/user');
    }

    /**
     * Processing data before model save
     *
     * @return Mage_Admin_Model_User
     */
    protected function _beforeSave()
    {
        $data = array(
            'firstname' => $this->getFirstname(),
            'lastname'  => $this->getLastname(),
            'email'     => $this->getEmail(),
            'modified'  => $this->_getDateNow(),
            'extra'     => serialize($this->getExtra())
        );

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
            $data['is_active'] = intval($this->getIsActive());
        }

        $this->addData($data);

        return parent::_beforeSave();
    }

    /**
     * Save admin user extra data (like configuration sections state)
     *
     * @param   array $data
     * @return  Mage_Admin_Model_User
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
     * @return Mage_Admin_Model_User
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
        if (null === $this->_role) {
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
     * @return Mage_Admin_Model_User
     */
    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    /**
     * Check if such combination role/user exists
     *
     * @return boolean
     */
    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return (is_array($result) && count($result) > 0) ? true : false;
    }

    /**
     * Assign user to role
     *
     * @return Mage_Admin_Model_User
     */
    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    /**
     * Check if user exists based on its id, username and email
     *
     * @return boolean
     */
    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return (is_array($result) && count($result) > 0) ? true : false;
    }

    /**
     * Retrieve admin user collection
     *
     * @return Mage_Admin_Model_Resource_User_Collection
     */
    public function getCollection() {
        return Mage::getResourceModel('admin/user_collection');
    }

    /**
     * Send email with new user password
     *
     * @return Mage_Admin_Model_User
     * @deprecated deprecated since version 1.6.1.0
     */
    public function sendNewPasswordEmail()
    {
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return Mage_Admin_Model_User
     */
    public function sendPasswordResetConfirmationEmail()
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getEmail(), $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_IDENTITY));
        $mailer->setStoreId(0);
        $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_FORGOT_EMAIL_TEMPLATE));
        $mailer->setTemplateParams(array(
            'user' => $this
        ));
        $mailer->send();

        return $this;
    }

    /**
     * Retrieve user name
     *
     * @param string $separator
     * @return string
     */
    public function getName($separator = ' ')
    {
        return $this->getFirstname() . $separator . $this->getLastname();
    }

    /**
     * Retrieve user identifier
     *
     * @return mixed
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
     * Authenticate user name and password and save loaded record
     *
     * @param string $username
     * @param string $password
     * @return boolean
     * @throws Mage_Core_Exception
     */
    public function authenticate($username, $password)
    {
        $config = Mage::getStoreConfigFlag('admin/security/use_case_sensitive_login');
        $result = false;

        try {
            Mage::dispatchEvent('admin_user_authenticate_before', array(
                'username' => $username,
                'user'     => $this
            ));
            $this->loadByUsername($username);
            $sensitive = ($config) ? $username == $this->getUsername() : true;

            if ($sensitive && $this->getId() && Mage::helper('core')->validateHash($password, $this->getPassword())) {
                if ($this->getIsActive() != '1') {
                    Mage::throwException(Mage::helper('adminhtml')->__('This account is inactive.'));
                }
                if (!$this->hasAssigned2Role($this->getId())) {
                    Mage::throwException(Mage::helper('adminhtml')->__('Access denied.'));
                }
                $result = true;
            }

            Mage::dispatchEvent('admin_user_authenticate_after', array(
                'username' => $username,
                'password' => $password,
                'user'     => $this,
                'result'   => $result,
            ));
        }
        catch (Mage_Core_Exception $e) {
            $this->unsetData();
            throw $e;
        }

        if (!$result) {
            $this->unsetData();
        }
        return $result;
    }

    /**
     * Login user
     *
     * @param   string $login
     * @param   string $password
     * @return  Mage_Admin_Model_User
     */
    public function login($username, $password)
    {
        if ($this->authenticate($username, $password)) {
            $this->getResource()->recordLogin($this);
        }
        return $this;
    }

    /**
     * Reload current user
     *
     * @return Mage_Admin_Model_User
     */
    public function reload()
    {
        $id = $this->getId();
        $this->setId(null);
        $this->load($id);
        return $this;
    }

    /**
     * Load user by its username
     *
     * @param string $username
     * @return Mage_Admin_Model_User
     */
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }

    /**
     * Check if user is assigned to any role
     *
     * @param int|Mage_Core_Admin_Model_User $user
     * @return null|boolean|array
     */
    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    /**
     * Retrieve encoded password
     *
     * @param string $password
     * @return string
     */
    protected function _getEncodedPassword($password)
    {
        return $this->_getHelper('core')->getHash($password, self::HASH_SALT_LENGTH);
    }

    /**
     * Returns helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Find first menu item that user is able to access
     *
     * @param Mage_Core_Model_Config_Element $parent
     * @param string $path
     * @param integer $level
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
                    return (string)$child->action;
                } else if ($child->children) {
                    $action = $this->findFirstAvailableMenu($child->children, $path . $childName . '/', $level + 1);
                    return $action ? $action : (string)$child->action;
                }
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
     * @deprecated Please use getStartupPageUrl() method instead
     * @see getStartupPageUrl()
     * @return string
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
            $nodePath = 'menu/' . join('/children/', explode('/', $startupPage)) . '/action';
            $url = (string)Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode($nodePath);
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
     * @return mixed
     */
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getUsername(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('User Name is required field.');
        }

        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('First Name is required field.');
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('Last Name is required field.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('adminhtml')->__('Please enter a valid email.');
        }

        if ($this->hasNewPassword()) {
            if (Mage::helper('core/string')->strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
                $errors[] = Mage::helper('adminhtml')->__('Password must be at least of %d characters.', self::MIN_PASSWORD_LENGTH);
            }

            if (!preg_match('/[a-z]/iu', $this->getNewPassword())
                || !preg_match('/[0-9]/u', $this->getNewPassword())
            ) {
                $errors[] = Mage::helper('adminhtml')->__('Password must include both numeric and alphabetic characters.');
            }

            if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
                $errors[] = Mage::helper('adminhtml')->__('Password confirmation must be same as password.');
            }
        }

        if ($this->userExists()) {
            $errors[] = Mage::helper('adminhtml')->__('A user with the same user name or email aleady exists.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Validate password against current user password
     * Returns true or array of errors.
     *
     * @return mixed
     */
    public function validateCurrentPassword($password)
    {
        $result = array();

        if (!Zend_Validate::is($password, 'NotEmpty')) {
            $result[] = $this->_getHelper('adminhtml')->__('Current password field cannot be empty.');
        } elseif (is_null($this->getId()) || !$this->_getHelper('core')->validateHash($password, $this->getPassword())){
            $result[] = $this->_getHelper('adminhtml')->__('Invalid current password.');
        }

        if (empty($result)) {
            $result = true;
        }
        return $result;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token and its creation time
     *
     * @param string $newResetPasswordLinkToken
     * @return Mage_Admin_Model_User
     * @throws Mage_Core_Exception
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken) {
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
     * @return boolean
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

        $dayDifference = floor(($currentTimestamp - $tokenTimestamp) / (24 * 60 * 60));
        if ($dayDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Clean password's validation data (password, current_password, new_password, password_confirmation)
     *
     * @return Mage_Admin_Model_User
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
     * @param string $format
     * @return string
     */
    protected function _getDateNow($dayOnly = false)
    {
        return now($dayOnly);
    }
}
