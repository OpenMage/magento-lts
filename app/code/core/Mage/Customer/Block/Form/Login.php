<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer login form block
 *
 * @package    Mage_Customer
 *
 * @method $this setCreateAccountUrl(string $value)
 */
class Mage_Customer_Block_Form_Login extends Mage_Core_Block_Template
{
    private $_username = -1;

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        return $helper->getLoginPostUrl();
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            /** @var Mage_Customer_Helper_Data $helper */
            $helper = $this->helper('customer');
            $url = $helper->getRegisterUrl();
        }
        return $url;
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        return $helper->getForgotPasswordUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if ($this->_username === -1) {
            $this->_username = Mage::getSingleton('customer/session')->getUsername(true);
        }
        return $this->_username;
    }

    /**
     * Can show the login form?
     * For mini login, which can be login from any page, which should be SSL enabled.
     *
     * @return bool
     */
    public function canShowLogin()
    {
        if (Mage::helper('customer')->isLoggedIn()) {
            return false;
        }

        // Set redirect URL after login
        if (Mage::getStoreConfigFlag(Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)) {
            $url = Mage::helper('customer')->getDashboardUrl();
        } else {
            $pathInfo = $this->getRequest()->getOriginalPathInfo();
            if (strtolower(substr($pathInfo, -5)) === '.html') {
                // For URL rewrite, preserve the path without considering query or post.
                $url = Mage::getBaseUrl() . ltrim($pathInfo, '/');
            } else {
                /**
                 * If login in homepage, $pathInfo === '/', $url becomes 'cms/index/index/',
                 * this prevents redirection to My Account after login
                 * @see Mage_Customer_AccountController::_loginPostRedirect()
                 *
                 * Login in all other pages should redirect correctly.
                 */
                $url = $this->getUrl('*/*/*', ['_current' => true]);
            }
        }
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);

        return true;
    }
}
