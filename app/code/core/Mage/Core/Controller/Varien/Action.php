<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Zend_Controller_Action class (formally)
 *
 * Allows dispatching before and after events for each controller action
 *
 * @category   Mage
 * @package    Mage_Core
 */
abstract class Mage_Core_Controller_Varien_Action
{
    public const FLAG_NO_CHECK_INSTALLATION    = 'no-install-check';
    public const FLAG_NO_DISPATCH              = 'no-dispatch';
    public const FLAG_NO_PRE_DISPATCH          = 'no-preDispatch';
    public const FLAG_NO_POST_DISPATCH         = 'no-postDispatch';
    public const FLAG_NO_START_SESSION         = 'no-startSession';
    public const FLAG_NO_DISPATCH_BLOCK_EVENT  = 'no-beforeGenerateLayoutBlocksDispatch';
    public const FLAG_NO_COOKIES_REDIRECT      = 'no-cookies-redirect';

    public const PARAM_NAME_SUCCESS_URL        = 'success_url';
    public const PARAM_NAME_ERROR_URL          = 'error_url';
    public const PARAM_NAME_REFERER_URL        = 'referer_url';
    public const PARAM_NAME_BASE64_URL         = 'r64';
    public const PARAM_NAME_URL_ENCODED        = 'uenc';

    public const PROFILER_KEY                  = 'mage::dispatch::controller::action';

    /**
     * Request object
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response object
     *
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * Real module name (like 'Mage_Module')
     *
     * @var string
     */
    protected $_realModuleName;

    /**
     * Action flags
     *
     * for example used to disable rendering default layout
     *
     * @var array
     */
    protected $_flags = [];

    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = [];

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea;

    /**
     * Namespace for session.
     * Should be defined for proper working session.
     *
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * Whether layout is loaded
     *
     * @see self::loadLayout()
     * @var bool
     */
    protected $_isLayoutLoaded = false;

    /**
     * Title parts to be rendered in the page head title
     *
     * @see self::_title()
     * @var array
     */
    protected $_titles = [];

    /**
     * Whether the default title should be removed
     *
     * @see self::_title()
     * @var bool
     */
    protected $_removeDefaultTitle = false;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = [])
    {
        $this->_request = $request;
        $this->_response = $response;

        Mage::app()->getFrontController()->setAction($this);

        $this->_construct();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
    }

    /**
     * @param string $action
     * @return bool
     */
    public function hasAction($action)
    {
        return method_exists($this, $this->getActionMethodName($action));
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Retrieve flag value
     *
     * @param   string $action
     * @param   string $flag
     * @return array|bool
     */
    public function getFlag($action, $flag = '')
    {
        if ($action === '') {
            $action = $this->getRequest()->getActionName();
        }
        if ($flag === '') {
            return $this->_flags;
        } elseif (isset($this->_flags[$action][$flag])) {
            return $this->_flags[$action][$flag];
        } else {
            return false;
        }
    }

    /**
     * Setting flag value
     *
     * @param   string $action
     * @param   string $flag
     * @param   string|int|bool $value
     * @return  $this
     */
    public function setFlag($action, $flag, $value)
    {
        if ($action === '') {
            $action = $this->getRequest()->getActionName();
        }
        $this->_flags[$action][$flag] = $value;
        return $this;
    }

    /**
     * Retrieve full bane of current action current controller and
     * current module
     *
     * @param   string $delimiter
     * @return  string
     */
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRequest()->getRequestedRouteName() . $delimiter .
            $this->getRequest()->getRequestedControllerName() . $delimiter .
            $this->getRequest()->getRequestedActionName();
    }

    /**
     * Retrieve current layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return Mage::getSingleton('core/layout');
    }

    /**
     * Load layout by handles(s)
     *
     * @param   string|null|bool $handles
     * @param   bool $generateBlocks
     * @param   bool $generateXml
     * @return  $this
     */
    public function loadLayout($handles = null, $generateBlocks = true, $generateXml = true)
    {
        // if handles were specified in arguments load them first
        if ($handles !== false && $handles !== '') {
            $this->getLayout()->getUpdate()->addHandle($handles ? $handles : 'default');
        }

        // add default layout handles for this action
        $this->addActionLayoutHandles();

        $this->loadLayoutUpdates();

        if (!$generateXml) {
            return $this;
        }
        $this->generateLayoutXml();

        if (!$generateBlocks) {
            return $this;
        }
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;

        return $this;
    }

    /**
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addActionLayoutHandles()
    {
        $update = $this->getLayout()->getUpdate();

        // load store handle
        $update->addHandle('STORE_' . Mage::app()->getStore()->getCode());

        // load theme handle
        $package = Mage::getSingleton('core/design_package');
        $update->addHandle(
            'THEME_' . $package->getArea() . '_' . $package->getPackageName() . '_' . $package->getTheme('layout')
        );

        // load action handle
        $update->addHandle(strtolower($this->getFullActionName()));

        return $this;
    }

    /**
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadLayoutUpdates()
    {
        $profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();

        // dispatch event for adding handles to layout update
        Mage::dispatchEvent(
            'controller_action_layout_load_before',
            ['action' => $this, 'layout' => $this->getLayout()]
        );

        // load layout updates by specified handles
        Varien_Profiler::start("$profilerKey::layout_load");
        $this->getLayout()->getUpdate()->load();
        Varien_Profiler::stop("$profilerKey::layout_load");

        return $this;
    }

    /**
     * @return $this
     */
    public function generateLayoutXml()
    {
        $profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();
        // dispatch event for adding text layouts
        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            Mage::dispatchEvent(
                'controller_action_layout_generate_xml_before',
                ['action' => $this, 'layout' => $this->getLayout()]
            );
        }

        // generate xml from collected text updates
        Varien_Profiler::start("$profilerKey::layout_generate_xml");
        $this->getLayout()->generateXml();
        Varien_Profiler::stop("$profilerKey::layout_generate_xml");

        return $this;
    }

    /**
     * @return $this
     */
    public function generateLayoutBlocks()
    {
        $profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();
        // dispatch event for adding xml layout elements
        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            Mage::dispatchEvent(
                'controller_action_layout_generate_blocks_before',
                ['action' => $this, 'layout' => $this->getLayout()]
            );
        }

        // generate blocks from xml layout
        Varien_Profiler::start("$profilerKey::layout_generate_blocks");
        $this->getLayout()->generateBlocks();
        Varien_Profiler::stop("$profilerKey::layout_generate_blocks");

        if (!$this->getFlag('', self::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            Mage::dispatchEvent(
                'controller_action_layout_generate_blocks_after',
                ['action' => $this, 'layout' => $this->getLayout()]
            );
        }

        return $this;
    }

    /**
     * Rendering layout
     *
     * @param   string $output
     * @return  $this|void
     */
    public function renderLayout($output = '')
    {
        $profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();

        if ($this->getFlag('', 'no-renderLayout')) {
            return;
        }

        if (Mage::app()->getFrontController()->getNoRender()) {
            return;
        }

        $this->_renderTitles();

        Varien_Profiler::start("$profilerKey::layout_render");

        if ($output !== '') {
            $this->getLayout()->addOutputBlock($output);
        }

        Mage::dispatchEvent('controller_action_layout_render_before');
        Mage::dispatchEvent('controller_action_layout_render_before_' . $this->getFullActionName());

        #ob_implicit_flush();
        $this->getLayout()->setDirectOutput(false);

        $output = $this->getLayout()->getOutput();
        Mage::getSingleton('core/translate_inline')->processResponseBody($output);
        $this->getResponse()->appendBody($output);
        Varien_Profiler::stop("$profilerKey::layout_render");

        return $this;
    }

    /**
     * @param string $action
     */
    public function dispatch($action)
    {
        try {
            $actionMethodName = $this->getActionMethodName($action);
            if (!method_exists($this, $actionMethodName)) {
                $actionMethodName = 'norouteAction';
            }

            Varien_Profiler::start(self::PROFILER_KEY . '::predispatch');
            $this->preDispatch();
            Varien_Profiler::stop(self::PROFILER_KEY . '::predispatch');

            if ($this->getRequest()->isDispatched()) {
                /**
                 * preDispatch() didn't change the action, so we can continue
                 */
                if (!$this->getFlag('', self::FLAG_NO_DISPATCH)) {
                    $profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();

                    Varien_Profiler::start($profilerKey);
                    $this->$actionMethodName();
                    Varien_Profiler::stop($profilerKey);

                    Varien_Profiler::start(self::PROFILER_KEY . '::postdispatch');
                    $this->postDispatch();
                    Varien_Profiler::stop(self::PROFILER_KEY . '::postdispatch');
                }
            }
        } catch (Mage_Core_Controller_Varien_Exception $e) {
            // set prepared flags
            foreach ($e->getResultFlags() as $flagData) {
                list($action, $flag, $value) = $flagData;
                $this->setFlag($action, $flag, $value);
            }
            // call forward, redirect or an action
            list($method, $parameters) = $e->getResultCallback();
            switch ($method) {
                case Mage_Core_Controller_Varien_Exception::RESULT_REDIRECT:
                    list($path, $arguments) = $parameters;
                    $this->_redirect($path, $arguments);
                    break;
                case Mage_Core_Controller_Varien_Exception::RESULT_FORWARD:
                    list($action, $controller, $module, $params) = $parameters;
                    $this->_forward($action, $controller, $module, $params);
                    break;
                default:
                    $actionMethodName = $this->getActionMethodName($method);
                    $this->getRequest()->setActionName($method);
                    $this->$actionMethodName($method);
                    break;
            }
        }
    }

    /**
     * Retrieve action method name
     *
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)
    {
        return $action . 'Action';
    }

    /**
     * Dispatch event before action
     *
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function preDispatch()
    {
        if (!$this->getFlag('', self::FLAG_NO_CHECK_INSTALLATION)) {
            if (!Mage::isInstalled()) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                $this->_redirect('install');
                return;
            }
        }

        // Prohibit disabled store actions
        if (Mage::isInstalled() && !Mage::app()->getStore()->getIsActive()) {
            Mage::app()->throwStoreException(Mage::helper('core')->__('The store view #%d is disabled.', Mage::app()->getStore()->getId()));
        }

        if ($this->_rewrite()) {
            return;
        }

        if (!$this->getFlag('', self::FLAG_NO_START_SESSION)) {
            $checkCookie = in_array($this->getRequest()->getActionName(), $this->_cookieCheckActions)
                && !$this->getRequest()->getParam('nocookie', false);
            $cookies = Mage::getSingleton('core/cookie')->get();
            /** @var Mage_Core_Model_Session $session */
            $session = Mage::getSingleton('core/session', ['name' => $this->_sessionNamespace])->start();

            if (empty($cookies)) {
                if ($session->getCookieShouldBeReceived()) {
                    $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, true);
                    $session->unsCookieShouldBeReceived();
                    $session->setSkipSessionIdFlag(true);
                } elseif ($checkCookie) {
                    if (isset($_GET[$session->getSessionIdQueryParam()]) && Mage::app()->getUseSessionInUrl()
                        && $this->_sessionNamespace != Mage_Adminhtml_Controller_Action::SESSION_NAMESPACE
                    ) {
                        $session->setCookieShouldBeReceived(true);
                    } else {
                        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, true);
                    }
                }
            }
        }

        Mage::app()->loadArea($this->getLayout()->getArea());

        if ($this->getFlag('', self::FLAG_NO_COOKIES_REDIRECT)
            && Mage::getStoreConfig('web/browser_capabilities/cookies')
        ) {
            $this->_forward('noCookies', 'index', 'core');
            return;
        }

        if ($this->getFlag('', self::FLAG_NO_PRE_DISPATCH)) {
            return;
        }

        Mage::dispatchEvent('controller_action_predispatch', ['controller_action' => $this]);
        Mage::dispatchEvent(
            'controller_action_predispatch_' . $this->getRequest()->getRouteName(),
            ['controller_action' => $this]
        );
        Mage::dispatchEvent(
            'controller_action_predispatch_' . $this->getFullActionName(),
            ['controller_action' => $this]
        );
    }

    /**
     * Dispatches event after action
     */
    public function postDispatch()
    {
        if ($this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
            return;
        }

        Mage::dispatchEvent(
            'controller_action_postdispatch_' . $this->getFullActionName(),
            ['controller_action' => $this]
        );
        Mage::dispatchEvent(
            'controller_action_postdispatch_' . $this->getRequest()->getRouteName(),
            ['controller_action' => $this]
        );
        Mage::dispatchEvent('controller_action_postdispatch', ['controller_action' => $this]);
    }

    /**
     * @param mixed|null $coreRoute
     */
    public function norouteAction($coreRoute = null)
    {
        $status = ($this->getRequest()->getParam('__status__'))
            ? $this->getRequest()->getParam('__status__')
            : new Varien_Object();

        Mage::dispatchEvent('controller_action_noroute', ['action' => $this, 'status' => $status]);
        if ($status->getLoaded() !== true
            || $status->getForwarded() === true
            || !is_null($coreRoute)
        ) {
            $this->loadLayout(['default', 'noRoute']);
            $this->renderLayout();
        } else {
            $status->setForwarded(true);
            $this->_forward(
                $status->getForwardAction(),
                $status->getForwardController(),
                $status->getForwardModule(),
                ['__status__' => $status]
            );
        }
    }

    public function noCookiesAction()
    {
        $redirect = new Varien_Object();
        Mage::dispatchEvent('controller_action_nocookies', [
            'action'    => $this,
            'redirect'  => $redirect
        ]);

        if ($url = $redirect->getRedirectUrl()) {
            $this->_redirectUrl($url);
        } elseif ($redirect->getRedirect()) {
            $this->_redirect($redirect->getPath(), $redirect->getArguments());
        } else {
            $this->loadLayout(['default', 'noCookie']);
            $this->renderLayout();
        }

        $this->getRequest()->setDispatched(true);
    }

    /**
     * Throw control to different action (control and module if was specified).
     *
     * @param string $action
     * @param string|null $controller
     * @param string|null $module
     */
    protected function _forward($action, $controller = null, $module = null, ?array $params = null)
    {
        $request = $this->getRequest();

        $request->initForward();

        if (isset($params)) {
            $request->setParams($params);
        }

        if (isset($controller)) {
            $request->setControllerName($controller);

            // Module should only be reset if controller has been specified
            if (isset($module)) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
            ->setDispatched(false);
    }

    /**
     * Initializing layout messages by message storage(s), loading and adding messages to layout messages block
     *
     * @param string|array $messagesStorage
     * @return $this
     */
    protected function _initLayoutMessages($messagesStorage)
    {
        if (!is_array($messagesStorage)) {
            $messagesStorage = [$messagesStorage];
        }
        foreach ($messagesStorage as $storageName) {
            $storage = Mage::getSingleton($storageName);
            if ($storage) {
                $block = $this->getLayout()->getMessagesBlock();
                $block->addMessages($storage->getMessages(true));
                $block->setEscapeMessageFlag($storage->getEscapeMessages(true));
                $block->addStorageType($storageName);
            } else {
                Mage::throwException(
                    Mage::helper('core')->__('Invalid messages storage "%s" for layout messages initialization', (string) $storageName)
                );
            }
        }
        return $this;
    }

    /**
     * Initializing layout messages by message storage(s), loading and adding messages to layout messages block
     *
     * @param string|array $messagesStorage
     * @return $this
     */
    public function initLayoutMessages($messagesStorage)
    {
        return $this->_initLayoutMessages($messagesStorage);
    }

    /**
     * Set redirect url into response
     *
     * @param   string $url
     * @return  $this
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        return $this;
    }

    /**
     * Set redirect into response
     *
     * @param   string $path
     * @param   array $arguments
     * @return  $this
     */
    protected function _redirect($path, $arguments = [])
    {
        return $this->setRedirectWithCookieCheck($path, $arguments);
    }

    /**
     * Set redirect into response with session id in URL if it is enabled.
     * It allows to distinguish primordial request from browser with cookies disabled.
     *
     * @param   string $path
     * @return  $this
     */
    public function setRedirectWithCookieCheck($path, array $arguments = [])
    {
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session', ['name' => $this->_sessionNamespace]);
        if ($session->getCookieShouldBeReceived() && Mage::app()->getUseSessionInUrl()
            && $this->_sessionNamespace != Mage_Adminhtml_Controller_Action::SESSION_NAMESPACE
        ) {
            $arguments += ['_query' => [
                $session->getSessionIdQueryParam() => $session->getSessionId()
            ]];
        }
        $this->getResponse()->setRedirect(Mage::getUrl($path, $arguments));
        return $this;
    }

    /**
     * Redirect to success page
     *
     * @param string $defaultUrl
     * @return $this
     */
    protected function _redirectSuccess($defaultUrl)
    {
        $successUrl = $this->getRequest()->getParam(self::PARAM_NAME_SUCCESS_URL);
        if (empty($successUrl)) {
            $successUrl = $defaultUrl;
        }
        if (!$this->_isUrlInternal($successUrl)) {
            $successUrl = Mage::app()->getStore()->getBaseUrl();
        }
        $this->getResponse()->setRedirect($successUrl);
        return $this;
    }

    /**
     * Redirect to error page
     *
     * @param string $defaultUrl
     * @return  $this
     */
    protected function _redirectError($defaultUrl)
    {
        $errorUrl = $this->getRequest()->getParam(self::PARAM_NAME_ERROR_URL);
        if (empty($errorUrl)) {
            $errorUrl = $defaultUrl;
        }
        if (!$this->_isUrlInternal($errorUrl)) {
            $errorUrl = Mage::app()->getStore()->getBaseUrl();
        }
        $this->getResponse()->setRedirect($errorUrl);
        return $this;
    }

    /**
     * Set referer url for redirect in response
     *
     * @param   string $defaultUrl
     * @return  $this
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
        }

        $this->getResponse()->setRedirect($refererUrl);
        return $this;
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @return string
     */
    protected function _getRefererUrl()
    {
        $refererUrl = $this->getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }
        if ($url = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }
        if (empty($refererUrl) || !$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Check url to be used as internal
     *
     * @param   string $url
     * @return  bool
     */
    protected function _isUrlInternal($url)
    {
        if (str_contains($url, 'http')) {
            /**
             * Url must start from base secure or base unsecure url
             */
            if (str_starts_with($url, Mage::app()->getStore()->getBaseUrl())
                || str_starts_with($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true))
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get real module name (like 'Mage_Module')
     *
     * @return  string
     */
    protected function _getRealModuleName()
    {
        if (empty($this->_realModuleName)) {
            $class = get_class($this);
            $this->_realModuleName = substr(
                $class,
                0,
                strpos(strtolower($class), '_' . strtolower($this->getRequest()->getControllerName() . 'Controller'))
            );
        }
        return $this->_realModuleName;
    }

    /**
     * Support for controllers rewrites
     *
     * Example of configuration:
     * <global>
     *   <routers>
     *     <core_module>
     *       <rewrite>
     *         <core_controller>
     *           <to>new_route/new_controller</to>
     *           <override_actions>true</override_actions>
     *           <actions>
     *             <core_action><to>new_module/new_controller/new_action</core_action>
     *           </actions>
     *         <core_controller>
     *       </rewrite>
     *     </core_module>
     *   </routers>
     * </global>
     *
     * This will override:
     * 1. core_module/core_controller/core_action to new_module/new_controller/new_action
     * 2. all other actions of core_module/core_controller to new_module/new_controller
     *
     * @return bool true if rewrite happened
     */
    protected function _rewrite()
    {
        $route = $this->getRequest()->getRouteName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();

        $rewrite = Mage::getConfig()->getNode('global/routers/' . $route . '/rewrite/' . $controller);
        if (!$rewrite) {
            return false;
        }

        if (!($rewrite->actions && $rewrite->actions->$action) || $rewrite->is('override_actions')) {
            $t = explode('/', (string)$rewrite->to);
            if (count($t) !== 2 || empty($t[0]) || empty($t[1])) {
                return false;
            }
            $t[2] = $action;
        } else {
            $t = explode('/', (string)$rewrite->actions->$action->to);
            if (count($t) !== 3 || empty($t[0]) || empty($t[1]) || empty($t[2])) {
                return false;
            }
        }

        $this->_forward(
            $t[2] === '*' ? $action : $t[2],
            $t[1] === '*' ? $controller : $t[1],
            $t[0] === '*' ? $route : $t[0]
        );

        return true;
    }

    /**
     * Validate Form Key
     *
     * @return bool
     */
    protected function _validateFormKey()
    {
        if (!($formKey = $this->getRequest()->getParam('form_key', null))
            || $formKey != Mage::getSingleton('core/session')->getFormKey()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Add an extra title to the end or one from the end, or remove all
     *
     * Usage examples:
     * $this->_title('foo')->_title('bar');
     * => bar / foo / <default title>
     *
     * $this->_title()->_title('foo')->_title('bar');
     * => bar / foo
     *
     * $this->_title('foo')->_title(false)->_title('bar');
     * bar / <default title>
     *
     * @see self::_renderTitles()
     * @param string|false|int|null $text
     * @param bool $resetIfExists
     * @return $this
     */
    protected function _title($text = null, $resetIfExists = true)
    {
        if (is_string($text)) {
            $this->_titles[] = $text;
        } elseif ($text === -1) {
            if (empty($this->_titles)) {
                $this->_removeDefaultTitle = true;
            } else {
                array_pop($this->_titles);
            }
        } elseif (empty($this->_titles) || $resetIfExists) {
            if ($text === false) {
                $this->_removeDefaultTitle = false;
                $this->_titles = [];
            } elseif ($text === null) {
                $this->_removeDefaultTitle = true;
                $this->_titles = [];
            }
        }
        return $this;
    }

    /**
     * Prepare titles in the 'head' layout block
     * Supposed to work only in actions where layout is rendered
     * Falls back to the default logic if there are no titles eventually
     *
     * @see self::loadLayout()
     * @see self::renderLayout()
     */
    protected function _renderTitles()
    {
        if ($this->_isLayoutLoaded && $this->_titles) {
            $titleBlock = $this->getLayout()->getBlock('head');
            if ($titleBlock) {
                if (!$this->_removeDefaultTitle) {
                    $title = trim($titleBlock->getTitle());
                    if ($title) {
                        array_unshift($this->_titles, $title);
                    }
                }
                $titleBlock->setTitle(implode(' / ', array_reverse($this->_titles)));
            }
        }
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ]);

        foreach ($dateFields as $dateField) {
            if ($dateField && !empty($array[$dateField])) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    /**
     * Convert dates with time in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDateTime($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format' => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format' => Varien_Date::DATETIME_INTERNAL_FORMAT
        ]);

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param string $fileName
     * @param string|array $content set to null to avoid starting output, $contentLength should be set explicitly in
     *                              that case
     * @param string $contentType
     * @param int $contentLength    explicit content length, if strlen($content) isn't applicable
     * @return $this
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    protected function _prepareDownloadResponse(
        $fileName,
        $content,
        $contentType = 'application/octet-stream',
        $contentLength = null
    ) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }
            if ($content['type'] == 'filename') {
                clearstatcache();
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength, true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                $ioAdapter->open(['path' => $ioAdapter->dirname($file)]);
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }
                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }

                exit(0);
            } else {
                $this->getResponse()->setBody($content);
            }
        }
        return $this;
    }
}
