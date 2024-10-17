<?php

/**
 * OAuth2 Authorization Block
 */
class Mage_Oauth2_Block_Authorize extends Mage_Core_Block_Template
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('oauth2/authorize.phtml');
    }

    /**
     * Get OAuth2 client
     *
     * @return Mage_Oauth2_Model_Client
     */
    public function getClient()
    {
        $clientId = $this->getRequest()->getParam('client_id');
        return Mage::getModel('oauth2/client')->load($clientId, 'entity_id');
    }

    /**
     * Get state parameter
     *
     * @return string|null
     */
    public function getState()
    {
        return $this->getRequest()->getParam('state');
    }

    /**
     * Get redirect URI
     *
     * @return string|null
     */
    public function getRedirectUri()
    {
        return $this->getRequest()->getParam('redirect_uri');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/index', ['_secure' => true]);
    }
}
