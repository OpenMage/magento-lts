<?php

/**
 * OAuth2 Client Admin Grid Container
 */
class Mage_Oauth2_Block_Adminhtml_Client extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'oauth2';
        $this->_controller = 'adminhtml_client';

        $helper = Mage::helper('oauth2');
        $this->_headerText = $helper->__('Manage OAuth2 Clients');
        $this->_addButtonLabel = $helper->__('Add New Client');

        parent::__construct();
    }
}
