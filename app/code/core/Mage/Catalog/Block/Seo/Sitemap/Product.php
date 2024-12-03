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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SEO Products Sitemap block
 *
 * @category   Mage
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
                'in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds()
            ]);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->setCollection($collection);
        return $this;
    }

    /**
     * Get item URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getItemUrl($product)
    {
        /** @var Mage_Catalog_Helper_Product $helper */
        $helper = Mage::helper('catalog/product');
        return $helper->getProductUrl($product);
    }
}
