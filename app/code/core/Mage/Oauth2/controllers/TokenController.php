<?php

class Mage_Oauth2_TokenController extends Mage_Oauth2_Controller_BaseController
{
    protected $helper;
    protected $tokenService;

    /**
     * Main action for handling OAuth2 token requests
     */
    public function indexAction()
    {
        $this->helper = Mage::helper('oauth2');
        $this->tokenService = Mage::getSingleton('oauth2/tokenService');

        try {
            $grantType = $this->getRequest()->getParam('grant_type');
            $clientId = $this->getRequest()->getParam('client_id');
            $clientSecret = $this->getRequest()->getParam('client_secret');

            $this->validateClient($clientId, $clientSecret, $grantType);

            switch ($grantType) {
                case 'authorization_code':
                    $response = $this->handleAuthorizationCodeGrant();
                    break;
                case 'refresh_token':
                    $response = $this->handleRefreshTokenGrant();
                    break;
                default:
                    throw new Exception('Invalid grant_type', 400);
            }

            $this->_sendResponse(200, 'Success', $response);
        } catch (Exception $e) {
            $this->_sendResponse($e->getCode() ?: 400, $e->getMessage());
        }
    }

    /**
     * Validate client credentials
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $grantType
     * @throws Exception if client credentials are invalid
     */
    protected function validateClient($clientId, $clientSecret, $grantType)
    {
        $client = Mage::getModel('oauth2/client')->load($clientId, 'entity_id');
        if (!$client->getId() || $client->getSecret() !== $clientSecret || !in_array($grantType, $client->getGrantTypes())) {
            throw new Exception('Invalid client.', 401);
        }
    }

    /**
     * Handle authorization code grant type
     *
     * @return array Token response
     * @throws Exception if authorization code is invalid
     */
    protected function handleAuthorizationCodeGrant()
    {
        $code = $this->getRequest()->getParam('code');
        $redirectUri = $this->getRequest()->getParam('redirect_uri');
        $clientId = $this->getRequest()->getParam('client_id');

        $authCode = $this->validateAuthorizationCode($code, $clientId, $redirectUri);

        $token = $this->tokenService->getOrCreateToken($clientId, $authCode);
        $authCode->setUsed(true)->save();

        return $this->formatTokenResponse($token);
    }

    /**
     * Validate authorization code
     *
     * @param string $code
     * @param string $clientId
     * @param string $redirectUri
     * @return Mage_Oauth2_Model_AuthCode
     * @throws Exception if authorization code is invalid
     */
    protected function validateAuthorizationCode($code, $clientId, $redirectUri)
    {
        $authCode = Mage::getModel('oauth2/authCode')->load($code, 'authorization_code');
        if (!$authCode->getId() || 
            $authCode->getClientId() != $clientId || 
            $authCode->getRedirectUri() != $redirectUri || 
            $authCode->getExpiresIn() < time() || 
            $authCode->getUsed()) {
            throw new Exception('Invalid authorization code, try to authorize again', 400);
        }
        return $authCode;
    }

    /**
     * Handle refresh token grant type
     *
     * @return array Token response
     * @throws Exception if refresh token is invalid
     */
    protected function handleRefreshTokenGrant()
    {
        $refreshToken = $this->getRequest()->getParam('refresh_token');
        $clientId = $this->getRequest()->getParam('client_id');

        $token = $this->validateRefreshToken($refreshToken, $clientId);

        $newToken = $this->tokenService->refreshToken($token);
        $token->delete();

        return $this->formatTokenResponse($newToken);
    }

    /**
     * Validate refresh token
     *
     * @param string $refreshToken
     * @param string $clientId
     * @return Mage_Oauth2_Model_AccessToken
     * @throws Exception if refresh token is invalid
     */
    protected function validateRefreshToken($refreshToken, $clientId)
    {
        $token = Mage::getModel('oauth2/accessToken')->load($refreshToken, 'refresh_token');
        if (!$token->getId() || $token->getClientId() != $clientId) {
            throw new Exception('Invalid refresh token', 400);
        }
        return $token;
    }

    /**
     * Format token response
     *
     * @param Mage_Oauth2_Model_AccessToken $token
     * @return array
     */
    protected function formatTokenResponse($token)
    {
        return [
            'access_token' => $token->getAccessToken(),
            'token_type' => 'Bearer',
            'expires_in' => $token->getExpiresIn(),
            'refresh_token' => $token->getRefreshToken(),
        ];
    }
}