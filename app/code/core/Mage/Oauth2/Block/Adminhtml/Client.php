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

        $this->_headerText = $this->__('Manage OAuth2 Clients');
        $this->_addButtonLabel = $this->__('Add New Client');

        parent::__construct();
    }
}
