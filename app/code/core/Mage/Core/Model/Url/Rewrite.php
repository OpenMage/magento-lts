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
 * @copyright  Copyright (c) 2018-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Url rewrite model class
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Url_Rewrite _getResource()
 * @method Mage_Core_Model_Resource_Url_Rewrite getResource()
 * @method Mage_Core_Model_Resource_Url_Rewrite_Collection getResourceCollection()
 *
 * @method $this setStoreId(int $value)
 * @method int getCategoryId()
 * @method $this setCategoryId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method string getIdPath()
 * @method $this setIdPath(string $value)
 * @method string getRequestPath()
 * @method $this setRequestPath(string $value)
 * @method string getTargetPath()
 * @method $this setTargetPath(string $value)
 * @method int getIsSystem()
 * @method $this setIsSystem(int $value)
 * @method string getOptions()
 * @method $this setOptions(string $value)
 * @method string getDescription()
 * @method $this setDescription(string $value)
 * @method string|array getTags()
 * @method $this setTags(string|array $value)
 * @method bool hasCategoryId()
 */
class Mage_Core_Model_Url_Rewrite extends Mage_Core_Model_Abstract implements Mage_Core_Model_Url_Rewrite_Interface
{
    public const TYPE_CATEGORY = 1;
    public const TYPE_PRODUCT  = 2;
    public const TYPE_CUSTOM   = 3;
    public const REWRITE_REQUEST_PATH_ALIAS = 'rewrite_request_path';

    /**
     * Cache tag for clear cache in after save and after delete
     *
     * @var mixed | array | string | boolean
     */
    protected $_cacheTag = false;

    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
     * Clean cache for front-end menu
     *
     * @return  Mage_Core_Model_Url_Rewrite
     */
    protected function _afterSave()
    {
        if ($this->hasCategoryId()) {
            $this->_cacheTag = [Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG];
        }

        parent::_afterSave();

        return $this;
    }

    /**
     * Load rewrite information for request
     * If $path is array - we must load possible records and choose one matching earlier record in array
     *
     * @param   mixed $path
     * @return  Mage_Core_Model_Url_Rewrite
     */
    public function loadByRequestPath($path)
    {
        $this->setId(null);
        $this->_getResource()->loadByRequestPath($this, $path);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }

    /**
     * @param string|array $tags
     * @return $this
     */
    public function loadByTags($tags)
    {
        $this->setId(null);

        $loadTags = is_array($tags) ? $tags : explode(',', $tags);

        $search = $this->getResourceCollection();
        foreach ($loadTags as $k => $t) {
            if (!is_numeric($k)) {
                $t = $k . '=' . $t;
            }
            $search->addTagsFilter($t);
        }
        if (!is_null($this->getStoreId())) {
            $search->addStoreFilter($this->getStoreId());
        }

        $search->setPageSize(1)->load();

        if ($search->getSize() > 0) {
            /** @var Mage_Core_Model_Url_Rewrite $rewrite */
            foreach ($search as $rewrite) {
                $this->setData($rewrite->getData());
            }
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasOption($key)
    {
        $optArr = explode(',', (string) $this->getOptions());

        return in_array($key, $optArr);
    }

    /**
     * @param string|array $tags
     * @return $this
     */
    public function addTag($tags)
    {
        $curTags = $this->getTags();

        $addTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($addTags as $k => $t) {
            if (!is_numeric($k)) {
                $t = $k . '=' . $t;
            }
            if (!in_array($t, $curTags)) {
                $curTags[] = $t;
            }
        }

        $this->setTags($curTags);

        return $this;
    }

    /**
     * @param string|array $tags
     * @return $this
     */
    public function removeTag($tags)
    {
        $curTags = $this->getTags();

        $removeTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($removeTags as $k => $t) {
            if (!is_numeric($k)) {
                $t = $k . '=' . $t;
            }
            if ($key = array_search($t, $curTags)) {
                unset($curTags[$key]);
            }
        }

        $this->setTags(',', $curTags);

        return $this;
    }

    /**
     * Implement logic of custom rewrites
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function rewrite(?Zend_Controller_Request_Http $request = null, ?Zend_Controller_Response_Http $response = null)
    {
        if (!Mage::isInstalled()) {
            return false;
        }
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }
        if (is_null($response)) {
            $response = Mage::app()->getFrontController()->getResponse();
        }
        if (is_null($this->getStoreId()) || $this->getStoreId() === false) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        /**
         * We have two cases of incoming paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Each of them matches two url rewrite request paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Choose any matched rewrite, but in priority order that depends on same presence of slash and query params.
         */
        $requestCases = [];
        $pathInfo = $request->getPathInfo();
        $origSlash = (substr($pathInfo, -1) == '/') ? '/' : '';
        $requestPath = trim($pathInfo, '/');

        // If there were final slash - add nothing to less priority paths. And vice versa.
        $altSlash = $origSlash ? '' : '/';

        $queryString = $this->_getQueryString(); // Query params in request, matching "path + query" has more priority
        if ($queryString) {
            $requestCases[] = $requestPath . $origSlash . '?' . $queryString;
            $requestCases[] = $requestPath . $altSlash . '?' . $queryString;
        }
        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_GET['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_GET['___from_store'])->getId();
            } catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) {
                return false;
            }
            $currentStore = Mage::app()->getStore();
            $this->setStoreId($currentStore->getId())->loadByIdPath($this->getIdPath());

            Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $currentStore->getCode(), true);
            $targetUrl = $request->getBaseUrl() . '/' . $this->getRequestPath();

            $this->_sendRedirectHeaders($targetUrl, true);
        }

        if (!$this->getId()) {
            return false;
        }

        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
        if ($external === 'http:/' || $external === 'https:') {
            $destinationStoreCode = Mage::app()->getStore($this->getStoreId())->getCode();
            Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $destinationStoreCode, true);

            $this->_sendRedirectHeaders($this->getTargetPath(), $isPermanentRedirectOption);
        } else {
            $targetUrl = $request->getBaseUrl() . '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if (Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL) && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl() . '/' . $storeCode . '/' . $this->getTargetPath();
            }

            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        if (Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL) && $storeCode = Mage::app()->getStore()->getCode()) {
            $targetUrl = $request->getBaseUrl() . '/' . $storeCode . '/' . $this->getTargetPath();
        }

        $queryString = $this->_getQueryString();
        if ($queryString) {
            $targetUrl .= '?' . $queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
    }

    /**
     * Prepare and return QUERY_STRING
     *
     * @return bool|string
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryParams = [];
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            $hasChanges = false;
            foreach (array_keys($queryParams) as $key) {
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
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool $isPermanent
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     * @SuppressWarnings("PHPMD.ExitExpression")
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
}
