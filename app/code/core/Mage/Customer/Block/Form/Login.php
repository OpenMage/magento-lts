<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer login form block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setCreateAccountUrl(string $value)
 */
class Mage_Customer_Block_Form_Login extends Mage_Core_Block_Template
{
    private $_username = -1;

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Customer Login'));
        return parent::_prepareLayout();
    }

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
            /** @var string $pathInfo 'something.html'|'/'|'/path0'|'/path0/path1/'|'/path0/path1/path2'|etc */
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
