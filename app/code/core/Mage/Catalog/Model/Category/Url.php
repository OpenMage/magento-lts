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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category url
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Url
{
    /**
     * Url instance
     *
     * @var Mage_Core_Model_Url
     */
    protected $_url;

    /**
     * Factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Url rewrite instance
     *
     * @var Mage_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Initialize Url model
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
    }

    /**
     * Retrieve Url for specified category
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl(Mage_Catalog_Model_Category $category)
    {
        $url = $category->getData('url');
        if (null !== $url) {
            return $url;
        }

        Varien_Profiler::start('REWRITE: '.__METHOD__);

        if ($category->hasData('request_path') && $category->getData('request_path') != '') {
            $category->setData('url', $this->_getDirectUrl($category));
            Varien_Profiler::stop('REWRITE: '.__METHOD__);
            return $category->getData('url');
        }

        $requestPath = $this->_getRequestPath($category);
        if ($requestPath) {
            $category->setRequestPath($requestPath);
            $category->setData('url', $this->_getDirectUrl($category));
            Varien_Profiler::stop('REWRITE: '.__METHOD__);
            return $category->getData('url');
        }

        Varien_Profiler::stop('REWRITE: '.__METHOD__);

        $category->setData('url', $category->getCategoryIdUrl());
        return $category->getData('url');
    }

    /**
     * Returns category URL by which it can be accessed
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    protected function _getDirectUrl(Mage_Catalog_Model_Category $category)
    {
        return $this->getUrlInstance()->getDirectUrl($category->getRequestPath());
    }

    /**
     * Retrieve request path
     *
     * @param Mage_Catalog_Model_Category $category
     * @return bool|string
     */
    protected function _getRequestPath(Mage_Catalog_Model_Category $category)
    {
        $rewrite = $this->getUrlRewrite();
        $storeId = $category->getStoreId();
        if ($storeId) {
            $rewrite->setStoreId($storeId);
        }
        $idPath = 'category/' . $category->getId();
        $rewrite->loadByIdPath($idPath);
        if ($rewrite->getId()) {
            return $rewrite->getRequestPath();
        }
        return false;
    }

    /**
     * Retrieve Url instance
     *
     * @return Mage_Core_Model_Url
     */
    public function getUrlInstance()
    {
        if (null === $this->_url) {
            $this->_url = $this->_factory->getModel('core/url');
        }
        return $this->_url;
    }

    /**
     * Retrieve Url rewrite instance
     *
     * @return Mage_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (null === $this->_urlRewrite) {
            $this->_urlRewrite = $this->_factory->getUrlRewriteInstance();
        }
        return $this->_urlRewrite;
    }
}
