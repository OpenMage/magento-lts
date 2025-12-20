<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * SEO Products Sitemap block
 *
 * @package    Mage_Catalog
 *
 * @method $this setCollection(Mage_Catalog_Model_Resource_Product_Collection $value)
 */
class Mage_Catalog_Block_Seo_Sitemap_Product extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{
    /**
     * Initialize products collection
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')
            ->addStoreFilter()
            ->addAttributeToFilter('status', [
                'in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(),
            ]);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->setCollection($collection);
        return $this;
    }

    /**
     * Get item URL
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getItemUrl($product)
    {
        /** @var Mage_Catalog_Helper_Product $helper */
        $helper = Mage::helper('catalog/product');
        return $helper->getProductUrl($product);
    }
}
