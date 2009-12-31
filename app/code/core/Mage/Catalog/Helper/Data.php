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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog data helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    const PRICE_SCOPE_GLOBAL            = 0;
    const PRICE_SCOPE_WEBSITE           = 1;
    const XML_PATH_PRICE_SCOPE          = 'catalog/price/scope';

    /**
     * Breadcrumb Path cache
     *
     * @var string
     */
    protected $_categoryPath;

    /**
     * Return current category path or get it from current category
     * and creating array of categories|product paths for breadcrumbs
     *
     * @return string
     */
    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {

            $path = array();
            if ($category = $this->getCategory()) {
                $pathInStore = $category->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = $category->getParentCategories();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $path['category'.$categoryId] = array(
                            'label' => $categories[$categoryId]->getName(),
                            'link' => $this->_isCategoryLink($categoryId) ? $categories[$categoryId]->getUrl() : ''
                        );
                    }
                }
            }

            if ($this->getProduct()) {
                $path['product'] = array('label'=>$this->getProduct()->getName());
            }

            $this->_categoryPath = $path;
        }
        return $this->_categoryPath;
    }

    /**
     * Check is category link
     *
     * @param int $categoryId
     * @return bool
     */
    protected function _isCategoryLink($categoryId)
    {
        if ($this->getProduct()) {
            return true;
        }
        if ($categoryId != $this->getCategory()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Return current category object
     *
     * @return Mage_Catalog_Model_Category|null
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Retrieve current Product object
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve Visitor/Customer Last Viewed URL
     *
     * @return string
     */
    public function getLastViewedUrl()
    {
        if ($productId = Mage::getSingleton('catalog/session')->getLastViewedProductId()) {
            $product = Mage::getModel('catalog/product')->load($productId);
            /* @var $product Mage_Catalog_Model_Product */
            if (Mage::helper('catalog/product')->canShow($product, 'catalog')) {
                return $product->getProductUrl();
            }
        }
        if ($categoryId = Mage::getSingleton('catalog/session')->getLastViewedCategoryId()) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            /* @var $category Mage_Catalog_Model_Category */
            if (!Mage::helper('catalog/category')->canShow($category)) {
                return '';
            }
            return $category->getCategoryUrl();
        }
        return '';
    }

    /**
     * Split SKU of an item by dashes and spaces
     * Words will not be broken, unless thir length is greater than $length
     *
     * @param string $sku
     * @param int $length
     * @return array
     */
    public function splitSku($sku, $length = 30)
    {
        return Mage::helper('core/string')->str_split($sku, $length, true, false, '[\-\s]');
    }

    /**
     * Retrieve attribute hidden fields
     *
     * @return array
     */
    public function getAttributeHiddenFields()
    {
        if (Mage::registry('attribute_type_hidden_fields')) {
            return Mage::registry('attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (Mage::registry('attribute_type_disabled_types')) {
            return Mage::registry('attribute_type_disabled_types');
        } else {
            return array();
        }
    }

    /**
     * Retrieve Catalog Price Scope
     *
     * @return int
     */
    public function getPriceScope()
    {
        return Mage::getStoreConfig(self::XML_PATH_PRICE_SCOPE);
    }

    /**
     * Is Global Price
     *
     * @return bool
     */
    public function isPriceGlobal()
    {
        return $this->getPriceScope() == self::PRICE_SCOPE_GLOBAL;
    }
}
