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

        $user = Mage::getModel('api/user')->login($username, $apiKey);
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

    public function isLoggedIn()
    {
        return $this->getUser() && $this->getUser()->getId();
    }
} // Class Mage_Api_Model_Session End