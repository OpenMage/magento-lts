<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Url model
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Url extends Mage_Catalog_Model_Url
{
    /**
     * Initialize Url model
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
    }

    /**
     * Retrieve Url for specified category
     *
     * @return string
     */
    public function getCategoryUrl(Mage_Catalog_Model_Category $category)
    {
        $url = $category->getData('url');
        if ($url !== null) {
            return $url;
        }

        Varien_Profiler::start('REWRITE: ' . __METHOD__);

        if ($category->hasData('request_path') && $category->getData('request_path') != '') {
            $category->setData('url', $this->_getDirectUrl($category));
            Varien_Profiler::stop('REWRITE: ' . __METHOD__);
            return $category->getData('url');
        }

        $requestPath = $this->_getRequestPath($category);
        if ($requestPath) {
            $category->setRequestPath($requestPath);
            $category->setData('url', $this->_getDirectUrl($category));
            Varien_Profiler::stop('REWRITE: ' . __METHOD__);
            return $category->getData('url');
        }

        Varien_Profiler::stop('REWRITE: ' . __METHOD__);

        $category->setData('url', $category->getCategoryIdUrl());
        return $category->getData('url');
    }

    /**
     * Returns category URL by which it can be accessed
     * @return string
     */
    protected function _getDirectUrl(Mage_Catalog_Model_Category $category)
    {
        return $this->getUrlInstance()->getDirectUrl($category->getRequestPath());
    }

    /**
     * Retrieve request path
     *
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
}
