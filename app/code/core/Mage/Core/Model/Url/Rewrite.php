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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Url rewrite model class
 *
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Url_Rewrite extends Mage_Core_Model_Abstract
{
    const TYPE_CATEGORY = 1;
    const TYPE_PRODUCT  = 2;
    const TYPE_CUSTOM   = 3;
    const REWRITE_REQUEST_PATH_ALIAS = 'rewrite_request_path';

    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
     * Load rewrite information for request
     *
     * if $path is array - that mean what we need try load for each item
     *
     * @param   mixed $path
     * @return  Mage_Core_Model_Url_Rewrite
     */
    public function loadByRequestPath($path)
    {
        $this->setId(null);

        if (is_array($path)) {
            foreach ($path as $pathInfo) {
                $this->load($pathInfo, 'request_path');
                if ($this->getId()) {
                	return $this;
                }
            }
        }
        else {
        	$this->load($path, 'request_path');
        }
        return $this;
    }

    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }

    public function loadByTags($tags)
    {
        $this->setId(null);

        $loadTags = is_array($tags) ? $tags : explode(',', $tags);

        $search = $this->getResourceCollection();
        foreach ($loadTags as $k=>$t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
            }
            $search->addTagsFilter($t);
        }
        if (!is_null($this->getStoreId())) {
            $search->addStoreFilter($this->getStoreId());
        }

        $search->setPageSize(1)->load();

        if ($search->getSize()>0) {
            foreach ($search as $rewrite) {
                $this->setData($rewrite->getData());
            }
        }

        return $this;
    }

    public function hasOption($key)
    {
        $optArr = explode(',', $this->getOptions());

        return array_search($key, $optArr) !== false;
    }

    public function addTag($tags)
    {
        $curTags = $this->getTags();

        $addTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($addTags as $k=>$t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
            }
            if (!in_array($t, $curTags)) {
                $curTags[] = $t;
            }
        }

        $this->setTags($curTags);

        return $this;
    }

    public function removeTag($tags)
    {
        $curTags = $this->getTags();

        $removeTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($removeTags as $t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
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
     * @param   Zend_Controller_Request_Http $request
     * @param   Zend_Controller_Response_Http $response
     * @return  Mage_Core_Model_Url
     */
    public function rewrite(Zend_Controller_Request_Http $request=null, Zend_Controller_Response_Http $response=null)
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
        if (is_null($this->getStoreId()) || false===$this->getStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $requestCases = array();
        $requestPath = trim($request->getPathInfo(), '/');

        /**
         * We need try to find rewrites information for both cases
         * More priority has url with query params
         */
        if ($queryString = $this->_getQueryString()) {
            $requestCases[] = $requestPath .'?'.$queryString;
            $requestCases[] = $requestPath;
        }
        else {
            $requestCases[] = $requestPath;
        }

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_GET['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_GET['___from_store']);
            }
            catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) {
                return false;
            }
            $this->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($this->getIdPath());
        }

        if (!$this->getId()) {
            return false;
        }


        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
        if ($external === 'http:/' || $external === 'https:') {
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header("Location: ".$this->getTargetPath());
            exit;
        } else {
            $targetUrl = $request->getBaseUrl(). '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }
            if ($isPermanentRedirectOption) {
                header('HTTP/1.1 301 Moved Permanently');
            }
            header('Location: '.$targetUrl);
            exit;
        }

        if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }

        if ($queryString = $this->_getQueryString()) {
        	$targetUrl .= '?'.$queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
    }

    protected function _getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryParams = array();
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            $hasChanges = false;
            foreach ($queryParams as $key=>$value) {
                if (substr($key, 0, 3) === '___') {
                    unset($queryParams[$key]);
                    $hasChanges = true;
                }
            }
            if ($hasChanges) {
                return http_build_query($queryParams);
            }
            else {
                return $_SERVER['QUERY_STRING'];
            }
        }
        return false;
    }

    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

}
