<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product related items block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Catalog_Block_Product_List_Upsell extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_columnCount = 4;

    protected $_items;

    protected $_itemCollection;

    protected $_itemLimits = [];

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /** @var Mage_Catalog_Model_Product $product */
        $this->_itemCollection = $product->getUpSellProductCollection()
            ->setPositionOrder()
            ->addStoreFilter()
        ;
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter(
                $this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );

            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        if ($this->getItemLimit('upsell') > 0) {
            $this->_itemCollection->setPageSize($this->getItemLimit('upsell'));
        }

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', [
            'product'       => $product,
            'collection'    => $this->_itemCollection,
            'limit'         => $this->getItemLimit()
        ]);

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * @return Mage_Catalog_Block_Product_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    /**
     * @return Mage_Catalog_Model_Product[]
     */
    public function getItems()
    {
        if (is_null($this->_items) && $this->getItemCollection()) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

    /**
     * @return float
     */
    public function getRowCount()
    {
        return ceil(count($this->getItemCollection()->getItems())/$this->getColumnCount());
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumnCount($columns)
    {
        if (intval($columns) > 0) {
            $this->_columnCount = intval($columns);
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    /**
     * @return mixed
     */
    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }

    /**
     * Set how many items we need to show in upsell block
     * Notice: this parametr will be also applied
     *
     * @param string $type
     * @param int $limit
     * @return $this
     */
    public function setItemLimit($type, $limit)
    {
        if (intval($limit) > 0) {
            $this->_itemLimits[$type] = intval($limit);
        }
        return $this;
    }

    /**
     * @param string $type
     * @return array|int|mixed
     */
    public function getItemLimit($type = '')
    {
        if ($type == '') {
            return $this->_itemLimits;
        }
        if (isset($this->_itemLimits[$type])) {
            return $this->_itemLimits[$type];
        } else {
            return 0;
        }
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getItemsTags($this->getItems()));
    }
}
