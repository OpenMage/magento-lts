<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Comapare Products Sidebar Block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_Compare_Sidebar extends Mage_Catalog_Block_Product_Compare_Abstract
{
    /**
     * Compare Products Collection
     *
     * @var null|Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
     */
    protected $_itemsCollection = null;

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        $this->setId('compare');
    }

    /**
     * Retrieve Compare Products Collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection
     */
    public function getItems()
    {
        if ($this->_itemsCollection) {
            return $this->_itemsCollection;
        }

        return $this->_getHelper()->getItemCollection();
    }

    /**
     * Set Compare Products Collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item_Collection $collection
     * @return $this
     */
    public function setItems($collection)
    {
        $this->_itemsCollection = $collection;
        return $this;
    }

    /**
     * Retrieve compare product helper
     *
     * @return Mage_Catalog_Helper_Product_Compare
     */
    public function getCompareProductHelper()
    {
        return $this->_getHelper();
    }

    /**
     * Retrieve Clean Compared Items URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return $this->_getHelper()->getClearListUrl();
    }

    /**
     * Retrieve Full Compare page URL
     *
     * @return string
     */
    public function getCompareUrl()
    {
        return $this->_getHelper()->getListUrl();
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        $compareItem = Mage::getModel('catalog/product_compare_item');
        foreach ($this->getItems() as $product) {
            $this->addModelTags($product);
            $this->addModelTags(
                $compareItem->setId($product->getCatalogCompareItemId()),
            );
        }

        return parent::getCacheTags();
    }
}
