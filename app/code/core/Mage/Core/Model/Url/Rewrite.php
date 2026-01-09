<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Url rewrite model class
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Url_Rewrite            _getResource()
 * @method int                                             getCategoryId()
 * @method Mage_Core_Model_Resource_Url_Rewrite_Collection getCollection()
 * @method string                                          getDescription()
 * @method string                                          getIdPath()
 * @method int                                             getIsSystem()
 * @method string                                          getOptions()
 * @method int                                             getProductId()
 * @method string                                          getRequestPath()
 * @method Mage_Core_Model_Resource_Url_Rewrite            getResource()
 * @method Mage_Core_Model_Resource_Url_Rewrite_Collection getResourceCollection()
 * @method array|string                                    getTags()
 * @method string                                          getTargetPath()
 * @method bool                                            hasCategoryId()
 * @method $this                                           setCategoryId(int $value)
 * @method $this                                           setDescription(string $value)
 * @method $this                                           setIdPath(string $value)
 * @method $this                                           setIsSystem(int $value)
 * @method $this                                           setOptions(string $value)
 * @method $this                                           setProductId(int $value)
 * @method $this                                           setRequestPath(string $value)
 * @method $this                                           setStoreId(int $value)
 * @method $this                                           setTags(array|string $value)
 * @method $this                                           setTargetPath(string $value)
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
     * @var array|bool|string
     */
    protected $_cacheTag = false;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
     * Clean cache for front-end menu
     *
     * @return Mage_Core_Model_Url_Rewrite
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
     * @param  mixed                       $path
     * @return Mage_Core_Model_Url_Rewrite
     * @throws Mage_Core_Exception
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
     * @param  string              $path
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }

    /**
     * @param  array|string        $tags
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByTags($tags)
    {
        $this->setId(null);

        $loadTags = is_array($tags) ? $tags : explode(',', $tags);

        $search = $this->getResourceCollection();
        foreach ($loadTags as $key => $value) {
            if (!is_numeric($key)) {
                $value = $key . '=' . $value;
            }

            $search->addTagsFilter($value);
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
     * @param  string $key
     * @return bool
     */
    public function hasOption($key)
    {
        $optArr = explode(',', (string) $this->getOptions());

        return in_array($key, $optArr);
    }

    /**
     * @param  array|string $tags
     * @return $this
     */
    public function addTag($tags)
    {
        $curTags = $this->getTags();

        $addTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($addTags as $key => $value) {
            if (!is_numeric($key)) {
                $value = $key . '=' . $value;
            }

            if (!in_array($value, $curTags)) {
                $curTags[] = $value;
            }
        }

        $this->setTags($curTags);

        return $this;
    }

    /**
     * @param  array|string $tags
     * @return $this
     */
    public function removeTag($tags)
    {
        $curTags = $this->getTags();

        $removeTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($removeTags as $tagKey => $value) {
            if (!is_numeric($tagKey)) {
                $value = $tagKey . '=' . $value;
            }

            if ($key = array_search($value, $curTags)) {
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
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Controller_Response_Exception
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
        $origSlash = (str_ends_with($pathInfo, '/')) ? '/' : '';
        $requestPath = trim($pathInfo, '/');
        $targetUrl = '';

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
            } catch (Exception) {
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
                if (str_starts_with($key, '___')) {
                    unset($queryParams[$key]);
                    $hasChanges = true;
                }
            }

            if ($hasChanges) {
                return http_build_query($queryParams);
            }

            return $_SERVER['QUERY_STRING'];
        }

        return false;
    }

    /**
     * @return null|int
     */
    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool   $isPermanent
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
