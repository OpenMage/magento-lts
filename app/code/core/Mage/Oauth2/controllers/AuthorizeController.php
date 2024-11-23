<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth2 Authorize Controller
 */
class Mage_Oauth2_AuthorizeController extends Mage_Oauth2_Controller_BaseController
{
    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $clientId = $this->getRequest()->getParam('client_id');
        $redirectUri = $this->getRequest()->getParam('redirect_uri');

        if (!$this->_validateParams($clientId, $redirectUri)) {
            return;
        }

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam('authorized') !== null) {
            $this->_processAuthorization();
            return;
        }

        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }

        $this->_renderAuthorizationForm();
    }

    /**
     * Validate request parameters
     *
     * @param string $clientId
     * @param string $redirectUri
     * @return bool
     */
    protected function _validateParams($clientId, $redirectUri)
    {
        if (!$clientId || !$redirectUri) {
            $this->_sendResponse(400, 'Invalid parameters.');
            return false;
        }

        $client = Mage::getModel('oauth2/client')->load($clientId, 'entity_id');
        if (!$client->getId()) {
            $this->_sendResponse(400, 'Invalid client.');
            return false;
        }
        if (!$client->getRedirectUri() || $client->getRedirectUri() != $redirectUri) {
            $this->_sendResponse(400, 'Invalid redirect_uri.');
            return false;
        }
        return true;
    }

    /**
     * Render authorization form
     *
     * @return void
     */
    protected function _renderAuthorizationForm()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('oauth2/authorize', 'oauth2.authorize')
        );
        $this->renderLayout();
    }

    /**
     * Process authorization
     *
     * @return void
     */
    protected function _processAuthorization()
    {
        try {
            $params = $this->getRequest()->getParams();
            $client = $this->_validateClient($params['client_id']);
            $customerId = $this->_getCustomerId($params);

            if ($params['authorized'] === 'yes') {
                $this->_authorizeClient($client, $customerId, $params['redirect_uri']);
            } else {
                $this->_sendResponse(401, 'User denied the request.');
            }
        } catch (Exception $e) {
            $this->_sendResponse(500, $e->getMessage());
            Mage::logException($e);
        }
    }

    /**
     * Validate client
     *
     * @param string $clientId
     * @return Mage_Oauth2_Model_Client
     * @throws Exception
     */
    protected function _validateClient($clientId)
    {
        $client = Mage::getModel('oauth2/client')->load($clientId, 'entity_id');
        if (!$client || !$client->getId()) {
            throw new Exception('Invalid client.');
        }
        return $client;
    }

    /**
     * Get customer ID
     *
     * @param array $params
     * @return int
     * @throws Exception
     */
    protected function _getCustomerId($params)
    {
        if ($this->_getCustomerSession()->isLoggedIn()) {
            return $this->_getCustomerSession()->getCustomerId();
        }

        $customer = Mage::getModel('customer/customer')->load($params['customer_id']);
        if (!$customer->getId()) {
            throw new Exception('Invalid customer.');
        }
        if (!empty($params['email']) && $params['email'] != $customer->getEmail()) {
            throw new Exception('Invalid customer email.');
        }

        return $customer->getId();
    }

    /**
     * Authorize client
     *
     * @param Mage_Oauth2_Model_Client $client
     * @param int $customerId
     * @param string $redirectUri
     */
    protected function _authorizeClient($client, $customerId, $redirectUri)
    {
        $authorizationCode = $this->_getOauth2Helper()->generateToken();
        $model = Mage::getModel('oauth2/authCode');
        $model->setAuthorizationCode($authorizationCode)
            ->setClientId($client->getId())
            ->setRedirectUri($redirectUri)
            ->setCustomerId($customerId)
            ->setExpiresIn(time() + 600)
            ->save();

        $redirectUri = $redirectUri . '?code=' . urlencode($authorizationCode);
        $this->_redirectUrl($redirectUri);
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get OAuth2 helper
     *
     * @return Mage_Oauth2_Helper_Data
     */
    protected function _getOauth2Helper()
    {
        return Mage::helper('oauth2');
    }
}
