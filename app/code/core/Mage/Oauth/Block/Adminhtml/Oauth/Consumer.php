<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * OAuth consumers grid container block
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_headerText = Mage::helper('adminhtml')->__('OAuth Consumers');

        //check allow edit
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if (!$session->isAllowed('system/oauth/consumer/edit')) {
            $this->_removeButton('add');
        }
    }
}
