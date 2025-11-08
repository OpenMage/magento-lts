<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * oAuth authorize controller
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Adminhtml_Oauth_AuthorizeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'admin/session';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    public $_publicActions = ['index', 'simple', 'confirm', 'confirmSimple','reject', 'rejectSimple'];

    /**
     * Disable showing of login form
     *
     * @return $this
     * @see Mage_Admin_Model_Observer::actionPreDispatchAdmin() method for explanation
     */
    public function preDispatch()
    {
        Mage::app()->getRequest()->setInternallyForwarded();

        // check login data before it set null in Mage_Admin_Model_Observer::actionPreDispatchAdmin
        $loginError = $this->_checkLoginIsEmpty();

        parent::preDispatch();

        // call after parent::preDispatch(); to get session started
        if ($loginError) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Invalid User Name or Password.'));
            $params = ['_query' => ['oauth_token' => $this->getRequest()->getParam('oauth_token', null)]];
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
            $params = ['_query' => ['oauth_token' => $this->getRequest()->getParam('oauth_token', null)]];
            $this->_redirect('*/*/*', $params);
        }

        return $this;
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initForm();

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Index action with a simple design
     *
     * @return void
     */
    public function simpleAction()
    {
        $this->_initForm(true);
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Init authorize page
     *
     * @param bool $simple
     * @return $this
     */
    protected function _initForm($simple = false)
    {
        /** @var Mage_Oauth_Model_Server $server */
        $server = Mage::getModel('oauth/server');
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton($this->_sessionName);

        $isException = false;
        try {
            $server->checkAuthorizeRequest();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_Oauth_Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred. Your authorization request is invalid.'));
        } catch (Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred.'));
        }

        $this->loadLayout();
        $layout = $this->getLayout();
        $logged = $session->isLoggedIn();

        $contentBlock = $layout->getBlock('content');
        if ($logged) {
            $contentBlock->unsetChild('oauth.authorize.form');
            /** @var Mage_Oauth_Block_Adminhtml_Oauth_Authorize_Button $block */
            $block = $contentBlock->getChild('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var Mage_Oauth_Block_Adminhtml_Oauth_Authorize $block */
            $block = $contentBlock->getChild('oauth.authorize.form');
        }

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param bool $simple
     * @return $this
     */
    protected function _initConfirmPage($simple = false)
    {
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton($this->_sessionName);

        /** @var Mage_Admin_Model_User $user */
        $user = $session->getData('user');
        if (!$user) {
            $session->addError($this->__('Please login to proceed authorization.'));
            $url = $helper->getAuthorizeUrl(Mage_Oauth_Model_Token::USER_TYPE_ADMIN);
            $this->_redirectUrl($url);
            return $this;
        }

        $this->loadLayout();

        /** @var Mage_Oauth_Block_Adminhtml_Oauth_Authorize $block */
        $block = $this->getLayout()->getBlock('content')->getChild('oauth.authorize.confirm');
        $block->setIsSimple($simple);

        try {
            /** @var Mage_Oauth_Model_Server $server */
            $server = Mage::getModel('oauth/server');

            $token = $server->authorizeToken($user->getId(), Mage_Oauth_Model_Token::USER_TYPE_ADMIN);

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->getResponse()->setRedirect($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $block->setVerifier($token->getVerifier());
                $session->addSuccess($this->__('Authorization confirmed.'));
            }
        } catch (Mage_Core_Exception $e) {
            $block->setHasException(true);
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $block->setHasException(true);
            $session->addException($e, $this->__('An error occurred on confirm authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Init reject page
     *
     * @param bool $simple
     * @return Mage_Oauth_Adminhtml_Oauth_AuthorizeController
     */
    protected function _initRejectPage($simple = false)
    {
        /** @var Mage_Oauth_Model_Server $server */
        $server = Mage::getModel('oauth/server');

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton($this->_sessionName);

        $this->loadLayout();

        /** @var Mage_Oauth_Block_Authorize $block */
        $block = $this->getLayout()->getBlock('oauth.authorize.reject');
        $block->setIsSimple($simple);

        try {
            $token = $server->checkAuthorizeRequest();
            /** @var Mage_Oauth_Helper_Data $helper */
            $helper = Mage::helper('oauth');

            if (($callback = $helper->getFullCallbackUrl($token, true))) {
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $session->addNotice($this->__('The application access request is rejected.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred on reject authorize.'));
        }

        //display exception
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Check is login data has empty login or pass
     * See Mage_Admin_Model_Session: there is no any error message if login or password is empty
     *
     * @return bool
     */
    protected function _checkLoginIsEmpty()
    {
        $error = false;
        $action = $this->getRequest()->getActionName();
        if (($action == 'index' || $action == 'simple') && $this->getRequest()->getPost('login')) {
            $postLogin  = $this->getRequest()->getPost('login');
            $username   = $postLogin['username'] ?? '';
            $password   = $postLogin['password'] ?? '';
            if (empty($username) || empty($password)) {
                $error = true;
            }
        }

        return $error;
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        $this->_initConfirmPage();
    }

    /**
     * Confirm token authorization simple page
     */
    public function confirmSimpleAction()
    {
        $this->_initConfirmPage();
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        $this->_initRejectPage();
    }

    /**
     * Reject token authorization simple page
     */
    public function rejectSimpleAction()
    {
        $this->_initRejectPage();
    }

    /**
     * Check admin permissions for this controller
     *
     * @return true
     */
    protected function _isAllowed()
    {
        return true;
    }
}
