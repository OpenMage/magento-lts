<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api model
 *
 * @category   Mage
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_User _getResource()
 * @method Mage_Api_Model_Resource_User getResource()
 * @method string getFirstname()
 * @method $this setFirstname(string $value)
 * @method string getLastname()
 * @method $this setLastname(string $value)
 * @method string getEmail()
 * @method $this setEmail(string $value)
 * @method string getUsername()
 * @method $this setUsername(string $value)
 * @method bool hasApiKey()
 * @method string getApiKey()
 * @method $this setApiKey(string $value)
 * @method bool hasApiKeyConfirmation()
 * @method string getApiKeyConfirmation()
 * @method string getCreated()
 * @method $this setCreated(string $value)
 * @method string getModified()
 * @method $this setModified(string $value)
 * @method int getLognum()
 * @method $this setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method $this setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method $this setIsActive(int $value)
 * @method string getSessid()
 * @method $this setSessid($sessId)
 * @method bool hasNewApiKey()
 * @method string getNewApiKey()
 * @method string getUserId()
 * @method string getLogdate()
 * @method int getRoleId()
 * @method array getRoleIds()
 * @method $this setLogdate(string $value)
 *
 * @method $this setRoleIds(array $value)
 * @method $this setRoleUserId(int $value)
 */
class Mage_Api_Model_User extends Mage_Core_Model_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'api_user';

    protected function _construct()
    {
        $this->_init('api/user');
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->_beforeSave();
        $data = [
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => Mage::getSingleton('core/date')->gmtDate()
        ];

        if ($this->getId() > 0) {
            $data['user_id']   = $this->getId();
        }

        if ($this->getUsername()) {
            $data['username']   = $this->getUsername();
        }

        if ($this->getApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getApiKey());
        }

        if ($this->getNewApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getNewApiKey());
        }

        if (!is_null($this->getIsActive())) {
            $data['is_active']  = (int) $this->getIsActive();
        }

        $this->setData($data);
        $this->_getResource()->save($this);
        $this->_afterSave();
        return $this;
    }

    /**
     * Delete user
     *
     * @return $this|Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
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
     * @return $this
     * @throws Mage_Core_Exception
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
     * @return $this
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
        return is_array($result) && count($result) > 0;
    }

    /**
     * Add user
     *
     * @return $this
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
        return is_array($result) && count($result) > 0;
    }

    /**
     * Get collection of users
     *
     * @return Object|Mage_Api_Model_Resource_User_Collection
     */
    public function getCollection()
    {
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
        return $this->getFirstname() . $separator . $this->getLastname();
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
        return 'U' . $this->getUserId();
    }

    /**
     * Authenticate user name and api key and save loaded record
     *
     * @param string $username
     * @param string $apiKey
     * @return bool
     * @throws Exception
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
        }

        $this->unsetData();
        return false;
    }

    /**
     * Login user
     *
     * @param string $username
     * @param string $apiKey
     * @return Mage_Api_Model_User
     * @throws Exception
     */
    public function login($username, $apiKey)
    {
        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $apiKey = new Mage_Core_Model_Security_Obfuscated($apiKey);

        $sessId = $this->getSessid();
        if ($this->authenticate($username, $apiKey)) {
            $this->setSessid($sessId);
            $this->getResource()->cleanOldSessions($this)
                ->recordLogin($this)
                ->recordSession($this);
            Mage::dispatchEvent('api_user_authenticated', [
               'model'    => $this,
               'api_key'  => $apiKey,
            ]);
        }

        return $this;
    }

    /**
     * Reload user
     *
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function loadBySessId($sessId)
    {
        $this->setData($this->getResource()->loadBySessId($sessId));
        return $this;
    }

    /**
     * Logout user by session id
     *
     * @param string $sessid
     * @return $this
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
     * @return array
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
        return Mage::helper('core')->getHash($apiKey, Mage_Admin_Model_User::HASH_SALT_LENGTH);
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
     * @return array|true
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = new ArrayObject();

        if (!Zend_Validate::is($this->getUsername(), 'NotEmpty')) {
            $errors->append($this->_getHelper('api')->__('User Name is required field.'));
        }

        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors->append($this->_getHelper('api')->__('First Name is required field.'));
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors->append($this->_getHelper('api')->__('Last Name is required field.'));
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors->append($this->_getHelper('api')->__('Please enter a valid email.'));
        }

        if ($this->hasNewApiKey()) {
            $apiKey = $this->getNewApiKey();
        } elseif ($this->hasApiKey()) {
            $apiKey = $this->getApiKey();
        }

        if (isset($apiKey)) {
            $minCustomerPasswordLength = $this->_getMinCustomerPasswordLength();
            if (strlen($apiKey) < $minCustomerPasswordLength) {
                $errors->append($this->_getHelper('api')
                    ->__('Api Key must be at least of %d characters.', $minCustomerPasswordLength));
            }

            if (!preg_match('/[a-z]/iu', $apiKey) || !preg_match('/[0-9]/u', $apiKey)) {
                $errors->append($this->_getHelper('api')
                    ->__('Api Key must include both numeric and alphabetic characters.'));
            }

            if ($this->hasApiKeyConfirmation() && $apiKey != $this->getApiKeyConfirmation()) {
                $errors->append($this->_getHelper('api')->__('Api Key confirmation must be same as Api Key.'));
            }
        }

        if ($this->userExists()) {
            $errors->append($this->_getHelper('api')
                ->__('A user with the same user name or email already exists.'));
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
