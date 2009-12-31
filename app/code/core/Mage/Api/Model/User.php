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
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Api_Model_User extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('api/user');
    }

    public function save()
    {
        $this->_beforeSave();
        $data = array(
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => Mage::getSingleton('core/date')->gmtDate()
            );

        if($this->getId() > 0) {
            $data['user_id']   = $this->getId();
        }

        if( $this->getUsername() ) {
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

    public function delete()
    {
        $this->_getResource()->delete($this);
        return $this;
    }

    public function saveRelations()
    {
        $this->_getResource()->_saveRelations($this);
        return $this;
    }

    public function getRoles()
    {
        return $this->_getResource()->_getRoles($this);
    }

    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return ( is_array($result) && count($result) > 0 ) ? true : false;
    }

    public function getCollection() {
        return Mage::getResourceModel('admin/user_collection');
    }

    public function getName($separator=' ')
    {
        return $this->getFirstname().$separator.$this->getLastname();
    }

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
     * @param   string $login
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

    public function reload()
    {
        $this->load($this->getId());
        return $this;
    }

    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }

    public function loadBySessId ($sessId)
    {
        $this->setData($this->getResource()->loadBySessId($sessId));
        return $this;
    }

    public function logoutBySessId($sessid)
    {
        $this->getResource()->clearBySessId($sessid);
        return $this;
    }

    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    protected function _getEncodedApiKey($apiKey)
    {
        return Mage::helper('core')->getHash($apiKey, 2);
    }

}