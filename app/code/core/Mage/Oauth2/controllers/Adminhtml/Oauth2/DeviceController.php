<?php

/**
 * OAuth2 Device Controller for Magento Admin Panel
 */
class Mage_Oauth2_Adminhtml_Oauth2_DeviceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $_session;

    /**
     * Get admin session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        if ($this->_session === null) {
            $this->_session = Mage::getSingleton('adminhtml/session');
        }
        return $this->_session;
    }

    /**
     * Index action - display list of OAuth2 devices
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('oauth2/device');
        $block = $this->getLayout()->createBlock('oauth2/adminhtml_device');
        $this->_addContent($block);
        $this->renderLayout();
    }

    /**
     * Authorize action - handle device authorization
     */
    public function authorizeAction()
    {
        $userCode = $this->getRequest()->getParam('user_code');
        $deviceCodeModel = Mage::getModel('oauth2/deviceCode')->load($userCode, 'user_code');

        try {
            $this->_validateDeviceCode($deviceCodeModel);
            $accessToken = $this->_authorizeDevice($deviceCodeModel);
            $this->_setSuccessMessages($accessToken);
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Validate device code
     *
     * @param Mage_Core_Model_Abstract $deviceCodeModel
     * @throws Exception
     */
    protected function _validateDeviceCode($deviceCodeModel)
    {
        if (!$deviceCodeModel->getId() || $deviceCodeModel->getExpiresIn() < time()) {
            throw new Exception('Invalid or expired user code');
        }
    }

    /**
     * Authorize device and generate/retrieve access token
     *
     * @param Mage_Core_Model_Abstract $deviceCodeModel
     * @return array
     */
    protected function _authorizeDevice($deviceCodeModel)
    {
        $adminId = Mage::getSingleton('admin/session')->getUser()->getId();
        $deviceCodeModel->setAuthorized(true)->setAdminId($adminId)->save();

        $clientId = $deviceCodeModel->getClientId();
        $accessTokenModel = Mage::getModel('oauth2/accessToken')->load($clientId, 'client_id');

        if ($accessTokenModel->getId() && $accessTokenModel->getExpiresIn() > time()) {
            return [
                'access_token' => $accessTokenModel->getAccessToken(),
                'refresh_token' => $accessTokenModel->getRefreshToken()
            ];
        }

        return $this->_generateNewAccessToken($clientId, $adminId);
    }

    /**
     * Generate new access token
     *
     * @param string $clientId
     * @param int $adminId
     * @return array
     */
    protected function _generateNewAccessToken($clientId, $adminId)
    {
        $helper = Mage::helper('oauth2');
        $accessTokenModel = Mage::getModel('oauth2/accessToken');

        $accessTokenModel->setAccessToken($helper->generateToken($clientId))
            ->setClientId($clientId)
            ->setCustomerId('')
            ->setAdminId($adminId)
            ->setRefreshToken($helper->generateToken($clientId))
            ->setExpiresIn(time() + 3600)
            ->save();

        return [
            'access_token' => $accessTokenModel->getAccessToken(),
            'refresh_token' => $accessTokenModel->getRefreshToken()
        ];
    }

    /**
     * Set success messages
     *
     * @param array $accessToken
     */
    protected function _setSuccessMessages($accessToken)
    {
        $this->_getSession()->addSuccess('Authorization approved, save your tokens, you cannot view them again.');
        $this->_getSession()->addSuccess('Access token created: ' . $accessToken['access_token']);
        $this->_getSession()->addSuccess('Refresh token created: ' . $accessToken['refresh_token']);
    }
}
