<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Base adminhtml controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Controller_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Name of "is URLs checked" flag
     */
    public const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * Session namespace to refer in other places
     */
    public const SESSION_NAMESPACE = 'adminhtml';

    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'admin';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = [];

    /**
     *Array of actions which can't be processed without form key validation
     *
     * @var array
     */
    protected $_forcedFormKeyActions = [];

    /**
     * Used module name in current adminhtml controller
     */
    protected $_usedModuleName = 'adminhtml';

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'adminhtml';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(static::ADMIN_RESOURCE);
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Retrieve base admihtml helper
     *
     * @return Mage_Adminhtml_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('adminhtml');
    }

    /**
     * Define active menu item in menu block
     *
     * @return $this
     */
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _addBreadcrumb($label, $title, $link = null)
    {
        /** @var Mage_Adminhtml_Block_Widget_Breadcrumbs $block */
        $block = $this->getLayout()->getBlock('breadcrumbs');
        $block->addLink($label, $title, $link);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('content')->append($block);
        return $this;
    }

    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('left')->append($block);
        return $this;
    }

    protected function _addJs(Mage_Core_Block_Abstract $block)
    {
        $this->getLayout()->getBlock('js')->append($block);
        return $this;
    }

    /**
     * Controller pre-dispatch method
     *
     * @return $this
     */
    public function preDispatch()
    {
        // get legacy theme choice form backend config
        if (Mage::getStoreConfigFlag('admin/design/use_legacy_theme')) {
            $theme = Mage::getConfig()->getNode('stores/admin/design/theme/default');
        } else {
            $theme = Mage::getConfig()->getNode('stores/admin/design/theme/openmage');
        }

        Mage::getDesign()
            ->setArea($this->_currentArea)
            ->setPackageName((string) Mage::getConfig()->getNode('stores/admin/design/package/name'))
            ->setTheme((string) $theme);
        foreach (['layout', 'template', 'skin', 'locale'] as $type) {
            if ($value = (string) Mage::getConfig()->getNode("stores/admin/design/theme/{$type}")) {
                Mage::getDesign()->setTheme($type, $value);
            }
        }

        $this->getLayout()->setArea($this->_currentArea);

        Mage::dispatchEvent('adminhtml_controller_action_predispatch_start', []);
        parent::preDispatch();
        $isValidFormKey = true;
        $isValidSecretKey = true;
        $keyErrorMsg = '';
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if ($this->getRequest()->isPost() || $this->_checkIsForcedFormKeyAction()) {
                $isValidFormKey = $this->_validateFormKey();
                $keyErrorMsg = Mage::helper('adminhtml')->__('Invalid Form Key. Please refresh the page.');
            } elseif (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
                $isValidSecretKey = $this->_validateSecretKey();
                $keyErrorMsg = Mage::helper('adminhtml')->__('Invalid Secret Key. Please refresh the page.');
            }
        }

        if (!$isValidFormKey || !$isValidSecretKey) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
            if ($this->getRequest()->getQuery('isAjax', false) || $this->getRequest()->getQuery('ajax', false)) {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode([
                    'error' => true,
                    'message' => $keyErrorMsg,
                ]));
            } else {
                if (!$isValidFormKey) {
                    Mage::getSingleton('adminhtml/session')->addError($keyErrorMsg);
                }

                $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
            }

            return $this;
        }

        if ($this->getRequest()->isDispatched()
            && $this->getRequest()->getActionName() !== 'denied'
            && !$this->_isAllowed()
        ) {
            $this->_forward('denied');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        if (!$this->getFlag('', self::FLAG_IS_URLS_CHECKED)
            && !$this->getRequest()->getParam('forwarded')
            && !$this->_getSession()->getIsUrlNotice(true)
            && !Mage::getConfig()->getNode('global/can_use_base_url')
        ) {
            //$this->_checkUrlSettings();
            $this->setFlag('', self::FLAG_IS_URLS_CHECKED, true);
        }

        if (is_null(Mage::getSingleton('adminhtml/session')->getLocale())) {
            Mage::getSingleton('adminhtml/session')->setLocale(Mage::app()->getLocale()->getLocaleCode());
        }

        return $this;
    }

    /**
     * @deprecated after 1.4.0.0 alpha, logic moved to Mage_Adminhtml_Block_Notification_Baseurl
     * @return $this
     */
    protected function _checkUrlSettings()
    {
        /**
         * Don't check for data saving actions
         */
        if ($this->getRequest()->getPost() || $this->getRequest()->getQuery('isAjax')) {
            return $this;
        }

        $configData = Mage::getModel('core/config_data');

        $defaultUnsecure = (string) Mage::getConfig()->getNode(
            'default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL,
        );
        $defaultSecure = (string) Mage::getConfig()->getNode(
            'default/' . Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL,
        );

        if ($defaultSecure == '{{base_url}}' || $defaultUnsecure == '{{base_url}}') {
            $this->_getSession()->addNotice(
                $this->__('{{base_url}} is not recommended to use in a production environment to declare the Base Unsecure URL / Base Secure URL. It is highly recommended to change this value in your Magento <a href="%s">configuration</a>.', $this->getUrl('adminhtml/system_config/edit', ['section' => 'web'])),
            );
            return $this;
        }

        $dataCollection = $configData->getCollection()
            ->addValueFilter('{{base_url}}');

        $url = false;
        foreach ($dataCollection as $data) {
            if ($data->getScope() == 'stores') {
                $code = Mage::app()->getStore($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'web', 'store' => $code]);
            }

            if ($data->getScope() == 'websites') {
                $code = Mage::app()->getWebsite($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'web', 'website' => $code]);
            }

            if ($url) {
                $this->_getSession()->addNotice(
                    $this->__('{{base_url}} is not recommended to use in a production environment to declare the Base Unsecure URL / Base Secure URL. It is highly recommended to change this value in your Magento <a href="%s">configuration</a>.', $url),
                );
                return $this;
            }
        }

        return $this;
    }

    public function deniedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '403 Forbidden');
        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->_redirect('*/index/login');
            return;
        }

        $this->loadLayout(['default', 'adminhtml_denied']);
        $this->renderLayout();
    }

    public function loadLayout($ids = null, $generateBlocks = true, $generateXml = true)
    {
        parent::loadLayout($ids, $generateBlocks, $generateXml);
        $this->_initLayoutMessages('adminhtml/session');
        return $this;
    }

    public function norouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');
        $this->loadLayout(['default', 'adminhtml_noroute']);
        $this->renderLayout();
    }

    /**
     * Retrieve currently used module name
     *
     * @return string
     */
    public function getUsedModuleName()
    {
        return $this->_usedModuleName;
    }

    /**
     * Set currently used module name
     *
     * @param string $moduleName
     * @return $this
     */
    public function setUsedModuleName($moduleName)
    {
        $this->_usedModuleName = $moduleName;
        return $this;
    }

    /**
     * Translate a phrase
     *
     * @return string
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getUsedModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * Set referer url for redirect in response
     *
     * Is overridden here to set defaultUrl to admin url
     *
     * @param   string $defaultUrl
     * @return  Mage_Adminhtml_Controller_Action
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        $defaultUrl = empty($defaultUrl) ? $this->getUrl('*') : $defaultUrl;
        parent::_redirectReferer($defaultUrl);
        return $this;
    }

    /**
     * Set redirect into response
     *
     * @param string $path
     * @param array $arguments
     * @return $this
     */
    protected function _redirect($path, $arguments = [])
    {
        $this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($this->getUrl($path, $arguments));
        return $this;
    }

    protected function _forward($action, $controller = null, $module = null, ?array $params = null)
    {
        $this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        return parent::_forward($action, $controller, $module, $params);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return Mage::helper('adminhtml')->getUrl($route, $params);
    }

    /**
     * Validate Secret Key
     *
     * @return bool
     */
    protected function _validateSecretKey()
    {
        if (is_array($this->_publicActions) && in_array($this->getRequest()->getActionName(), $this->_publicActions)) {
            return true;
        }

        if (!($secretKey = $this->getRequest()->getParam(Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME, null))
            || !hash_equals(Mage::getSingleton('adminhtml/url')->getSecretKey(), $secretKey)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate password for current admin user
     *
     * @param string $password - current password
     *
     * @return mixed - returns true or array of errors
     */
    protected function _validateCurrentPassword($password)
    {
        $user = Mage::getSingleton('admin/session')->getUser();
        return $user->validateCurrentPassword($password);
    }

    /**
     * Check forced use form key for action
     *
     *  @return bool
     */
    protected function _checkIsForcedFormKeyAction()
    {
        return in_array(
            strtolower($this->getRequest()->getActionName()),
            array_map('strtolower', $this->_forcedFormKeyActions),
        );
    }

    /**
     * Set actions name for forced use form key if "Secret Key to URLs" disabled
     *
     * @param array | string $actionNames - action names for forced use form key
     */
    protected function _setForcedFormKeyActions($actionNames)
    {
        if (!Mage::helper('adminhtml')->isEnabledSecurityKeyUrl()) {
            $actionNames = (is_array($actionNames)) ? $actionNames : (array) $actionNames;
            $actionNames = array_merge($this->_forcedFormKeyActions, $actionNames);
            $actionNames = array_unique($actionNames);
            $this->_forcedFormKeyActions = $actionNames;
        }
    }

    /**
     * Validate request parameter
     *
     * @param string $param - request parameter
     * @param string $pattern - pattern that should be contained in parameter
     *
     * @return bool
     */
    protected function _validateRequestParam($param, $pattern = '')
    {
        $pattern = empty($pattern) ? '/^[a-z0-9\-\_\/]*$/si' : $pattern;
        if (preg_match($pattern, $param)) {
            return true;
        }

        return false;
    }

    /**
     * Validate request parameters
     *
     * @param array $params - array of request parameters
     * @param string $pattern - pattern that should be contained in parameter
     *
     * @return bool
     */
    protected function _validateRequestParams($params, $pattern = '')
    {
        foreach ($params as $param) {
            if (!$this->_validateRequestParam($param, $pattern)) {
                return false;
            }
        }

        return true;
    }
}
