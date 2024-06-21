<?php

/**
 * OAuth2 Device Admin Block
 */
class Mage_Oauth2_Block_Adminhtml_Device extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_controller = 'adminhtml_device';
        $this->setTemplate('oauth2/verify.phtml');
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/oauth2_device/authorize');
    }
}