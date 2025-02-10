<?php
/**
 * OAuth authorized tokens grid container block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_AuthorizedTokens extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Construct grid container
     */
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oauth_authorizedTokens';
        $this->_headerText = Mage::helper('adminhtml')->__('Authorized OAuth Tokens');

        $this->_removeButton('add');
    }
}
