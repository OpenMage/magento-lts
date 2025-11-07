<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Auth session model
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Acl getAcl()
 * @method int getActiveTabId()
 * @method string getDeletedPath()
 * @method bool getIndirectLogin()
 * @method bool getIsTreeWasExpanded()
 * @method int getLastEditedCategory()
 * @method string getLastViewedStore()
 * @method bool getSyncProcessStopWatch()
 * @method Mage_Admin_Model_User getUser()
 * @method bool getUserPasswordChanged()
 * @method bool hasSyncProcessStopWatch()
 * @method $this setAcl(Mage_Admin_Model_Acl $acl)
 * @method $this setActiveTabId(int $value)
 * @method $this setAttributeData(array|false $data)
 * @method $this setDeletedPath(string $value)
 * @method $this setIndirectLogin(bool $value)
 * @method $this setIsFirstVisit(bool $value)
 * @method $this setIsTreeWasExpanded(bool $value)
 * @method $this setLastEditedCategory(int $value)
 * @method $this setLastViewedStore(string $value)
 * @method $this setSyncProcessStopWatch(bool $value)
 * @method $this setUser(Mage_Admin_Model_User $user)
 * @method $this setUserPasswordChanged(bool $value)
 * @method $this unsActiveTabId()
 */
class Mage_Admin_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Session admin SID config path
     *
     * @const
     */
    public const XML_PATH_ALLOW_SID_FOR_ADMIN_AREA = 'web/session/use_admin_sid';

    /**
     * Whether it is the first page after successful login
     *
     * @var null|bool
     */
    protected $_isFirstPageAfterLogin;

    /**
     * @var Mage_Admin_Model_Redirectpolicy
     */
    protected $_urlPolicy;

    /**
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Class constructor
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        $this->_urlPolicy = (!empty($parameters['redirectPolicy']))
            ? $parameters['redirectPolicy'] : Mage::getModel('admin/redirectpolicy');

        $this->_response = (!empty($parameters['response']))
            ? $parameters['response'] : new Mage_Core_Controller_Response_Http();

        $this->_factory = (!empty($parameters['factory']))
            ? $parameters['factory'] : Mage::getModel('core/factory');

        $this->init('admin');
        $this->logoutIndirect();
    }

    /**
     * Pull out information from session whether there is currently the first page after log in
     *
     * The idea is to set this value on login(), then redirect happens,
     * after that on next request the value is grabbed once the session is initialized
     * Since the session is used as a singleton, the value will be in $_isFirstPageAfterLogin until the end of request,
     * unless it is reset intentionally from somewhere
     *
     * @param string $namespace
     * @param string $sessionName
     * @return $this
     * @see self::login()
     */
    public function init($namespace, $sessionName = null)
    {
        parent::init($namespace, $sessionName);
        $this->isFirstPageAfterLogin();
        return $this;
    }

    /**
     * Logout user if was logged not from admin
     */
    protected function logoutIndirect()
    {
        $user = $this->getUser();
        if ($user) {
            $extraData = $user->getExtra();
            if (!is_null(Mage::app()->getRequest()->getParam('SID'))
                && !$this->allowAdminSid()
                || isset($extraData['indirect_login'])
                && $this->getIndirectLogin()
            ) {
                $this->unsetData('user');
                $this->setIndirectLogin(false);
            }
        }
    }

    /**
     * Try to login user in admin
     *
     * @param  string $username
     * @param  string $password
     * @param  Mage_Core_Controller_Request_Http $request
     * @return null|Mage_Admin_Model_User
     */
    public function login($username, $password, $request = null)
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $password = new Mage_Core_Model_Security_Obfuscated($password);

        try {
            /** @var Mage_Admin_Model_User $user */
            $user = $this->_factory->getModel('admin/user');
            $user->login($username, $password);
            if ($user->getId()) {
                $this->renewSession();

                if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                    Mage::getSingleton('adminhtml/url')->renewSecretUrls();
                }

                $this->setIsFirstPageAfterLogin(true);
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
                if ($backendLocale = $user->getBackendLocale()) {
                    Mage::getSingleton('adminhtml/session')->setLocale($backendLocale);
                }

                $alternativeUrl = $this->_getRequestUri($request);
                $redirectUrl = $this->_urlPolicy->getRedirectUrl($user, $request, $alternativeUrl);
                if ($redirectUrl) {
                    Mage::dispatchEvent('admin_session_user_login_success', ['user' => $user]);
                    $this->_response->clearHeaders()
                        ->setRedirect($redirectUrl)
                        ->sendHeadersAndExit();
                }
            } else {
                Mage::throwException(Mage::helper('adminhtml')->__('Invalid User Name or Password.'));
            }
        } catch (Mage_Core_Exception $e) {
            $e->setMessage(
                Mage::helper('adminhtml')->__('You did not sign in correctly or your account is temporarily disabled.'),
            );
            $this->_loginFailed($e, $request, $username, $e->getMessage());
        } catch (Exception $e) {
            $message = Mage::helper('adminhtml')->__('An error occurred while logging in.');
            $this->_loginFailed($e, $request, $username, $message);
        }

        return $user ?? null;
    }

    /**
     * Refresh ACL resources stored in session
     *
     * @param  Mage_Admin_Model_User $user
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
            $this->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
        }

        if ($user->getReloadAclFlag()) {
            $user->getResource()->saveReloadAclFlag($user, 0);
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
     * @return bool
     */
    public function isAllowed($resource, $privilege = null)
    {
        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
            if (!preg_match('/^admin/', $resource)) {
                $resource = 'admin/' . $resource;
            }

            try {
                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
            } catch (Exception) {
                try {
                    if (!$acl->has($resource)) {
                        return $acl->isAllowed($user->getAclRole(), null, $privilege);
                    }
                } catch (Exception) {
                }
            }
        }

        return false;
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getUser() && $this->getUser()->getId();
    }

    /**
     * Check if it is the first page after successful login
     *
     * @return bool
     */
    public function isFirstPageAfterLogin()
    {
        if (is_null($this->_isFirstPageAfterLogin)) {
            $this->_isFirstPageAfterLogin = $this->getData('is_first_visit', true);
        }

        return $this->_isFirstPageAfterLogin;
    }

    /**
     * Setter whether the current/next page should be treated as first page after login
     *
     * @param bool $value
     * @return $this
     */
    public function setIsFirstPageAfterLogin($value)
    {
        $this->_isFirstPageAfterLogin = (bool) $value;
        return $this->setIsFirstVisit($this->_isFirstPageAfterLogin);
    }

    /**
     * Custom REQUEST_URI logic
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return null|string
     */
    protected function _getRequestUri($request = null)
    {
        if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            return Mage::getSingleton('adminhtml/url')->getUrl('*/*/*', ['_current' => true]);
        } elseif ($request) {
            return $request->getRequestUri();
        } else {
            return null;
        }
    }

    /**
     * Login failed process
     *
     * @param Exception $e
     * @param null|Mage_Core_Controller_Request_Http $request
     * @param string $username
     * @param string $message
     */
    protected function _loginFailed($e, $request, $username, $message)
    {
        try {
            Mage::dispatchEvent('admin_session_user_login_failed', [
                'user_name' => $username,
                'exception' => $e,
            ]);
        } catch (Exception) {
        }

        if ($request && !$request->getParam('messageSent')) {
            Mage::getSingleton('adminhtml/session')->addError($message);
            $request->setParam('messageSent', true);
        }
    }

    /**
     * Check is allowed to use SID for admin area
     *
     * @return bool
     */
    protected function allowAdminSid()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ALLOW_SID_FOR_ADMIN_AREA);
    }
}
