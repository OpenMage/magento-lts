<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New products block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_New extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default value for products count that will be shown
     */
    public const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(['cache_lifetime' => 86400]);
        $this->addCacheTag(Mage_Catalog_Model_Product::CACHE_TAG);
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            'CATALOG_PRODUCT_NEW',
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            $this->getProductsCount(),
            Mage::app()->getStore()->getCurrentCurrencyCode(),
        ];
    }

    /**
     * Prepare and return product collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection|Object|Varien_Data_Collection
     */
    protected function _getProductCollection()
    {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        return $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', ['or' => [
                0 => ['date' => true, 'to' => $todayEndOfDayDate],
                1 => ['is' => new Zend_Db_Expr('null')]],
            ], 'left')
            ->addAttributeToFilter('news_to_date', ['or' => [
                0 => ['date' => true, 'from' => $todayStartOfDayDate],
                1 => ['is' => new Zend_Db_Expr('null')]],
            ], 'left')
            ->addAttributeToFilter(
                [
                    ['attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')],
                    ['attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null')],
                ],
            )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);
    }

    /**
     * Prepare collection with new products
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param int $count
     * @return $this
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if ($this->_productsCount === null) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
}
