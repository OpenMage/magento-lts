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
 * Catalog data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_categoryPath;

    public function getBreadcrumbPath()
    {
        if (!$this->_categoryPath) {

            $path = array();
            if ($this->getCategory()) {
                $pathInStore = $this->getCategory()->getPathInStore();
                $pathIds = array_reverse(explode(',', $pathInStore));

                $categories = Mage::getResourceModel('catalog/category_collection')
                    ->setStore(Mage::app()->getStore())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('url_key')
                    ->addFieldToFilter('entity_id', array('in'=>$pathIds))
                    ->load()
                    ->getItems();

                // add category path breadcrumb
                foreach ($pathIds as $categoryId) {
                    if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                        $categories[$categoryId]->setStoreId(Mage::app()->getStore()->getId());
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

    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

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
}
