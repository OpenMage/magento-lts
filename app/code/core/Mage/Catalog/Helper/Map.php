<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog (site)map helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Map extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_USE_TREE_MODE = 'catalog/sitemap/tree_mode';

    protected $_moduleName = 'Mage_Catalog';

    /**
     * @return string
     */
    public function getCategoryUrl()
    {
        return $this->_getUrl('catalog/seo_sitemap/category');
    }

    /**
     * @return string
     */
    public function getProductUrl()
    {
        return $this->_getUrl('catalog/seo_sitemap/product');
    }

    /**
     * Return true if category tree mode enabled
     *
     * @return bool
     */
    public function getIsUseCategoryTreeMode()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_USE_TREE_MODE);
    }
}
