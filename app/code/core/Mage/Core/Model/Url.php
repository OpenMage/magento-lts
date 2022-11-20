<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * URL
 *
 * Properties:
 *
 * - request
 *
 * - relative_url: true, false
 * - type: 'link', 'skin', 'js', 'media'
 * - store: instanceof Mage_Core_Model_Store
 * - secure: true, false
 *
 * - scheme: 'http', 'https'
 * - user: 'user'
 * - password: 'password'
 * - host: 'localhost'
 * - port: 80, 443
 * - base_path: '/dev/magento/'
 * - base_script: 'index.php'
 *
 * - storeview_path: 'storeview/'
 * - route_path: 'module/controller/action/param1/value1/param2/value2'
 * - route_name: 'module'
 * - controller_name: 'controller'
 * - action_name: 'action'
 * - route_params: array('param1'=>'value1', 'param2'=>'value2')
 *
 * - query: (?)'param1=value1&param2=value2'
 * - query_array: array('param1'=>'value1', 'param2'=>'value2')
 * - fragment: (#)'fragment-anchor'
 *
 * URL structure:
 *
 * https://user:password@host:443/base_path/[base_script][storeview_path]route_name/controller_name/action_name/param1/value1?query_param=query_value#fragment
 *       \__________A___________/\____________________________________B_____________________________________/
 * \__________________C___________________/              \__________________D_________________/ \_____E_____/
 * \_____________F______________/                        \___________________________G______________________/
 * \___________________________________________________H____________________________________________________/
 *
 * - A: authority
 * - B: path
 * - C: absolute_base_url
 * - D: action_path
 * - E: route_params
 * - F: host_url
 * - G: route_path
 * - H: route_url
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setType(string $value)
 * @method $this setSecure(bool $value)
 * @method $this setSecureIsForced(bool $value)
 * @method string getScheme()
 * @method string getHost()
 * @method string getPort()
 * @method string getPath()
 */
class Mage_Core_Model_Url extends Varien_Object
{
    /**
     * Default controller name
     */
    public const DEFAULT_CONTROLLER_NAME   = 'index';

    /**
     * Default action name
     */
    public const DEFAULT_ACTION_NAME       = 'index';

    /**
     * XML base url path unsecure
     */
    public const XML_PATH_UNSECURE_URL     = 'web/unsecure/base_url';

    /**
     * XML base url path secure
     */
    public const XML_PATH_SECURE_URL       = 'web/secure/base_url';

    /**
     * XML path for using in adminhtml
     */
    public const XML_PATH_SECURE_IN_ADMIN  = 'default/web/secure/use_in_adminhtml';

    /**
     * XML path for using in frontend
     */
    public const XML_PATH_SECURE_IN_FRONT  = 'web/secure/use_in_frontend';

    /**
     * Param name for form key functionality
     */
    public const FORM_KEY = 'form_key';

    /**
     * Configuration data cache
     *
     * @var array
     */
    protected static $_configDataCache;

    /**
     * Encrypted session identifier
     *
     * @var string|null
     */
    protected static $_encryptedSessionId;

    /**
     * Reserved Route parameter keys
     *
     * @var array
     */
    protected $_reservedRouteParams = [
        '_store', '_type', '_secure', '_forced_secure', '_use_rewrite', '_nosid',
        '_absolute', '_current', '_direct', '_fragment', '_escape', '_query',
        '_store_to_url'
    ];

    /**
     * Controller request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Use Session ID for generate URL
     *
     * @var bool
     */
    protected $_useSession;

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->setStore(null);
    }

    /**
     * Initialize object data from retrieved url
     *
     * @param   string $url
     * @return  Mage_Core_Model_Url
     */
    public function parseUrl($url)
    {
        $data = parse_url($url);
        $parts = [
            'scheme'   => 'setScheme',
            'host'     => 'setHost',
            'port'     => 'setPort',
            'user'     => 'setUser',
            'pass'     => 'setPassword',
            'path'     => 'setPath',
            'query'    => 'setQuery',
            'fragment' => 'setFragment'];

        foreach ($parts as $component => $method) {
            if (isset($data[$component])) {
                $this->$method($data[$component]);
            }
        }
        return $this;
    }

    /**
     * Retrieve default controller name
     *
     * @return string
     */
    public function getDefaultControllerName()
    {
        return self::DEFAULT_CONTROLLER_NAME;
    }

    /**
     * Set use_url_cache flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setUseUrlCache($flag)
    {
        $this->setData('use_url_cache', $flag);
        return $this;
    }

    /**
     * Set use session rule
     *
     * @param bool $useSession
     * @return $this
     */
    public function setUseSession($useSession)
    {
        $this->_useSession = (bool) $useSession;
        return $this;
    }

    /**
     * Set route front name
     *
     * @param string $name
     * @return $this
     */
    public function setRouteFrontName($name)
    {
        $this->setData('route_front_name', $name);
        return $this;
    }

    /**
     * Retrieve use session rule
     *
     * @return bool
     */
    public function getUseSession()
    {
        if (is_null($this->_useSession)) {
            $this->_useSession = Mage::app()->getUseSessionInUrl();
        }
        return $this->_useSession;
    }

    /**
     * Retrieve default action name
     *
     * @return string
     */
    public function getDefaultActionName()
    {
        return self::DEFAULT_ACTION_NAME;
    }

    /**
     * Retrieve configuration data
     *
     * @param string $key
     * @param string|null $prefix
     * @return string
     */
    public function getConfigData($key, $prefix = null)
    {
        if (is_null($prefix)) {
            $prefix = 'web/' . ($this->getSecure() ? 'secure' : 'unsecure') . '/';
        }
        $path = $prefix . $key;

        $cacheId = $this->getStore()->getCode() . '/' . $path;
        if (!isset(self::$_configDataCache[$cacheId])) {
            $data = $this->getStore()->getConfig($path);
            self::$_configDataCache[$cacheId] = $data;
        }

        return self::$_configDataCache[$cacheId];
    }

    /**
     * Set request
     *
     * @param Zend_Controller_Request_Http $request
     * @return $this
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Zend request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = Mage::app()->getRequest();
        }
        return $this->_request;
    }

    /**
     * Retrieve URL type
     *
     * @return string
     */
    public function getType()
    {
        if (!$this->hasData('type')) {
            $this->setData('type', Mage_Core_Model_Store::URL_TYPE_LINK);
        }
        return $this->_getData('type');
    }

    /**
     * Retrieve is secure mode URL
     *
     * @return bool
     */
    public function getSecure()
    {
        if ($this->hasData('secure_is_forced')) {
            return (bool)$this->getData('secure');
        }

        $store = $this->getStore();

        if ($store->isAdmin() && !$store->isAdminUrlSecure()) {
            return false;
        }
        if (!$store->isAdmin() && !$store->isFrontUrlSecure()) {
            return false;
        }

        if (!$this->hasData('secure')) {
            if ($this->getType() == Mage_Core_Model_Store::URL_TYPE_LINK && !$store->isAdmin()) {
                $pathSecure = Mage::getConfig()->shouldUrlBeSecure('/' . $this->getActionPath());
                $this->setData('secure', $pathSecure);
            } else {
                $this->setData('secure', true);
            }
        }
        return $this->getData('secure');
    }

    /**
     * Set store entity
     *
     * @param mixed $data
     * @return $this
     */
    public function setStore($data)
    {
        $this->setData('store', Mage::app()->getStore($data));
        return $this;
    }

    /**
     * Get current store for the url instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (!$this->hasData('store')) {
            $this->setStore(null);
        }
        return $this->_getData('store');
    }

    /**
     * Retrieve Base URL
     *
     * @param array $params
     * @return string
     */
    public function getBaseUrl($params = [])
    {
        if (isset($params['_store'])) {
            $this->setStore($params['_store']);
        }
        if (isset($params['_type'])) {
            $this->setType($params['_type']);
        }
        if (isset($params['_secure'])) {
            $this->setSecure($params['_secure']);
        }

        /**
         * Add availability support urls without store code
         */
        if ($this->getType() == Mage_Core_Model_Store::URL_TYPE_LINK
            && Mage::app()->getRequest()->isDirectAccessFrontendName($this->getRouteFrontName())) {
            $this->setType(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK);
        }

        return $this->getStore()->getBaseUrl($this->getType(), $this->getSecure());
    }

    /**
     * Set Route Parameters
     *
     * @param array|string $data
     * @return $this
     */
    public function setRoutePath($data)
    {
        if ($this->_getData('route_path') == $data) {
            return $this;
        }

        $a = explode('/', $data);

        $route = array_shift($a);
        if ($route === '*') {
            $route = $this->getRequest()->getRequestedRouteName();
        }
        $this->setRouteName($route);
        $routePath = $route . '/';

        if (!empty($a)) {
            $controller = array_shift($a);
            if ($controller === '*') {
                $controller = $this->getRequest()->getRequestedControllerName();
            }
            $this->setControllerName($controller);
            $routePath .= $controller . '/';
        }

        if (!empty($a)) {
            $action = array_shift($a);
            if ($action === '*') {
                $action = $this->getRequest()->getRequestedActionName();
            }
            $this->setActionName($action);
            $routePath .= $action . '/';
        }

        if (!empty($a)) {
            $this->unsetData('route_params');
            while (!empty($a)) {
                $key = array_shift($a);
                if (!empty($a)) {
                    $value = array_shift($a);
                    $this->setRouteParam($key, $value);
                    $routePath .= $key . '/' . $value . '/';
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve action path
     *
     * @return string
     */
    public function getActionPath()
    {
        if (!$this->getRouteName()) {
            return '';
        }

        $hasParams = (bool) $this->getRouteParams();
        $path = $this->getRouteFrontName() . '/';

        if ($this->getControllerName()) {
            $path .= $this->getControllerName() . '/';
        } elseif ($hasParams) {
            $path .= $this->getDefaultControllerName() . '/';
        }
        if ($this->getActionName()) {
            $path .= $this->getActionName() . '/';
        } elseif ($hasParams) {
            $path .= $this->getDefaultActionName() . '/';
        }

        return $path;
    }

    /**
     * Retrieve route path
     *
     * @param array $routeParams
     * @return string
     */
    public function getRoutePath($routeParams = [])
    {
        if (!$this->hasData('route_path')) {
            $routePath = $this->getRequest()->getAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS);
            if (!empty($routeParams['_use_rewrite']) && ($routePath !== null)) {
                $this->setData('route_path', $routePath);
                return $routePath;
            }
            $routePath = $this->getActionPath();
            if ($this->getRouteParams()) {
                foreach ($this->getRouteParams() as $key => $value) {
                    if (is_null($value) || $value === false || $value === '' || !is_scalar($value)) {
                        continue;
                    }
                    $routePath .= $key . '/' . $value . '/';
                }
            }
            if ($routePath != '' && substr($routePath, -1, 1) !== '/') {
                $routePath .= '/';
            }
            $this->setData('route_path', $routePath);
        }
        return $this->_getData('route_path');
    }

    /**
     * Set route name
     *
     * @param string $data
     * @return $this
     */
    public function setRouteName($data)
    {
        if ($this->_getData('route_name') == $data) {
            return $this;
        }
        $this->unsetData('route_front_name')
            ->unsetData('route_path')
            ->unsetData('controller_name')
            ->unsetData('action_name')
            ->unsetData('secure');
        return $this->setData('route_name', $data);
    }

    /**
     * Retrieve route front name
     *
     * @return string
     */
    public function getRouteFrontName()
    {
        if (!$this->hasData('route_front_name')) {
            $routeName = $this->getRouteName();
            $route = Mage::app()->getFrontController()->getRouterByRoute($routeName);
            $frontName = $route->getFrontNameByRoute($routeName);

            $this->setRouteFrontName($frontName);
        }

        return $this->_getData('route_front_name');
    }

    /**
     * Retrieve route name
     *
     * @return string|null
     */
    public function getRouteName()
    {
        return $this->_getData('route_name');
    }

    /**
     * Set Controller Name
     *
     * Reset action name and route path if has change
     *
     * @param string $data
     * @return $this
     */
    public function setControllerName($data)
    {
        if ($this->_getData('controller_name') == $data) {
            return $this;
        }
        $this->unsetData('route_path')->unsetData('action_name')->unsetData('secure');
        return $this->setData('controller_name', $data);
    }

    /**
     * Retrieve controller name
     *
     * @return string|null
     */
    public function getControllerName()
    {
        return $this->_getData('controller_name');
    }

    /**
     * Set Action name
     * Reseted route path if action name has change
     *
     * @param string $data
     * @return $this
     */
    public function setActionName($data)
    {
        if ($this->_getData('action_name') == $data) {
            return $this;
        }
        $this->unsetData('route_path');
        return $this->setData('action_name', $data)->unsetData('secure');
    }

    /**
     * Retrieve action name
     *
     * @return string|null
     */
    public function getActionName()
    {
        return $this->_getData('action_name');
    }

    /**
     * Set route params
     *
     * @param array $data
     * @param bool $unsetOldParams
     * @return $this
     */
    public function setRouteParams(array $data, $unsetOldParams = true)
    {
        if (isset($data['_type'])) {
            $this->setType($data['_type']);
            unset($data['_type']);
        }

        if (isset($data['_store'])) {
            $this->setStore($data['_store']);
            unset($data['_store']);
        }

        if (isset($data['_forced_secure'])) {
            $this->setSecure((bool)$data['_forced_secure']);
            $this->setSecureIsForced(true);
            unset($data['_forced_secure']);
        } elseif (isset($data['_secure'])) {
            $this->setSecure((bool)$data['_secure']);
            unset($data['_secure']);
        }

        if (isset($data['_absolute'])) {
            unset($data['_absolute']);
        }

        if ($unsetOldParams) {
            $this->unsetData('route_params');
        }

        $this->setUseUrlCache(true);
        if (isset($data['_current'])) {
            if (is_array($data['_current'])) {
                foreach ($data['_current'] as $key) {
                    if (array_key_exists($key, $data) || !$this->getRequest()->getUserParam($key)) {
                        continue;
                    }
                    $data[$key] = $this->getRequest()->getUserParam($key);
                }
            } elseif ($data['_current']) {
                foreach ($this->getRequest()->getUserParams() as $key => $value) {
                    if (array_key_exists($key, $data) || $this->getRouteParam($key)) {
                        continue;
                    }
                    $data[$key] = $value;
                }
                foreach ($this->getRequest()->getQuery() as $key => $value) {
                    $this->setQueryParam($key, $value);
                }
                $this->setUseUrlCache(false);
            }
            unset($data['_current']);
        }

        if (isset($data['_use_rewrite'])) {
            unset($data['_use_rewrite']);
        }

        if (isset($data['_store_to_url']) && (bool)$data['_store_to_url'] === true) {
            if (!Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getStore())
                && !Mage::app()->isSingleStoreMode()
            ) {
                $this->setQueryParam('___store', $this->getStore()->getCode());
            }
        }
        unset($data['_store_to_url']);

        foreach ($data as $k => $v) {
            $this->setRouteParam($k, $v);
        }

        return $this;
    }

    /**
     * Retrieve route params
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->_getData('route_params');
    }

    /**
     * Set route param
     *
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    public function setRouteParam($key, $data)
    {
        $params = $this->_getData('route_params');
        if (isset($params[$key]) && $params[$key] == $data) {
            return $this;
        }
        $params[$key] = $data;
        $this->unsetData('route_path');
        return $this->setData('route_params', $params);
    }

    /**
     * Retrieve route params
     *
     * @param string $key
     * @return mixed
     */
    public function getRouteParam($key)
    {
        return $this->getData('route_params', $key);
    }

    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     *
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null)
    {
        $this->unsetData('route_params');

        if (isset($routeParams['_direct'])) {
            if (is_array($routeParams)) {
                $this->setRouteParams($routeParams, false);
            }
            return $this->getBaseUrl() . $routeParams['_direct'];
        }

        if (!is_null($routePath)) {
            $this->setRoutePath($routePath);
        }
        if (is_array($routeParams)) {
            $this->setRouteParams($routeParams, false);
        }

        return $this->getBaseUrl() . $this->getRoutePath($routeParams);
    }

    /**
     * If the host was switched but session cookie won't recognize it - add session id to query
     *
     * @return $this
     */
    public function checkCookieDomains()
    {
        $hostArr = explode(':', $this->getRequest()->getServer('HTTP_HOST'));
        if ($hostArr[0] !== $this->getHost()) {
            $session = Mage::getSingleton('core/session');
            if (!$session->isValidForHost($this->getHost())) {
                if (!self::$_encryptedSessionId) {
                    $helper = Mage::helper('core');
                    if (!$helper) {
                        return $this;
                    }
                    self::$_encryptedSessionId = $session->getEncryptedSessionId();
                }
                $this->setQueryParam($session->getSessionIdQueryParam(), self::$_encryptedSessionId);
            }
        }
        return $this;
    }

    /**
     * Add session param
     *
     * @return $this
     */
    public function addSessionParam()
    {
        $session = Mage::getSingleton('core/session');

        if (!self::$_encryptedSessionId) {
            $helper = Mage::helper('core');
            if (!$helper) {
                return $this;
            }
            self::$_encryptedSessionId = $session->getEncryptedSessionId();
        }
        $this->setQueryParam($session->getSessionIdQueryParam(), self::$_encryptedSessionId);
        return $this;
    }

    /**
     * Set URL query param(s)
     *
     * @param mixed $data
     * @return $this
     */
    public function setQuery($data)
    {
        if ($this->_getData('query') == $data) {
            return $this;
        }
        $this->unsetData('query_params');
        return $this->setData('query', $data);
    }

    /**
     * Get query params part of url
     *
     * @param bool $escape "&" escape flag
     * @return string
     */
    public function getQuery($escape = false)
    {
        if (!$this->hasData('query')) {
            $query = '';
            $params = $this->getQueryParams();
            if (is_array($params)) {
                ksort($params);
                $query = http_build_query($params, '', $escape ? '&amp;' : '&');
            }
            $this->setData('query', $query);
        }
        return $this->_getData('query');
    }

    /**
     * Set query Params as array
     *
     * @param array $data
     * @return $this
     */
    public function setQueryParams(array $data)
    {
        $this->unsetData('query');

        if ($this->_getData('query_params') == $data) {
            return $this;
        }

        $params = $this->_getData('query_params');
        if (!is_array($params)) {
            $params = [];
        }
        foreach ($data as $param => $value) {
            $params[$param] = $value;
        }
        $this->setData('query_params', $params);

        return $this;
    }

    /**
     * Purge Query params array
     *
     * @return $this
     */
    public function purgeQueryParams()
    {
        $this->setData('query_params', []);
        return $this;
    }

    /**
     * Return Query Params
     *
     * @return array
     */
    public function getQueryParams()
    {
        if (!$this->hasData('query_params')) {
            $params = [];
            if ($this->_getData('query')) {
                foreach (explode('&', $this->_getData('query')) as $param) {
                    $paramArr = explode('=', $param);
                    $params[$paramArr[0]] = urldecode($paramArr[1]);
                }
            }
            $this->setData('query_params', $params);
        }
        return $this->_getData('query_params');
    }

    /**
     * Set query param
     *
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    public function setQueryParam($key, $data)
    {
        $params = $this->getQueryParams();
        if (isset($params[$key]) && $params[$key] == $data) {
            return $this;
        }
        $params[$key] = $data;
        $this->unsetData('query');
        return $this->setData('query_params', $params);
    }

    /**
     * Retrieve query param
     *
     * @param string $key
     * @return mixed
     */
    public function getQueryParam($key)
    {
        if (!$this->hasData('query_params')) {
            $this->getQueryParams();
        }
        return $this->getData('query_params', $key);
    }

    /**
     * Set fragment to URL
     *
     * @param string $data
     * @return $this
     */
    public function setFragment($data)
    {
        return $this->setData('fragment', $data);
    }

    /**
     * Retrieve URL fragment
     *
     * @return string|null
     */
    public function getFragment()
    {
        return $this->_getData('fragment');
    }

    /**
     * Build url by requested path and parameters
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return  string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $escapeQuery = false;

        /**
         * All system params should be unset before we call getRouteUrl
         * this method has condition for adding default controller and action names
         * in case when we have params
         */
        if (isset($routeParams['_fragment'])) {
            $this->setFragment($routeParams['_fragment']);
            unset($routeParams['_fragment']);
        }

        if (isset($routeParams['_escape'])) {
            $escapeQuery = $routeParams['_escape'];
            unset($routeParams['_escape']);
        }

        $query = null;
        if (isset($routeParams['_query'])) {
            $this->purgeQueryParams();
            $query = $routeParams['_query'];
            unset($routeParams['_query']);
        }

        $noSid = null;
        if (isset($routeParams['_nosid'])) {
            $noSid = (bool)$routeParams['_nosid'];
            unset($routeParams['_nosid']);
        }

        $url = $this->getRouteUrl($routePath, $routeParams);
        /**
         * Apply query params, need call after getRouteUrl for rewrite _current values
         */
        if ($query !== null) {
            if (is_string($query)) {
                $this->setQuery($query);
            } elseif (is_array($query)) {
                $this->setQueryParams($query);
            }
            if ($query === false) {
                $this->setQueryParams([]);
            }
        }

        if ($noSid !== true) {
            $this->_prepareSessionUrl($url);
        }

        $query = $this->getQuery($escapeQuery);
        if ($query) {
            $mark = (strpos($url, '?') === false) ? '?' : ($escapeQuery ? '&amp;' : '&');
            $url .= $mark . $query;
        }

        if ($this->getFragment()) {
            $url .= '#' . $this->getFragment();
        }

        return $this->escape($url);
    }

    /**
     * Return singleton model instance
     *
     * @param string $name
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($name, $arguments = [])
    {
        return Mage::getSingleton($name, $arguments);
    }

    /**
     * Check and add session id to URL
     *
     * @param string $url
     *
     * @return $this
     */
    protected function _prepareSessionUrl($url)
    {
        return $this->_prepareSessionUrlWithParams($url, []);
    }

    /**
     * Check and add session id to URL, session is obtained with parameters
     *
     * @param string $url
     * @param array $params
     *
     * @return $this
     */
    protected function _prepareSessionUrlWithParams($url, array $params)
    {
        if (!$this->getUseSession()) {
            return $this;
        }

        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session', $params);

        $sessionId = $session->getSessionIdForHost($url);
        if (Mage::app()->getUseSessionVar() && !$sessionId) {
            $this->setQueryParam('___SID', $this->getSecure() ? 'S' : 'U'); // Secure/Unsecure
        } elseif ($sessionId) {
            $this->setQueryParam($session->getSessionIdQueryParam(), $sessionId);
        }
        return $this;
    }

    /**
     * Rebuild URL to handle the case when session ID was changed
     *
     * @param string $url
     * @return string
     */
    public function getRebuiltUrl($url)
    {
        $this->parseUrl($url);
        $port = $this->getPort();
        if ($port) {
            $port = ':' . $port;
        } else {
            $port = '';
        }
        $url = $this->getScheme() . '://' . $this->getHost() . $port . $this->getPath();

        $this->_prepareSessionUrl($url);

        $query = $this->getQuery();
        if ($query) {
            $url .= '?' . $query;
        }

        $fragment = $this->getFragment();
        if ($fragment) {
            $url .= '#' . $fragment;
        }

        return $this->escape($url);
    }

    /**
     * Escape (enclosure) URL string
     *
     * @param string $value
     * @return string
     */
    public function escape($value)
    {
        $value = str_replace('"', '%22', $value);
        $value = str_replace("'", '%27', $value);
        $value = str_replace('>', '%3E', $value);
        $value = str_replace('<', '%3C', $value);
        return $value;
    }

    /**
     * Build url by direct url and parameters
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getDirectUrl($url, $params = [])
    {
        $params['_direct'] = $url;
        return $this->getUrl('', $params);
    }

    /**
     * Replace Session ID value in URL
     *
     * @param string $html
     * @return string
     */
    public function sessionUrlVar($html)
    {
        if (strpos($html, '__SID') === false) {
            return $html;
        } else {
            return preg_replace_callback(
                '#(\?|&amp;|&)___SID=([SU])(&amp;|&)?#',
                [$this, "sessionVarCallback"],
                $html
            );
        }
    }

    /**
     * Check and return use SID for URL
     *
     * @param bool $secure
     * @return bool
     */
    public function useSessionIdForUrl($secure = false)
    {
        $key = 'use_session_id_for_url_' . (int) $secure;
        if (is_null($this->getData($key))) {
            $httpHost = Mage::app()->getFrontController()->getRequest()->getHttpHost();
            $urlHost = parse_url(
                Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $secure),
                PHP_URL_HOST
            );

            if ($httpHost != $urlHost) {
                $this->setData($key, true);
            } else {
                $this->setData($key, false);
            }
        }
        return $this->getData($key);
    }

    /**
     * Callback function for session replace
     *
     * @param array $match
     * @return string
     */
    public function sessionVarCallback($match)
    {
        if ($this->useSessionIdForUrl($match[2] == 'S')) {
            $session = Mage::getSingleton('core/session');
            /** @var Mage_Core_Model_Session $session */
            return $match[1]
                . $session->getSessionIdQueryParam()
                . '=' . $session->getEncryptedSessionId()
                . ($match[3] ?? '');
        } else {
            if ($match[1] == '?' && isset($match[3])) {
                return '?';
            } elseif ($match[1] == '?' && !isset($match[3])) {
                return '';
            } elseif (($match[1] == '&amp;' || $match[1] == '&') && !isset($match[3])) {
                return '';
            } elseif (($match[1] == '&amp;' || $match[1] == '&') && isset($match[3])) {
                return $match[3];
            }
        }
        return '';
    }

    /**
     * Check if users originated URL is one of the domain URLs assigned to stores
     *
     * @return bool
     */
    public function isOwnOriginUrl()
    {
        $storeDomains = [];
        $referer = parse_url(Mage::app()->getFrontController()->getRequest()->getServer('HTTP_REFERER'), PHP_URL_HOST);
        foreach (Mage::app()->getStores() as $store) {
            $storeDomains[] = parse_url($store->getBaseUrl(), PHP_URL_HOST);
            $storeDomains[] = parse_url($store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true), PHP_URL_HOST);
        }
        $storeDomains = array_unique($storeDomains);
        if (empty($referer) || in_array($referer, $storeDomains)) {
            return true;
        }
        return false;
    }

    /**
     * Return frontend redirect URL with SID and other session parameters if any
     *
     * @param string $url
     *
     * @return string
     */
    public function getRedirectUrl($url)
    {
        $this->_prepareSessionUrlWithParams($url, [
            'name' => Mage_Core_Controller_Front_Action::SESSION_NAMESPACE
        ]);

        $query = $this->getQuery(false);
        if ($query) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . $query;
        }

        return $url;
    }
}
