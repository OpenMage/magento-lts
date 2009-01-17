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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Mage_Reports_Block_Product_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    protected $_eventTypeId = 0;

    /**
     * Retrieve page size (count)
     *
     * @return int
     */
    protected function getPageSize()
    {
        if ($this->hasData('page_size')) {
            return $this->getData('page_size');
        }
        return 5;
    }

    /**
     * Obtain product ids, that must not be included in collection
     * @return array
     */
    protected function _getProductsToSkip()
    {
        return array();
    }

    /**
     * Get products collection and apply recent events log to it
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getRecentProductsCollection()
    {
        // get products collection and apply status and visibility filter
        $collection = $this->_addProductAttributesAndPrices(Mage::getModel('catalog/product')->getCollection())
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addUrlRewrite()
            ->setPageSize($this->getPageSize())
            ->setCurPage(1)
        ;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        // apply events log to collection with required parameters
        $skip = $this->_getProductsToSkip();
        $subtype = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $subjectId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        } else {
            $subjectId = Mage::getSingleton('log/visitor')->getId();
            $subtype = 1;
        }
        Mage::getResourceSingleton('reports/event')->applyLogToCollection($collection, $this->_eventTypeId, $subjectId, $subtype, $skip);

        foreach ($collection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
        return $collection;
    }
}
