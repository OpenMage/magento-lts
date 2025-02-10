<?php
/**
 * OAuth admin authorization block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Authorize extends Mage_Oauth_Block_AuthorizeBaseAbstract
{
    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Retrieve admin form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/*');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getIdentityLabel()
    {
        return $this->__('User Name');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getFormTitle()
    {
        return $this->__('Log in as admin');
    }

    /**
     * Retrieve reject application authorization URL
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'adminhtml/oauth_authorize/reject';
    }
}
