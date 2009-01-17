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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Url model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Model_Product_Url extends Varien_Object
{
    protected static $_url;
    protected static $_urlRewrite;

    const CACHE_TAG = 'url_rewrite';

    /**
    * @return Mage_Core_Model_Url
    */
    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }

    /**
    * @return Mage_Core_Model_Url_Rewrite
    */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return self::$_urlRewrite;
    }

    /**
     * 'no_selection' shouldn't be a valid image attribute value
     * @param string $image
     * @return string
     */
    protected function _validImage($image)
    {
        if($image == 'no_selection') {
            $image = null;
        }
        return $image;
    }

    /**
     * Get product url
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  bool $useSid
     * @return string
     */
    public function getProductUrl($product, $useSid = true)
    {
        $cacheUrlKey = 'url_'
            . ($product->getCategoryId() && !$product->getDoNotUseCategoryId() ? $product->getCategoryId() : 'NONE')
            . '_' . intval($useSid);
        $url = $product->getData($cacheUrlKey);

        if (is_null($url)) {
            if ($product->getStoreId()) {
                $this->getUrlInstance()->setStore($product->getStoreId());
            }

            // auto add SID to URL
            $originalSid = $this->getUrlInstance()->getUseSession();
            $this->getUrlInstance()->setUseSession($useSid);

            if ($product->hasData('request_path') && $product->getRequestPath() != '') {
                $this->setData($cacheUrlKey, $this->getUrlInstance()->getDirectUrl($product->getRequestPath()));
                $this->getUrlInstance()->setUseSession($originalSid);
                return $this->getData($cacheUrlKey);
            }

            Varien_Profiler::start('REWRITE: '.__METHOD__);

            $rewrite = $this->getUrlRewrite();
            if ($product->getStoreId()) {
                $rewrite->setStoreId($product->getStoreId());
            }
            else {
                $rewrite->setStoreId(Mage::app()->getStore()->getId());
            }

            $idPath = 'product/'.$product->getId();
            if ($product->getCategoryId() && !$product->getDoNotUseCategoryId() && Mage::getStoreConfig('catalog/seo/product_use_categories')) {
                $idPath .= '/'.$product->getCategoryId();
            }

            $rewrite->loadByIdPath($idPath);
//echo $this->getUrlInstance()->getBaseUrl();
            if ($rewrite->getId()) {
                $this->setData($cacheUrlKey, $this->getUrlInstance()->getDirectUrl($rewrite->getRequestPath()));
                Varien_Profiler::stop('REWRITE: '.__METHOD__);
                $this->getUrlInstance()->setUseSession($originalSid);
                return $this->getData($cacheUrlKey);
            }

            Varien_Profiler::stop('REWRITE: '.__METHOD__);
            Varien_Profiler::start('REGULAR: '.__METHOD__);

            $url = $this->getUrlInstance()->getUrl('catalog/product/view', array(
                'id'        => $product->getId(),
                's'         => $product->getUrlKey(),
                'category'  => $product->getCategoryId()
            ));

            $this->getUrlInstance()->setUseSession($originalSid);

            Varien_Profiler::stop('REGULAR: '.__METHOD__);
        }
        return $url;
    }

    public function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($str));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');

        return $urlKey;
    }

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_Catalog_Model_Category $category
     *
     * @return string
     */
    public function getUrlPath($product, $category=null)
    {
        $path = $product->getData('url_path');

        if (is_null($category)) {
            /** @todo get default category */
            return $path;
        } elseif (!$category instanceof Mage_Catalog_Model_Category) {
            Mage::throwException('Invalid category object supplied');
        }

        return Mage::helper('catalog/category')->getCategoryUrlPath($category->getUrlPath())
            . '/' . $path;
    }
}