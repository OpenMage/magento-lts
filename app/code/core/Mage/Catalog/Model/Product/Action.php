<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Mass Action processing model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'store_id'      => &$storeId
        ]);

        $this->_getResource()->updateAttributes($productIds, $attrData, $storeId);
        $this->setData([
            'product_ids'       => array_unique($productIds),
            'attributes_data'   => $attrData,
            'store_id'          => $storeId
        ]);

        // register mass action indexer event
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            Mage_Catalog_Model_Product::ENTITY,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
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
            'action'        => $type
        ]);

        if ($type === 'add') {
            Mage::getModel('catalog/product_website')->addProducts($websiteIds, $productIds);
        } elseif ($type === 'remove') {
            Mage::getModel('catalog/product_website')->removeProducts($websiteIds, $productIds);
        }

        $this->setData([
            'product_ids' => array_unique($productIds),
            'website_ids' => $websiteIds,
            'action_type' => $type
        ]);

        // register mass action indexer event
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this,
            Mage_Catalog_Model_Product::ENTITY,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        );

        // add back compatibility system event
        Mage::dispatchEvent('catalog_product_website_update', [
            'website_ids'   => $websiteIds,
            'product_ids'   => $productIds,
            'action'        => $type
        ]);
    }
}
