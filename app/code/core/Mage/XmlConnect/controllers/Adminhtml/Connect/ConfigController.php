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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Adminhtml mobile configuration controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Adminhtml_Connect_ConfigController extends Mage_XmlConnect_Controller_AdminAction
{
    /**
     * Configuration action
     */
    public function indexAction()
    {
        try {
            if (!$this->_initCookies()) {
                return;
            }
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading configuration.')
            );
        }
    }

    /**
     * Localization action
     */
    public function localizationAction()
    {
        try {
            $this->loadLayout(false);
            $this->renderLayout();
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_message(Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_USER_SPACE_DEFAULT, $e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_message(
                Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_SERVER_SP_DEFAULT,
                $this->__('An error occurred while loading localization.')
            );
        }
    }

    /**
     * Set admin application cookies
     *
     * Set application cookies: application code and device screen size.
     *
     * @return bool
     */
    protected function _initCookies()
    {
        $cookieToSetArray = array(
            array(
                'cookieName'    => self::DEVICE_TYPE_COOKIE_NAME,
                'paramName'     => self::DEVICE_TYPE_COOKIE_NAME,
            ),
            array(
                'cookieName'    => Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_NAME,
                'paramName'     => Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_NAME,
        ));

        $cookieExpireOffset = 3600 * 24 * 30;
        foreach ($cookieToSetArray as $item) {
            if ($this->getRequest()->getParam($item['paramName'], false)) {
                Mage::getSingleton('core/cookie')->set(
                    $item['cookieName'],
                    $this->getRequest()->getParam($item['paramName']),
                    $cookieExpireOffset, '/', null, null, true
                );
            } else {
                $this->_message(
                    Mage_XmlConnect_Model_Simplexml_Message_Error::ERROR_CLIENT_SP_DEFAULT,
                    $this->__('Request param %s is missed', $item['paramName'])
                );
                return false;
            }
        }
        return true;
    }

    /**
     * Is check cookie required flag
     *
     * @return bool
     */
    protected function _isCheckCookieRequired()
    {
        return false;
    }
}
