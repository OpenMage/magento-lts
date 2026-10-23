<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth authorization block
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Authorize extends Mage_Oauth_Block_AuthorizeBaseAbstract
{
    /**
     * Retrieve customer form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        $url = $helper->getLoginPostUrl();
        if ($this->getIsSimple()) {
            if (strstr($url, '?')) {
                $url .= '&simple=1';
            } else {
                $url = rtrim($url, '/') . '/simple/1';
            }
        }

        return $url;
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getIdentityLabel()
    {
        return $this->__('Email Address');
    }

    /**
     * Get form identity label
     *
     * @return string
     */
    public function getFormTitle()
    {
        return $this->__('Log in as customer');
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    public function getRejectUrlPath()
    {
        return 'oauth/authorize/reject';
    }
}
