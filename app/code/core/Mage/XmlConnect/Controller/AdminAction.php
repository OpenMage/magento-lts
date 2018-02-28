<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect admin application controller abstract
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Controller_AdminAction extends Mage_Adminhtml_Controller_Action
{
    /**
     * Admin application device type cookie
     */
    const DEVICE_TYPE_COOKIE_NAME = 'device_type';

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_XmlConnect_Controller_AdminAction
     */
    public function preDispatch()
    {
        Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
        // override admin store design settings via stores section
        Mage::getDesign()->setArea($this->_currentArea)
            ->setPackageName((string)Mage::getConfig()->getNode('stores/admin/design/package/name'))
            ->setTheme((string)Mage::getConfig()->getNode('stores/admin/design/theme/default'));
        foreach (array('layout', 'template', 'skin', 'locale') as $type) {
            $value = (string)Mage::getConfig()->getNode("stores/admin/design/theme/{$type}");
            if ($value) {
                Mage::getDesign()->setTheme($type, $value);
            }
        }

        $this->getLayout()->setArea($this->_currentArea);

        Mage::dispatchEvent('adminhtml_controller_action_predispatch_start', array());
        Mage_Core_Controller_Varien_Action::preDispatch();

        if ($this->getRequest()->isDispatched() && $this->getRequest()->getActionName() !== 'denied'
            && !$this->_isAllowed()
        ) {
            $this->_forward('denied');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        if (is_null(Mage::getSingleton('adminhtml/session')->getLocale())) {
            Mage::getSingleton('adminhtml/session')->setLocale(Mage::app()->getLocale()->getLocaleCode());
        }

        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        if ($this->_isCheckCookieRequired()) {
            $this->_checkCookie();
        }

        return $this;
    }

    /**
     * Check device cookies
     *
     * @return null
     */
    public function _checkCookie()
    {
        $AdminDeviceCookie  = self::DEVICE_TYPE_COOKIE_NAME;
        $currentDevice      = isset($_COOKIE[$AdminDeviceCookie]) ? (string) $_COOKIE[$AdminDeviceCookie] : '';
        if (!array_key_exists($currentDevice, Mage_XmlConnect_Helper_Data::getSupportedDevices())) {
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_CLIENT_SP_CONFIG_RELOAD_REQUIRED,
                $this->__('Device type doesn\'t recognized.')
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        $screenSizeCookieName = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_NAME;
        $screenSize = isset($_COOKIE[$screenSizeCookieName]) ? (string) $_COOKIE[$screenSizeCookieName] : '';
        if (!$screenSize) {
            $deviceClassName = 'Mage_XmlConnect_Model_Device_' . ucfirst($currentDevice);
            $_COOKIE[$screenSizeCookieName] = constant($deviceClassName . '::SCREEN_SIZE_DEFAULT');
        }
    }

    /**
     * Is check cookie required flag
     *
     * @return bool
     */
    protected function _isCheckCookieRequired()
    {
        return true;
    }

    /**
     * Validate response body
     *
     * @return null
     */
    public function postDispatch()
    {
        parent::postDispatch();
        $body = $this->getResponse()->getBody();
        if (empty($body)) {
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while processing your request.')
            );
        }
    }

    /**
     * Access denied action
     *
     * @return Mage_XmlConnect_Controller_AdminAction|null
     */
    public function deniedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
        if (Mage::getSingleton('xmlconnect/configuration')->isActiveAdminApp()) {
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SP_ACCESS_FORBIDDEN);
        } else {
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SP_ACCESS_FORBIDDEN,
                $this->__('Admin application has not been enabled')
            );
        }
        return $this;
    }

    /**
     * Generate message xml and set it to response body
     *
     * @param string $messageCode
     * @param string $messageText
     * @param array $children
     * @return null
     */
    protected function _message($messageCode, $messageText = null, $children = array())
    {
        /** @var $messageXmlObj Mage_XmlConnect_Model_Simplexml_Message */
        $messageXmlObj = Mage::getModel('xmlconnect/simplexml_message', $messageCode);
        $messageXmlObj->setMessageText($messageText)->setChildren($children);
        $this->getResponse()->setBody($messageXmlObj);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('xmlconnect/admin_connect')
               && Mage::getSingleton('xmlconnect/configuration')->isActiveAdminApp();
    }

    /**
     * Allowed controller actions w/o authorization
     *
     * @return array
     */
    public function getAllowedControllerActions()
    {
        return array('connect_user' => array('loginform', 'login', 'logout', 'denied'));
    }
}
