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
class Mage_Oauth_AuthorizeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'customer/session';

    /**
     * Init authorize page
     *
     * @param  bool  $simple Is simple page?
     * @return $this
     */
    protected function _initForm($simple = false)
    {
        /** @var Mage_Oauth_Model_Server $server */
        $server = Mage::getModel('oauth/server');
        /** @var Mage_Customer_Model_Session $session */
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
            /** @var Mage_Oauth_Block_Authorize_Button $block */
            $block = $contentBlock->getChild('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var Mage_Oauth_Block_Authorize $block */
            $block = $contentBlock->getChild('oauth.authorize.form');
        }

        /** @var Mage_Core_Helper_Url $helper */
        $helper = Mage::helper('core/url');
        $session->setAfterAuthUrl(Mage::getUrl('customer/account/login', ['_nosid' => true]))
                ->setBeforeAuthUrl($helper->getCurrentUrl());

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param  bool  $simple Is simple page?
     * @return $this
     */
    protected function _initConfirmPage($simple = false)
    {
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton($this->_sessionName);
        if (!$session->getCustomerId()) {
            $session->addError($this->__('Please login to proceed authorization.'));
            $url = $helper->getAuthorizeUrl(Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER);
            $this->_redirectUrl($url);
            return $this;
        }

        $this->loadLayout();

        /** @var Mage_Oauth_Block_Authorize $block */
        $block = $this->getLayout()->getBlock('oauth.authorize.confirm');
        $block->setIsSimple($simple);

        try {
            /** @var Mage_Oauth_Model_Server $server */
            $server = Mage::getModel('oauth/server');

            $token = $server->authorizeToken($session->getCustomerId(), Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER);

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            }

            $block->setVerifier($token->getVerifier());
            $session->addSuccess($this->__('Authorization confirmed.'));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_Oauth_Exception $e) {
            $session->addException($e, $this->__('An error occurred. Your authorization request is invalid.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred on confirm authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Init reject page
     *
     * @param  bool  $simple Is simple page?
     * @return $this
     */
    protected function _initRejectPage($simple = false)
    {
        $this->loadLayout();

        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton($this->_sessionName);
        try {
            /** @var Mage_Oauth_Model_Server $server */
            $server = Mage::getModel('oauth/server');

            /** @var Mage_Oauth_Block_Authorize $block */
            $block = $this->getLayout()->getBlock('oauth.authorize.reject');
            $block->setIsSimple($simple);

            $token = $server->checkAuthorizeRequest();
            /** @var Mage_Oauth_Helper_Data $helper */
            $helper = Mage::helper('oauth');

            if (($callback = $helper->getFullCallbackUrl($token, true))) {
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            }

            $session->addNotice($this->__('The application access request is rejected.'));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred on reject authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Index action.
     */
    public function indexAction()
    {
        $this->_initForm();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * OAuth authorize or allow decline access simple page
     */
    public function simpleAction()
    {
        $this->_initForm(true);
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
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
        $this->_initConfirmPage(true);
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
        $this->_initRejectPage(true);
    }
}
