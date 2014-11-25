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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Url rewrite request model
 *
 * @category Mage
 * @package Mage_Core
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Url_Rewrite_Request
{
    /**
     * Instance of request
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Instance of core config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Collection of front controller's routers
     *
     * @var array
     */
    protected $_routers = array();

    /**
     * Instance of url rewrite model
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected $_rewrite;

    /**
     * Application
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Mage Factory model
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Constructor
     * Arguments:
     *   request  - Zend_Controller_Request_Http
     *   config   - Mage_Core_Model_Config
     *   factory  - Mage_Core_Model_Factory
     *   routers  - array
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getModel('core/factory');
        $this->_app     = !empty($args['app']) ? $args['app'] : Mage::app();
        $this->_config  = !empty($args['config']) ? $args['config'] : Mage::getConfig();
        $this->_request = !empty($args['request'])
            ? $args['request'] : Mage::app()->getFrontController()->getRequest();
        $this->_rewrite = !empty($args['rewrite'])
            ? $args['rewrite'] : $this->_factory->getModel('core/url_rewrite');

        if (!empty($args['routers'])) {
            $this->_routers = $args['routers'];
        }
    }

    /**
     * Implement logic of custom rewrites
     *
     * @return bool
     */
    public function rewrite()
    {
        if (!Mage::isInstalled()) {
            return false;
        }

        if (!$this->_request->isStraight()) {
            Varien_Profiler::start('mage::dispatch::db_url_rewrite');
            $this->_rewriteDb();
            Varien_Profiler::stop('mage::dispatch::db_url_rewrite');
        }

        Varien_Profiler::start('mage::dispatch::config_url_rewrite');
        $this->_rewriteConfig();
        Varien_Profiler::stop('mage::dispatch::config_url_rewrite');

        return true;
    }

    /**
     * Implement logic of custom rewrites
     *
     * @return bool
     */
    protected function _rewriteDb()
    {
        if (null === $this->_rewrite->getStoreId() || false === $this->_rewrite->getStoreId()) {
            $this->_rewrite->setStoreId($this->_app->getStore()->getId());
        }

        $requestCases = $this->_getRequestCases();
        $this->_rewrite->loadByRequestPath($requestCases);

        $fromStore = $this->_request->getQuery('___from_store');
        if (!$this->_rewrite->getId() && $fromStore) {
            $stores = $this->_app->getStores(false, true);
            if (!empty($stores[$fromStore])) {
                /** @var $store Mage_Core_Model_Store */
                $store = $stores[$fromStore];
                $fromStoreId = $store->getId();
            } else {
                return false;
            }

            $this->_rewrite->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->_rewrite->getId()) {
                return false;
            }

            // Load rewrite by id_path
            $currentStore = $this->_app->getStore();
            $this->_rewrite->setStoreId($currentStore->getId())->loadByIdPath($this->_rewrite->getIdPath());

            $this->_setStoreCodeCookie($currentStore->getCode());

            $targetUrl = $currentStore->getBaseUrl() . $this->_rewrite->getRequestPath();
            $this->_sendRedirectHeaders($targetUrl, true);
        }

        if (!$this->_rewrite->getId()) {
            return false;
        }

        $this->_request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $this->_rewrite->getRequestPath());
        $this->_processRedirectOptions();

        return true;
    }

    /**
     * Set store code to a cookie
     *
     * @param string $storeCode
     */
    protected function _setStoreCodeCookie($storeCode)
    {
        $this->_app->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $storeCode, true);
    }

    /**
     * Process redirect (R) and permanent redirect (RP)
     *
     * @return Mage_Core_Model_Url_Rewrite_Request
     */
    protected function _processRedirectOptions()
    {
        $isPermanentRedirectOption = $this->_rewrite->hasOption('RP');

        $external = substr($this->_rewrite->getTargetPath(), 0, 6);
        if ($external === 'http:/' || $external === 'https:') {
            $destinationStoreCode = $this->_app->getStore($this->_rewrite->getStoreId())->getCode();
            $this->_setStoreCodeCookie($destinationStoreCode);
            $this->_sendRedirectHeaders($this->_rewrite->getTargetPath(), $isPermanentRedirectOption);
        }

        $targetUrl = $this->_request->getBaseUrl() . '/' . $this->_rewrite->getTargetPath();

        $storeCode = $this->_app->getStore()->getCode();
        if (Mage::getStoreConfig('web/url/use_store') && !empty($storeCode)) {
            $targetUrl = $this->_request->getBaseUrl() . '/' . $storeCode . '/' . $this->_rewrite->getTargetPath();
        }

        if ($this->_rewrite->hasOption('R') || $isPermanentRedirectOption) {
            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        $queryString = $this->_getQueryString();
        if ($queryString) {
            $targetUrl .= '?' . $queryString;
        }

        $this->_request->setRequestUri($targetUrl);
        $this->_request->setPathInfo($this->_rewrite->getTargetPath());

        return $this;
    }

    /**
     * Apply configuration rewrites to current url
     *
     * @return bool
     */
    protected function _rewriteConfig()
    {
        $config = $this->_config->getNode('global/rewrite');
        if (!$config) {
            return false;
        }
        foreach ($config->children() as $rewrite) {
            $from = (string)$rewrite->from;
            $to = (string)$rewrite->to;
            if (empty($from) || empty($to)) {
                continue;
            }
            $from = $this->_processRewriteUrl($from);
            $to   = $this->_processRewriteUrl($to);

            $pathInfo = preg_replace($from, $to, $this->_request->getPathInfo());
            if (isset($rewrite->complete)) {
                $this->_request->setPathInfo($pathInfo);
            } else {
                $this->_request->rewritePathInfo($pathInfo);
            }
        }
        return true;
    }

    /**
     * Prepare request cases.
     *
     * We have two cases of incoming paths - with and without slashes at the end ("/somepath/" and "/somepath").
     * Each of them matches two url rewrite request paths
     * - with and without slashes at the end ("/somepath/" and "/somepath").
     * Choose any matched rewrite, but in priority order that depends on same presence of slash and query params.
     *
     * @return array
     */
    protected function _getRequestCases()
    {
        $pathInfo = $this->_request->getPathInfo();
        $requestPath = trim($pathInfo, '/');
        $origSlash = (substr($pathInfo, -1) == '/') ? '/' : '';
        // If there were final slash - add nothing to less priority paths. And vice versa.
        $altSlash = $origSlash ? '' : '/';

        $requestCases = array();
        // Query params in request, matching "path + query" has more priority
        $queryString = $this->_getQueryString();
        if ($queryString) {
            $requestCases[] = $requestPath . $origSlash . '?' . $queryString;
            $requestCases[] = $requestPath . $altSlash . '?' . $queryString;
        }
        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;
        return $requestCases;
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool $isPermanent
     */
    protected function _sendRedirectHeaders($url, $isPermanent = false)
    {
        if ($isPermanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Location: ' . $url);
        exit;
    }

    /**
     * Prepare and return QUERY_STRING
     *
     * @return bool|string
     */
    protected function _getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryParams = array();
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            $hasChanges = false;
            foreach ($queryParams as $key => $value) {
                if (substr($key, 0, 3) === '___') {
                    unset($queryParams[$key]);
                    $hasChanges = true;
                }
            }
            if ($hasChanges) {
                return http_build_query($queryParams);
            } else {
                return $_SERVER['QUERY_STRING'];
            }
        }
        return false;
    }

    /**
     * Replace route name placeholders in url to front name
     *
     * @param string $url
     * @return string
     */
    protected function _processRewriteUrl($url)
    {
        $startPos = strpos($url, '{');
        if ($startPos !== false) {
            $endPos = strpos($url, '}');
            $routeName = substr($url, $startPos + 1, $endPos - $startPos - 1);
            $router = $this->_getRouterByRoute($routeName);
            if ($router) {
                $frontName = $router->getFrontNameByRoute($routeName);
                $url = str_replace('{' . $routeName . '}', $frontName, $url);
            }
        }
        return $url;
    }

    /**
     * Retrieve router by name
     *
     * @param string $name
     * @return Mage_Core_Controller_Varien_Router_Abstract|bool
     */
    protected function _getRouter($name)
    {
        if (isset($this->_routers[$name])) {
            return $this->_routers[$name];
        }
        return false;
    }

    /**
     * Retrieve router by name
     *
     * @param string $routeName
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    protected function _getRouterByRoute($routeName)
    {
        // empty route supplied - return base url
        if (empty($routeName)) {
            $router = $this->_getRouter('standard');
        } elseif ($this->_getRouter('admin')->getFrontNameByRoute($routeName)) {
            // try standard router url assembly
            $router = $this->_getRouter('admin');
        } elseif ($this->_getRouter('standard')->getFrontNameByRoute($routeName)) {
            // try standard router url assembly
            $router = $this->_getRouter('standard');
        } else {
            // try custom router url assembly
            $router = $this->_getRouter($routeName);
            if (!$router) {
                // get default router url
                $router = $this->_getRouter('default');
            }
        }
        return $router;
    }
}
