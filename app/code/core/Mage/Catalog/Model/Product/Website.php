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
 * Catalog product website model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Website extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_website');
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Product_Website
     */
    public function removeProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->removeProducts($websiteIds, $productIds);
            $this->_refreshRewrites($productIds);
            Mage::getResourceModel('catalog/category')->refreshProductIndex(
                array(), $productIds
            );
            Mage::dispatchEvent('catalog_product_website_update', array(
                'website_ids'   => $websiteIds,
                'product_ids'   => $productIds,
                'action'        => 'remove'
            ));
        }
        catch (Exception $e) {
            Mage::throwException(
                Mage::helper('catalog')->__('There was an error while removing products from websites')
            );
        }
        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Product_Website
     */
    public function addProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->addProducts($websiteIds, $productIds);
            $this->_refreshRewrites($productIds);
            Mage::getResourceModel('catalog/category')->refreshProductIndex(
                array(), $productIds
            );
            Mage::dispatchEvent('catalog_product_website_update', array(
                'website_ids'   => $websiteIds,
                'product_ids'   => $productIds,
                'action'        => 'add'
            ));
        }
        catch (Exception $e) {
            Mage::throwException(
                Mage::helper('catalog')->__('There was an error while adding products to websites')
            );
        }
        return $this;
    }

    /**
     * Refresh products url rewrites
     *
     * @param array $productIds
     */
    protected function _refreshRewrites($productIds)
    {
//        $urlModel = Mage::getModel('catalog/url');
        /* @var $urlModel Mage_Catalog_Model_Url */
        foreach ($productIds as $productId) {
//            $urlModel->refreshProductRewrite($productId);
        }
    }
} // Class Mage_Catalog_Model_Product_Website End