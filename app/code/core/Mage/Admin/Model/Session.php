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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Auth session model
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('admin');
    }

    public function login($username, $password, $request=null)
    {
        if (empty($username) || empty($password)) {
            return;
        }

        $user = Mage::getModel('admin/user')->login($username, $password);
        if ( $user->getId() && $user->getIsActive() != '1' ) {
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Your Account has been deactivated.'));
                $request->setParam('messageSent', true);
            }
        } elseif (!Mage::getModel('admin/user')->hasAssigned2Role($user->getId())) {
            if ($request && !$request->getParam('messageSent')) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Access Denied.'));
                $request->setParam('messageSent', true);
            }
        } else {
            if ($user->getId()) {
                $session = Mage::getSingleton('admin/session');
                $session->setIsFirstVisit(true);
                $session->setUser($user);
                $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                if ($request) {
                    /**
                     * Added hack as $_GET['ft'] param for redirecting to dashboard
                     * if _prepareDownloadResponse used when user is not logged in
                     */
                    $requestUriInfo = parse_url($request->getRequestUri());
                    if (isset($requestUriInfo['query']) && $requestUriInfo['query'] != '') {
                        $requestUriPostfix = '&ft';
                    } else {
                        $requestUriPostfix = '?ft';
                    }

                    header('Location: '.$request->getRequestUri() . $requestUriPostfix);
                    exit;
                }
            } else {
                if ($request && !$request->getParam('messageSent')) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Invalid Username or Password.'));
                    $request->setParam('messageSent', true);
                }
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
            $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        }
        if ($user->getReloadAclFlag()) {
            $user->unsetData('password');
            $user->setReloadAclFlag('0')->save();
        }
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * Mage::getSingleton('admin/session')->isAllowed('admin/catalog')
     * Mage::getSingleton('admin/session')->isAllowed('catalog')
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
            if (!preg_match('/^admin/', $resource)) {
            	$resource = 'admin/'.$resource;
            }

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
}