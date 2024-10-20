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
     * Authorize action - Process device authorization
     */
    public function authorizeAction()
    {
        $userCode = $this->getRequest()->getParam('user_code');
        $clientSecret = $this->getRequest()->getParam('client_secret');
        $clientId = $this->getRequest()->getParam('client_id');
        $userType = $this->getRequest()->getParam('user_type');
        $id = $this->getRequest()->getParam('id');
        $email = $this->getRequest()->getParam('email');
        $client = $this->_loadClient($clientId);

        if (!$client->getId() || $client->getSecret() !== $clientSecret) {
            $this->_sendResponse(400, 'Invalid client');
            return;
        }

        if ($id && $email && $userType) {
            $admin = $customer = null;
            if ($userType === 'admin') {
                $admin = Mage::getModel('admin/user')->load($id);
                if (!$admin->getId() || $admin->getEmail() !== $email || !$admin->getIsActive()) {
                    $this->_sendResponse(400, 'Invalid admin');
                    return;
                }
            } elseif ($userType === 'customer') {
                $customer = Mage::getModel('customer/customer')->load($id);
                if (!$customer->getId() || $customer->getEmail() !== $email) {
                    $this->_sendResponse(400, 'Invalid customer');
                    return;
                }
            }
        } else {
            $this->_sendResponse(400, 'Invalid parameters');
            return;
        }
        $deviceCodeModel = $this->_loadDeviceCode($userCode, 'user_code');

        try {
            $deviceCodeModel->setAuthorized(true);
            if ($admin) {
                $deviceCodeModel->setAdminId($admin->getId());
            } elseif ($customer) {
                $deviceCodeModel->setCustomerId($customer->getId());
            }
            $deviceCodeModel->save();

            $this->_sendResponse(200, 'Success');
        } catch (Exception $e) {
            $this->_sendResponse(500, 'Failed to authorize device, contact administrator');
            Mage::logException($e);
        }
    }

    /**
     * Poll action - Check authorization status and provide access token
     */
    public function pollAction()
    {
        $deviceCode = $this->getRequest()->getParam('device_code');
        $userType = $this->getRequest()->getParam('user_type');
        $id = $this->getRequest()->getParam('id');
        $deviceCodeModel = $this->_loadDeviceCode($deviceCode, 'device_code');

        if (!$this->_isValidDeviceCode($deviceCodeModel)) {
            $this->_sendResponse(400, 'Invalid or expired device code');
            return;
        }

        if (!$id || !$userType) {
            $this->_sendResponse(400, 'Invalid parameters');
            return;
        }
        if ($userType === 'admin') {
            if (!$deviceCodeModel->getAdminId() || $deviceCodeModel->getAdminId() !== $id) {
                $this->_sendResponse(400, 'Invalid admin');
                return;
            }
        } elseif ($userType === 'customer') {
            if (!$deviceCodeModel->getCustomerId() || $deviceCodeModel->getCustomerId() !== $id) {
                $this->_sendResponse(400, 'Invalid customer');
                return;
            }
        }

        if ($deviceCodeModel->getAuthorized()) {
            $model = $this->_generateAccessToken($deviceCodeModel->getClientId(), $deviceCodeModel->getAdminId(), $deviceCodeModel->getCustomerId());
            $this->_sendResponse(200, 'Success', [
                'access_token' => $model->getAccessToken(),
                'token_type' => 'Bearer',
                'expires_in' => $model->getExpiresIn(),
                'refresh_token' => $model->getRefreshToken(),
            ]);
            $deviceCodeModel->delete();
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
     * Generate access token
     *
     * @param string $clientId
     * @param int $customerId
     * @return Mage_Oauth2_Model_AccessToken
     */
    protected function _generateAccessToken($clientId, $adminId, $customerId)
    {
        $model = Mage::getModel('oauth2/accessToken')->load($clientId, 'client_id');

        if ($model->getId() && $model->getExpiresIn() > time()) {
            return $model;
        }

        $helper = Mage::helper('oauth2');
        $model->setAccessToken($helper->generateToken())
            ->setClientId($clientId)
            ->setAdminId($adminId)
            ->setCustomerId($customerId)
            ->setRefreshToken($helper->generateToken())
            ->setExpiresIn(time() + 3600)
            ->save();

        return $model;
    }
}
