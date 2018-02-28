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
 * XmlConnect controller abstract
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * Message status `error`
     */
    const MESSAGE_STATUS_ERROR      = 'error';

    /**
     * Message status `warning`
     */
    const MESSAGE_STATUS_WARNING    = 'warning';

    /**
     * Message status `success`
     */
    const MESSAGE_STATUS_SUCCESS    = 'success';

    /**
     * Message type `alert`
     */
    const MESSAGE_TYPE_ALERT        = 'alert';

    /**
     * Message type `prompt`
     */
    const MESSAGE_TYPE_PROMPT       = 'prompt';

    /**
     * Declare content type header
     * Validate current application
     *
     * @return null
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        /**
         * Load application by specified code and make sure that application exists
         */
        $cookieName = Mage_XmlConnect_Model_Application::APP_CODE_COOKIE_NAME;
        $appCode    = isset($_COOKIE[$cookieName]) ? (string) $_COOKIE[$cookieName] : '';
        $screenSizeCookieName = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_NAME;
        $screenSize = isset($_COOKIE[$screenSizeCookieName]) ? (string) $_COOKIE[$screenSizeCookieName] : '';
        if (!$appCode) {
            $this->_message(Mage::helper('xmlconnect')->__('Specified invalid app code.'), self::MESSAGE_STATUS_ERROR);
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        /**
         * Check is website offline
         */
        if ((int)Mage::getStoreConfig('general/restriction/is_active')
            && (int)Mage::getStoreConfig('general/restriction/mode') == 0
        ) {
            $this->_message(
                Mage::helper('xmlconnect')->__('Website is offline.'), self::MESSAGE_STATUS_SUCCESS
            );
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        /** @var $appModel Mage_XmlConnect_Model_Application */
        $appModel = Mage::getModel('xmlconnect/application')->loadByCode($appCode);
        $appModel->setScreenSize($screenSize);
        if ($appModel && $appModel->getId()) {
            Mage::app()->setCurrentStore(Mage::app()->getStore($appModel->getStoreId())->getCode());
            Mage::getSingleton('core/locale')->emulate($appModel->getStoreId());
            Mage::register('current_app', $appModel, true);
        } else {
            $this->_message(Mage::helper('xmlconnect')->__('Specified invalid app code.'), self::MESSAGE_STATUS_ERROR);
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
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
        if (empty($body) && !$this->getFlag('forwarded')) {
            $this->_message(
                Mage::helper('xmlconnect')->__('An error occurred while processing your request.'),
                self::MESSAGE_STATUS_ERROR
            );
        }
    }

    /**
     * Generate message xml and set it to response body
     *
     * @param string $text
     * @param string $status
     * @param array $children
     * @return null
     */
    protected function _message($text, $status, $children = array())
    {
        /** @var $message Mage_XmlConnect_Model_Simplexml_Element */
        $message = Mage::getModel('xmlconnect/simplexml_element', '<message></message>');
        $message->addCustomChild('status', $status);
        $message->addCustomChild('text', $text);

        foreach ($children as $node => $value) {
            $message->addCustomChild($node, $value);
        }

        $this->getResponse()->setBody($message->asNiceXml());
    }

    /**
     * Throw control to different action (control and module if was specified).
     *
     * @param string $action
     * @param string|null $controller
     * @param string|null $module
     * @param array|null $params
     * @return null
     */
    protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        $this->setFlag('', 'forwarded', true);
        return parent::_forward($action, $controller, $module, $params);
    }

    /**
     * Check api version and forward if equal
     *
     * @param string $action
     * @param string $apiVersion
     * @return bool
     */
    protected function _checkApiForward($action, $apiVersion)
    {
        if (Mage::helper('xmlconnect')->checkApiVersion($apiVersion)) {
            $this->_forward($action);
            return true;
        }
        return false;
    }
}
