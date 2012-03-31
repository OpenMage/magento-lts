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
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_AuthorizeController extends Mage_Core_Controller_Front_Action
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
     * @param bool $simple      Is simple page?
     * @return Mage_OAuth_AuthorizeController
     */
    protected function _initForm($simple = false)
    {
        /** @var $server Mage_OAuth_Model_Server */
        $server = Mage::getModel('oauth/server');
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);

        $isException = false;
        try {
            $server->checkAuthorizeRequest();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_OAuth_Exception $e) {
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
            /** @var $block Mage_OAuth_Block_Authorize_Button */
            $block = $contentBlock->getChild('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block Mage_OAuth_Block_Authorize */
            $block = $contentBlock->getChild('oauth.authorize.form');
        }

        if ($simple) {
            $layout->getBlock('oauth.authorize.style')->setData('is_logged', $logged);
        }

        /** @var $helper Mage_Core_Helper_Url */
        $helper = Mage::helper('core/url');
        $session->setAfterAuthUrl(Mage::getUrl('customer/account/login', array('_nosid' => true)))
                ->setBeforeAuthUrl($helper->getCurrentUrl());

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param bool $simple      Is simple page?
     * @return Mage_OAuth_AuthorizeController
     */
    protected function _initConfirmPage($simple = false)
    {
        $this->loadLayout();
        try {
            /** @var $session Mage_Customer_Model_Session */
            $session = Mage::getSingleton($this->_sessionName);
            /** @var $server Mage_OAuth_Model_Server */
            $server = Mage::getModel('oauth/server');

            /** @var $block Mage_OAuth_Block_Authorize */
            $block = $this->getLayout()->getBlock('oauth.authorize.confirm');
            $block->setIsSimple($simple);

            /** @var $token Mage_OAuth_Model_Token */
            $token = $server->authorizeToken($session->getCustomerId(), Mage_OAuth_Model_Token::USER_TYPE_CUSTOMER);

            /** @var $helper Mage_OAuth_Helper_Data */
            $helper = Mage::helper('oauth');

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $block->setVerifier($token->getVerifier());
                $session->addSuccess($this->__('Authorization confirmed.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_OAuth_Exception $e) {
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
     * @param bool $simple      Is simple page?
     * @return Mage_OAuth_AuthorizeController
     */
    protected function _initRejectPage($simple = false)
    {
        $this->loadLayout();

        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);
        try {
            /** @var $server Mage_OAuth_Model_Server */
            $server = Mage::getModel('oauth/server');

            /** @var $block Mage_OAuth_Block_Authorize */
            $block = $this->getLayout()->getBlock('oauth.authorize.reject');
            $block->setIsSimple($simple . ($simple ? '&simple=1' : ''));

            /** @var $token Mage_OAuth_Model_Token */
            $token = $server->checkAuthorizeRequest();
            /** @var $helper Mage_OAuth_Helper_Data */
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

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

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
     * OAuth authorize or allow decline access simple page
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
