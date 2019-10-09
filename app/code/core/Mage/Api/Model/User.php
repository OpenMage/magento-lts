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
 * @package     Mage_Api
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api model
 *
 * @method Mage_Api_Model_Resource_User _getResource()
 * @method Mage_Api_Model_Resource_User getResource()
 * @method string getFirstname()
 * @method Mage_Api_Model_User setFirstname(string $value)
 * @method string getLastname()
 * @method Mage_Api_Model_User setLastname(string $value)
 * @method string getEmail()
 * @method Mage_Api_Model_User setEmail(string $value)
 * @method string getUsername()
 * @method Mage_Api_Model_User setUsername(string $value)
 * @method string getApiKey()
 * @method Mage_Api_Model_User setApiKey(string $value)
 * @method string getCreated()
 * @method Mage_Api_Model_User setCreated(string $value)
 * @method string getModified()
 * @method Mage_Api_Model_User setModified(string $value)
 * @method int getLognum()
 * @method Mage_Api_Model_User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method Mage_Api_Model_User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method Mage_Api_Model_User setIsActive(int $value)
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_User extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'api_user';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('api/user');
    }

    /**
     * Save user
     *
     * @return Mage_Api_Model_User|Mage_Core_Model_Abstract
     */
    public function save()
    {
        $this->_beforeSave();
        $data = array(
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => Mage::getSingleton('core/date')->gmtDate()
            );

        if ($this->getId() > 0) {
            $data['user_id']   = $this->getId();
        }

        if ( $this->getUsername() ) {
            $data['username']   = $this->getUsername();
        }

        if ($this->getApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getApiKey());
        }

        if ($this->getNewApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getNewApiKey());
        }

        if ( !is_null($this->getIsActive()) ) {
            $data['is_active']  = intval($this->getIsActive());
        }

        $this->setData($data);
        $this->_getResource()->save($this);
        $this->_afterSave();
        return $this;
    }

    /**
     * Delete user
     *
     * @return Mage_Api_Model_User|Mage_Core_Model_Abstract
     */
    public function delete()
    {
        $this->_beforeDelete();
        $this->_getResource()->delete($this);
        $this->_afterDelete();
        return $this;
    }

    /**
     * Save relations for users
     *
     * @return Mage_Api_Model_User
     */
    public function saveRelations()
    {
        $this->_getResource()->_saveRelations($this);
        return $this;
    }

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->_getResource()->_getRoles($this);
    }

    /**
     * Delete user from role
     *
     * @return Mage_Api_Model_User
     */
    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    /**
     * Check is user role exists
     *
     * @return bool
     */
    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    /**
     * Add user
     *
     * @return Mage_Api_Model_User
     */
    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    /**
     * Check if user exists
     *
     * @return bool
     */
    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    /**
     * Get collection of users
     *
     * @return Object|Mage_Api_Model_Resource_User_Collection
     */
    public function getCollection() {
        return Mage::getResourceModel('api/user_collection');
    }

    /**
     * Get user's name
     *
     * @param string $separator
     * @return string
     */
    public function getName($separator = ' ')
    {
        return $this->getFirstname().$separator.$this->getLastname();
    }

    /**
     * Get user's id
     *
     * @return string
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
        return 'U'.$this->getUserId();
    }

    /**
     * Authenticate user name and api key and save loaded record
     *
     * @param string $username
     * @param string $apiKey
     * @return boolean
     */
    public function authenticate($username, $apiKey)
    {
        $this->loadByUsername($username);
        if (!$this->getId()) {
            return false;
        }
        $auth = Mage::helper('core')->validateHash($apiKey, $this->getApiKey());
        if ($auth) {
            return true;
        } else {
            $this->unsetData();
            return false;
        }
    }

    /**
     * Login user
     *
     * @param   string $username
     * @param   string $apiKey
     * @return  Mage_Api_Model_User
     */
    public function login($username, $apiKey)
    {
        $sessId = $this->getSessid();
        if ($this->authenticate($username, $apiKey)) {
            $this->setSessid($sessId);
            $this->getResource()->cleanOldSessions($this)
                ->recordLogin($this)
                ->recordSession($this);
            Mage::dispatchEvent('api_user_authenticated', array(
               'model'    => $this,
               'api_key'  => $apiKey,
            ));
        }

        return $this;
    }

    /**
     * Reload user
     *
     * @return Mage_Api_Model_User
     */
    public function reload()
    {
        $this->load($this->getId());
        return $this;
    }

    /**
     * Load user by username
     *
     * @param string $username
     * @return Mage_Api_Model_User
     */
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }

    /**
     * Load user by session id
     *
     * @param string $sessId
     * @return Mage_Api_Model_User
     */
    public function loadBySessId ($sessId)
    {
        $this->setData($this->getResource()->loadBySessId($sessId));
        return $this;
    }

    /**
     * Logout user by session id
     *
     * @param string $sessid
     * @return Mage_Api_Model_User
     */
    public function logoutBySessId($sessid)
    {
        $this->getResource()->clearBySessId($sessid);
        return $this;
    }

    /**
     * Check if user is assigned to role
     *
     * @param int|Mage_Core_Model_Abstract $user
     * @return array|null
     */
    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    /**
     * Retrieve encoded api key
     *
     * @param string $apiKey
     * @return string
     */
    protected function _getEncodedApiKey($apiKey)
    {
        return $this->_getHelper('core')->getHashPassword($apiKey, Mage_Admin_Model_User::HASH_SALT_LENGTH);
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
     * Validate user attribute values.
     *
     * @return array|bool
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = new ArrayObject();

        if (!Zend_Validate::is($this->getUsername(), 'NotEmpty')) {
            $errors[] = $this->_getHelper('api')->__('User Name is required field.');
        }

        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = $this->_getHelper('api')->__('First Name is required field.');
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = $this->_getHelper('api')->__('Last Name is required field.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = $this->_getHelper('api')->__('Please enter a valid email.');
        }

        if ($this->hasNewApiKey()) {
            $apiKey = $this->getNewApiKey();
        } elseif ($this->hasApiKey()) {
            $apiKey = $this->getApiKey();
        }

        if (isset($apiKey)) {
            $minCustomerPasswordLength = $this->_getMinCustomerPasswordLength();
            if (strlen($apiKey) < $minCustomerPasswordLength) {
                $errors[] = $this->_getHelper('api')
                    ->__('Api Key must be at least of %d characters.', $minCustomerPasswordLength);
            }

            if (!preg_match('/[a-z]/iu', $apiKey) || !preg_match('/[0-9]/u', $apiKey)) {
                $errors[] = $this->_getHelper('api')
                    ->__('Api Key must include both numeric and alphabetic characters.');
            }

            if ($this->hasApiKeyConfirmation() && $apiKey != $this->getApiKeyConfirmation()) {
                $errors[] = $this->_getHelper('api')->__('Api Key confirmation must be same as Api Key.');
            }
        }

        if ($this->userExists()) {
            $errors[] = $this->_getHelper('api')
                ->__('A user with the same user name or email already exists.');
        }

        if (count($errors) === 0) {
            return true;
        }

        return (array) $errors;
    }

    /**
     * Get min customer password length
     *
     * @return int
     */
    protected function _getMinCustomerPasswordLength()
    {
        return Mage::getSingleton('customer/customer')->getMinPasswordLength();
    }
}
