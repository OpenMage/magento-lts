<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product related items block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_List_Crosssell extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_item';

    /**
     * Crosssell item collection
     *
     * @var Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    protected $_itemCollection;

    /**
     * Prepare crosssell items data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /** @var Mage_Catalog_Model_Product $product */

        $this->_itemCollection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * Before rendering html process
     * Prepare items collection
     *
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve crosssell items collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getItems()
    {
        return $this->_itemCollection;
    }
}
