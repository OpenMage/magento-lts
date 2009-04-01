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

/**
 * Webservice api session
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public $sessionIds = array();

    public function start($sessionName=null)
    {
        parent::start($sessionName=null);
        $this->sessionIds[] = $this->getSessionId();
        return $this;
    }

    public function revalidateCookie()
    {
        // In api we don't use cookies
    }

    public function login($username, $apiKey)
    {
        if (empty($username) || empty($apiKey)) {
            return;
        }

        $user = Mage::getModel('api/user')
            ->setSessid($this->getSessionId())
            ->login($username, $apiKey);

        if ( $user->getId() && $user->getIsActive() != '1' ) {
            Mage::throwException(Mage::helper('api')->__('Your Account has been deactivated.'));
        } elseif (!Mage::getModel('api/user')->hasAssigned2Role($user->getId())) {
            Mage::throwException(Mage::helper('api')->__('Access Denied.'));
        } else {
            if ($user->getId()) {
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
            } else {
                Mage::throwException(Mage::helper('api')->__('Unable to login.'));
            }
        }

        return $user;
    }

    public function refreshAcl($user=null)
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
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    public function isAllowed($resource, $privilege=null)
    {
        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
    	    try {
        	    if ($acl->isAllowed($user->getAclRole(), 'all', null)){
        	        return true;
        	    }
    	    } catch (Exception $e) {}

        	try {
                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
        	} catch (Exception $e) {
        	    return false;
        	}
        }
        return false;
    }

    /**
     *  Check session expiration
     *
     *  @return	  boolean
     */
    public function isSessionExpired ($user)
    {
        if (!$user->getId()) {
            return true;
        }
        $timeout = strtotime( now() ) - strtotime( $user->getLogdate() );
        return $timeout > Mage::getStoreConfig('api/config/session_timeout');
    }


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
     *  @param    string $sessId
     *  @return	  boolean
     */
    protected function _renewBySessId ($sessId)
    {
        $user = Mage::getModel('api/user')->loadBySessId($sessId);
        if (!$user->getId() || !$user->getSessid()) {
            return false;
        }
        if ($user->getSessid() == $sessId && !$this->isSessionExpired($user)) {
            $this->setUser($user);
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
            $user->getResource()->recordLogin($user);
            return true;
        }
        return false;
    }

} // Class Mage_Api_Model_Session End