<?php

/**
 * OAuth2 Device Verification Block
 */
class Mage_Oauth2_Block_Device_Verify extends Mage_Core_Block_Template
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setTemplate('oauth2/device/verify.phtml');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('oauth2/device/authorize');
    }

    /**
     * Get user code
     *
     * @return string|null
     */
    public function getUserCode()
    {
        return Mage::registry('current_device_code');
    }
}
