<?php
/**
 * OAuth authorization block with auth buttons
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Authorize_Button extends Mage_Oauth_Block_Authorize_ButtonBaseAbstract
{
    /**
     * Retrieve confirm authorization url path
     *
     * @return string
     */
    public function getConfirmUrlPath()
    {
        return 'adminhtml/oauth_authorize/confirm';
    }

    /**
     * Retrieve reject authorization url path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'adminhtml/oauth_authorize/reject';
    }
}
