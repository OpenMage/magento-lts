<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product country attribute source
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Countryofmanufacture extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get list of all available countries
     *
     * @return mixed
     */
    public function getAllOptions()
    {
        $cacheKey = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache, ['allowed_classes' => false]);
        } else {
            $collection = Mage::getModel('directory/country')->getResourceCollection();
            if (!Mage::app()->getStore()->isAdmin()) {
                $collection->loadByStore();
            }
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, ['config']);
            }
        }
        return $options;
    }
}
