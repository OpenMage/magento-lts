<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Mass Action processing model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Action _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Action getResource()
 */
class Mage_Catalog_Model_Product_Action extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalog/product_action');
    }

    /**
     * Update attribute values for entity list per store
     *
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return $this
     */
    public function updateAttributes($productIds, $attrData, $storeId)
    {
        Mage::dispatchEvent('catalog_product_attribute_update_before', [
            'attributes_data' => &$attrData,
            'product_ids'   => &$productIds,
            'store_id'      => &$storeId,
        ]);

        $this->_getResource()->updateAttributes($productIds, $attrData, $storeId);
        $this->setData([
            'product_ids'       => array_unique($productIds),
            'attributes_data'   => $attrData,
            'store_id'          => $storeId,
        ]);

        // register mass action indexer event
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            Mage_Catalog_Model_Product::ENTITY,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        );

        Mage::dispatchEvent('catalog_product_attribute_update_after', [
            'product_ids'   => $productIds,
        ]);

        return $this;
    }

    /**
     * Update websites for product action
     *
     * allowed types:
     * - add
     * - remove
     *
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     */
    public function updateWebsites($productIds, $websiteIds, $type)
    {
        Mage::dispatchEvent('catalog_product_website_update_before', [
            'website_ids'   => $websiteIds,
            'product_ids'   => $productIds,
            'action'        => $type,
        ]);

        if ($type === 'add') {
            Mage::getModel('catalog/product_website')->addProducts($websiteIds, $productIds);
        } elseif ($type === 'remove') {
            Mage::getModel('catalog/product_website')->removeProducts($websiteIds, $productIds);
        }

        $this->setData([
            'product_ids' => array_unique($productIds),
            'website_ids' => $websiteIds,
            'action_type' => $type,
        ]);

        // register mass action indexer event
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            Mage_Catalog_Model_Product::ENTITY,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        );

        // add back compatibility system event
        Mage::dispatchEvent('catalog_product_website_update', [
            'website_ids'   => $websiteIds,
            'product_ids'   => $productIds,
            'action'        => $type,
        ]);
    }
}
