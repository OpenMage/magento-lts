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
 * Custom Zend_Controller_Request_Http class (formally)
 *
 * Allows dispatching before and after events for each controller action
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Request_Http extends Zend_Controller_Request_Http
{
    /**
     * ORIGINAL_PATH_INFO
     * @var string
     */
    protected $_originalPathInfo = '';
    protected $_storeCode = null;
    protected $_requestString = '';

    protected $_route;

    /**
     * Returns ORIGINAL_PATH_INFO.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getOriginalPathInfo()
    {
        if (empty($this->_originalPathInfo)) {
            $this->setPathInfo();
        }
        return $this->_originalPathInfo;
    }

    public function getStoreCodeFromPath()
    {
        if (!$this->_storeCode) {
            // get store view code
            if (Mage::app()->isInstalled() && Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL)) {
                $p = explode('/', trim($this->getPathInfo(), '/'));
                $storeCode = $p[0];

                $stores = Mage::app()->getStores(true, true);

                if ($storeCode !== '' && isset($stores[$storeCode])) {
                    array_shift($p);
                    $this->setPathInfo(implode('/', $p));
                    $this->_storeCode = $storeCode;
                    Mage::app()->setCurrentStore($storeCode);
                }
                else {
                    $this->_storeCode = Mage::app()->getStore()->getCode();
                }
            } else {
                $this->_storeCode = Mage::app()->getStore()->getCode();
            }

        }
        return $this->_storeCode;
    }

    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            if ((null !== $baseUrl)
                && (false === ($pathInfo = substr($requestUri, strlen($baseUrl)))))
            {
                // If substr() returns false then PATH_INFO is set to an empty string
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }

            if (Mage::app()->isInstalled() && Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL)) {
                $p = explode('/', ltrim($pathInfo, '/'), 2);
                $storeCode = $p[0];
                $stores = Mage::app()->getStores(true, true);
                if ($storeCode!=='' && isset($stores[$storeCode])) {
                    Mage::app()->setCurrentStore($storeCode);
                    $pathInfo = '/'.(isset($p[1]) ? $p[1] : '');
                }
                elseif ($storeCode !== '') {
                    $this->setActionName('noRoute');
                }
            }

            $this->_originalPathInfo = (string) $pathInfo;

            $this->_requestString = $pathInfo . ($pos!==false ? substr($requestUri, $pos) : '');
        }

        $this->_pathInfo = (string) $pathInfo;
        return $this;
    }

    public function getOriginalRequest()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setPathInfo($this->getOriginalPathInfo());
        return $request;
    }

    public function getRequestString()
    {
        return $this->_requestString;
    }

    public function getBasePath()
    {
        $path = parent::getBasePath();
        if (empty($path)) {
            $path = '/';
        } else {
            $path = str_replace('\\', '/', $path);
        }
        return $path;
    }

    public function getBaseUrl()
    {
        $url = parent::getBaseUrl();
        $url = str_replace('\\', '/', $url);
        return $url;
    }

    public function setRouteName($route)
    {
        $this->_route = $route;
        $router = Mage::app()->getFrontController()->getRouterByRoute($route);
        if (!$router) return $this;
        $module = $router->getFrontNameByRoute($route);
        if ($module) {
            $this->setModuleName($module);
        }
        return $this;
    }

    public function getRouteName()
    {
        return $this->_route;
    }
}