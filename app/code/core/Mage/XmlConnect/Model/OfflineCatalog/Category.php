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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect offline catalog category model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_OfflineCatalog_Category extends Mage_XmlConnect_Block_Catalog_Category
{
    /**
     * Category url
     */
    const URL_CATEGORY_DETAILS = 'xmlconnect/catalog/category/id/%1$s/';

    /**
     * Product count in category
     */
    const PRODUCT_IN_CATEGORY = 10;

    /**
     * Request controller
     *
     * @var null|Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var array
     */
    protected $_productIds = array();

    /**
     * Initialize category model
     */
    protected function _construct()
    {
        $this->setCategoryModel(Mage::getModel('xmlconnect/offlineCatalog_category_category'));
        $this->setIndexCategoryModel(Mage::getModel('xmlconnect/offlineCatalog_category_indexCategory'));
        $this->_request = Mage::app()->getRequest();
        parent::_construct();
    }

    /**
     * Get request
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        if (null === $this->_request) {
            $this->_request = Mage::app()->getRequest();
        }
        return $this->_request;
    }

    /**
     * Export category data
     *
     * @return Mage_XmlConnect_Model_OfflineCatalog_Category
     */
    public function exportData()
    {
        $this->_getRequest()->setParam('id', null)->setParam('count', self::PRODUCT_IN_CATEGORY);
        $this->getIndexCategoryModel()->exportData();
        /** @var $helper Mage_Catalog_Helper_Category */
        $helper = Mage::helper('catalog/category');
        foreach ($helper->getStoreCategories() as $category) {
            if (!$category->getIsActive()) {
                continue;
            }
            $this->_exportCategory($category);
        }
        return $this;
    }

    /**
     * Export category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_XmlConnect_Model_OfflineCatalog_Category
     */
    protected function _exportCategory($category)
    {
        /** @var $exportHelper Mage_XmlConnect_Helper_OfflineCatalog */
        $exportHelper  = Mage::helper('xmlconnect/offlineCatalog');
        $this->_getRequest()->setParam('app_code', $exportHelper->getCurrentDeviceModel()->getCode());

        $categoryId = $category->getId();
        $this->getCategoryModel()->setCategoryId($categoryId);
        $this->_getRequest()->setParam('id', $categoryId);
        $this->getCategoryModel()->exportData();
        return $this;
    }
}
