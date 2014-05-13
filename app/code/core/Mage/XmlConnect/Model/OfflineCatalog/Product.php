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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect offline catalog product model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_OfflineCatalog_Product extends Mage_XmlConnect_Block_Catalog_Product_List
{
    /**
     * Type product
     */
    const TYPE_PRODUCT = 'product';

    /**
     * Type review
     */
    const TYPE_REVIEW = 'review';

    /**
     * Type gallery
     */
    const TYPE_GALLERY = 'gallery';

    /**
     * Export product data
     *
     * @return Mage_XmlConnect_Model_OfflineCatalog_Product
     */
    public function exportData()
    {
        /** @var $helper Mage_Catalog_Helper_Category */
        $helper = Mage::helper('catalog/category');
        Mage::app()->getRequest()->setParam(
            'count', Mage_XmlConnect_Model_OfflineCatalog_Category::PRODUCT_IN_CATEGORY
        );
        foreach ($helper->getStoreCategories() as $category) {
            if (!$category->getIsActive()) {
                continue;
            }
            $this->_exportProductCollection($category);
        }
    }

    /**
     * Export product collection
     *
     * @param Varien_Data_Tree_Node $category
     */
    protected function _exportProductCollection($category)
    {
        $categoryModel = Mage::getModel('catalog/category')->load($category->getId());
        $this->setCategory($categoryModel)->setLayer(Mage::getSingleton('catalog/layer'));
        foreach ($this->_getProductCollection() as $product) {
            $product->load($product->getEntityId());
            Mage::app()->getRequest()->setParam('id', $product->getId());
            $this->_getExportModel(self::TYPE_PRODUCT)->setProduct($product)->exportData();
            $this->_getExportModel(self::TYPE_GALLERY)->setProduct($product)->exportData();
            $this->_getExportModel(self::TYPE_REVIEW)->setProduct($product)->exportData();
        }
    }

    /**
     * Get export model by type
     *
     * @param string $type
     * @return Mage_Core_Model_Abstract|null
     */
    protected function _getExportModel($type)
    {
        switch ($type) {
            case self::TYPE_PRODUCT:
                return Mage::getSingleton('xmlconnect/offlineCatalog_product_product');
                break;
            case self::TYPE_GALLERY:
                return Mage::getSingleton('xmlconnect/offlineCatalog_product_gallery');
                break;
            case self::TYPE_REVIEW:
                return Mage::getSingleton('xmlconnect/offlineCatalog_product_review');
                break;
            default:
                return null;
        }
    }
}
