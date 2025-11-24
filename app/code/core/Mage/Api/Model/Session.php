<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice api session
 *
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Acl getAcl()
 * @method Mage_Api_Model_User getUser()
 * @method $this setAcl(Mage_Api_Model_Acl $loadAcl)
 * @method $this setUser(Mage_Api_Model_User $user)
 */
class Mage_Api_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public $sessionIds = [];

    protected $_currentSessId = null;

    /**
     * @param null|string $sessionName
     * @return $this
     */
    public function start($sessionName = null)
    {
        $this->_currentSessId = md5(time() . uniqid('', true) . $sessionName);
        $this->sessionIds[] = $this->getSessionId();
        return $this;
    }

    /**
     * @param string $namespace
     * @param null|string $sessionName
     * @return $this
     */
    public function init($namespace, $sessionName = null)
    {
        if (is_null($this->_currentSessId)) {
            $this->start();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->_currentSessId;
    }

    /**
     * @param null|string $sessId
     * @return $this
     */
    public function setSessionId($sessId = null)
    {
        if (!is_null($sessId)) {
            $this->_currentSessId = $sessId;
        }

        return $this;
    }

    /**
     * @return void
     */
    public function revalidateCookie()
    {
        // In api we don't use cookies
    }

    /**
     * @return bool
     */
    public function clear()
    {
        if ($sessId = $this->getSessionId()) {
            try {
                Mage::getModel('api/user')->logoutBySessId($sessId);
            } catch (Exception) {
                return false;
            }
        }

        return true;
    }

    /**
     * Flag login as HTTP Basic Auth.
     *
     * @return $this
     */
    public function setIsInstaLogin(bool $isInstaLogin = true)
    {
        $this->setData('is_insta_login', $isInstaLogin);
        return $this;
    }

    /**
     * Is insta-login?
     */
    public function getIsInstaLogin(): bool
    {
        return (bool) $this->getData('is_insta_login');
    }

    /**
     * @param string $username
     * @param string $apiKey
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function login($username, $apiKey)
    {
        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $apiKey = new Mage_Core_Model_Security_Obfuscated($apiKey);

        $user = Mage::getModel('api/user')
            ->setSessid($this->getSessionId());
        if ($this->getIsInstaLogin() && $user->authenticate($username, $apiKey)) {
            Mage::dispatchEvent('api_user_authenticated', [
                'model'    => $user,
                'api_key'  => $apiKey,
            ]);
        } else {
            $user->login($username, $apiKey);
        }

        if ($user->getId() && $user->getIsActive() != '1') {
            Mage::throwException(Mage::helper('api')->__('Your account has been deactivated.'));
        } elseif (!Mage::getModel('api/user')->hasAssigned2Role($user->getId())) {
            Mage::throwException(Mage::helper('api')->__('Access denied.'));
        } elseif ($user->getId()) {
            $this->setUser($user);
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
        } else {
            Mage::throwException(Mage::helper('api')->__('Unable to login.'));
        }

        return $user;
    }

    /**
     * @param null|Mage_Api_Model_User $user
     * @return $this
     */
    public function refreshAcl($user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }

        if (!$user) {
            return $this;
        }

        if (!$this->getAcl() || $user->getReloadAclFlag()) {
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
        }

        if ($user->getReloadAclFlag()) {
            $user->unsetData('api_key');
            $user->setReloadAclFlag('0')->save();
        }

        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
            try {
                if ($acl->isAllowed($user->getAclRole(), 'all', null)) {
                    return true;
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }

            try {
                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
            } catch (Exception) {
                return false;
            }
        }

        return false;
    }

    /**
     *  Check session expiration
     *
     * @param Mage_Api_Model_User $user
     * @return bool
     */
    public function isSessionExpired($user)
    {
        if (!$user->getId()) {
            return true;
        }

        $timeout = strtotime(Varien_Date::now()) - strtotime($user->getLogdate());
        return $timeout > Mage::getStoreConfig('api/config/session_timeout');
    }

    /**
     * @param false|string $sessId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function isLoggedIn($sessId = false)
    {
        $userExists = $this->getUser() && $this->getUser()->getId();

        if (!$userExists && $sessId !== false) {
            return $this->_renewBySessId($sessId);
        }

        if ($userExists) {
            Mage::register('isSecureArea', true, true);
        }

        return $userExists;
    }

    /**
     *  Renew user by session ID if session not expired
     *
     *  @param string $sessId
     *  @return bool
     */
    protected function _renewBySessId($sessId)
    {
        $user = Mage::getModel('api/user')->loadBySessId($sessId);
        if (!$user->getId() || !$user->getSessid()) {
            return false;
        }

        if ($user->getSessid() == $sessId && !$this->isSessionExpired($user)) {
            $this->setUser($user);
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());

            $user->getResource()->recordLogin($user)
                ->recordSession($user);

            return true;
        }

        return false;
    }
}
