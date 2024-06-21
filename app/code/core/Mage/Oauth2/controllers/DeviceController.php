<?php

/**
 * OAuth2 Device Controller
 */
class Mage_Oauth2_DeviceController extends Mage_Oauth2_Controller_BaseController
{
    /**
     * Request action - Generate device and user codes
     */
    public function requestAction()
    {
        $clientId = $this->getRequest()->getParam('client_id');
        $client = $this->_loadClient($clientId);

        if (!$client->getId()) {
            $this->_sendResponse(400, 'Invalid client');
            return;
        }

        $deviceCode = $this->_generateDeviceCode($clientId);
        $userCode = $this->_generateUserCode();

        $this->_saveDeviceCode($deviceCode, $userCode, $clientId);
        $this->_sendSuccessResponse($deviceCode, $userCode);
    }

    /**
     * Verify action - Display verification form
     */
    public function verifyAction()
    {
        $userCode = $this->getRequest()->getParam('user_code');
        $deviceCodeModel = $this->_loadDeviceCode($userCode, 'user_code');

        if (!$this->_isValidDeviceCode($deviceCodeModel)) {
            $this->_sendResponse(400, 'Invalid or expired user code');
            return;
        }

        $this->_renderVerificationForm($userCode);
    }

    /**
     * Authorize action - Process device authorization
     */
    public function authorizeAction()
    {
        if (!$this->_isCustomerLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        }

        $userCode = $this->getRequest()->getParam('user_code');
        $deviceCodeModel = $this->_loadDeviceCode($userCode, 'user_code');

        try {
            $this->_authorizeDevice($deviceCodeModel);
            Mage::getSingleton('core/session')->addSuccess('Authorization approved');
            $this->_redirect('/');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('oauth2/device/verify');
        }
    }

    /**
     * Poll action - Check authorization status and provide access token
     */
    public function pollAction()
    {
        $deviceCode = $this->getRequest()->getParam('device_code');
        $deviceCodeModel = $this->_loadDeviceCode($deviceCode, 'device_code');

        if (!$this->_isValidDeviceCode($deviceCodeModel)) {
            $this->_sendResponse(400, 'Invalid or expired device code');
            return;
        }

        if ($deviceCodeModel->getAuthorized()) {
            $accessToken = $this->_generateAccessToken($deviceCodeModel->getClientId(), $deviceCodeModel->getCustomerId());
            $this->_sendResponse(200, 'Success', ['access_token' => $accessToken]);
        } else {
            $this->_sendResponse(202, 'Authorization pending');
        }
    }

    /**
     * Load client by ID
     *
     * @param string $clientId
     * @return Mage_Oauth2_Model_Client
     */
    protected function _loadClient($clientId)
    {
        return Mage::getModel('oauth2/client')->load($clientId, 'entity_id');
    }

    /**
     * Generate device code
     *
     * @param string $clientId
     * @return string
     */
    protected function _generateDeviceCode($clientId)
    {
        return uniqid($clientId);
    }

    /**
     * Generate user code
     *
     * @return string
     */
    protected function _generateUserCode()
    {
        return strtoupper(bin2hex(random_bytes(3)));
    }

    /**
     * Save device code
     *
     * @param string $deviceCode
     * @param string $userCode
     * @param string $clientId
     */
    protected function _saveDeviceCode($deviceCode, $userCode, $clientId)
    {
        Mage::getModel('oauth2/deviceCode')
            ->setDeviceCode($deviceCode)
            ->setUserCode($userCode)
            ->setClientId($clientId)
            ->setExpiresIn(time() + 600)
            ->setAuthorized(false)
            ->save();
    }

    /**
     * Send success response for device code request
     *
     * @param string $deviceCode
     * @param string $userCode
     */
    protected function _sendSuccessResponse($deviceCode, $userCode)
    {
        $this->_sendResponse(200, 'Success', [
            'device_code' => $deviceCode,
            'user_code' => $userCode,
            'verification_uri' => Mage::getUrl('oauth2/device/verify')
        ]);
    }

    /**
     * Load device code model
     *
     * @param string $code
     * @param string $field
     * @return Mage_Oauth2_Model_DeviceCode
     */
    protected function _loadDeviceCode($code, $field)
    {
        return Mage::getModel('oauth2/deviceCode')->load($code, $field);
    }

    /**
     * Check if device code is valid
     *
     * @param Mage_Oauth2_Model_DeviceCode $deviceCodeModel
     * @return bool
     */
    protected function _isValidDeviceCode($deviceCodeModel)
    {
        return $deviceCodeModel->getId() && $deviceCodeModel->getExpiresIn() >= time();
    }

    /**
     * Render verification form
     *
     * @param string $userCode
     */
    protected function _renderVerificationForm($userCode)
    {
        Mage::register('current_device_code', $userCode);
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('oauth2/device_verify', 'oauth2.device.verify')
        );
        $this->renderLayout();
        Mage::unregister('current_device_code');
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    protected function _isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Authorize device
     *
     * @param Mage_Oauth2_Model_DeviceCode $deviceCodeModel
     */
    protected function _authorizeDevice($deviceCodeModel)
    {
        $deviceCodeModel->setAuthorized(true)
            ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
            ->save();
    }

    /**
     * Generate access token
     *
     * @param string $clientId
     * @param int $customerId
     * @return string
     */
    protected function _generateAccessToken($clientId, $customerId)
    {
        $model = Mage::getModel('oauth2/accessToken')->load($clientId, 'client_id');

        if ($model->getId() && $model->getExpiresIn() > time()) {
            return $model->getAccessToken();
        }

        $helper = Mage::helper('oauth2');
        $model->setAccessToken($helper->generateToken($clientId))
            ->setClientId($clientId)
            ->setCustomerId($customerId)
            ->setRefreshToken($helper->generateToken($clientId))
            ->setExpiresIn(time() + 3600)
            ->save();

        return $model->getAccessToken();
    }
}
