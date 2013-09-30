<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogIndex Data Retreiver Abstract Model
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Data_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Product Type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract
     */
    protected $_typeInstance;

    /**
     * Defines when product type has children
     *
     * @var boolean
     */
    protected $_haveChildren = array(
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS=>true,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES=>true,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES=>true,
                        );

    /**
     * Defines when product type has parents
     *
     * @var boolean
     */
    protected $_haveParents = true;

    const LINK_GET_CHILDREN = 1;
    const LINK_GET_PARENTS = 1;

    /**
     * Initialize abstract resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalogindex/data_abstract');
    }

    /**
     * Return all children ids
     *
     * @param Mage_Core_Model_Store $store
     * @param int $parentId
     * @return mixed
     */
    public function getChildProductIds($store, $parentIds)
    {
        if (!$this->_haveChildren) {
            return false;
        }

        if (!$this->_getLinkSettings()) {
            return false;
        }

        return $this->fetchLinkInformation($store, $this->_getLinkSettings(), self::LINK_GET_CHILDREN, $parentIds);
    }

    /**
     * Return all parent ids
     *
     * @param Mage_Core_Model_Store $store
     * @param int $childId
     * @return mixed
     */
    public function getParentProductIds($store, $childIds)
    {
        if (!$this->_haveParents) {
            return false;
        }

        if (!$this->_getLinkSettings()) {
            return false;
        }

        return $this->fetchLinkInformation($store, $this->_getLinkSettings(), self::LINK_GET_PARENTS, $childIds);
    }

    /**
     * Returns an array of product children/parents
     *
     * @param Mage_Core_Model_Store $store
     * @param array $settings
     * @param int $type
     * @param int $suppliedId
     */
    protected function fetchLinkInformation($store, $settings, $type, $suppliedId)
    {
        switch ($type) {
            case self::LINK_GET_CHILDREN:
                $whereField = $settings['parent_field'];
                $idField = $settings['child_field'];
                break;

            case self::LINK_GET_PARENTS:
                $idField = $settings['parent_field'];
                $whereField = $settings['child_field'];
                break;
        }

        $additional = array();
        if (isset($settings['additional']) && is_array($settings['additional'])) {
            $additional = $settings['additional'];
        }

        return $this->getResource()->fetchLinkInformation($store->getId(), $settings['table'], $idField, $whereField, $suppliedId, $additional);
    }

    /**
     * Fetch final price for product
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @param Mage_Customer_Model_Group $group
     * @return float
     */
    public function getFinalPrice($product, $store, $group)
    {
        $basePrice = $specialPrice = $specialPriceFrom = $specialPriceTo = null;
        $priceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'price');
        $specialPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'special_price');
        $specialPriceFromId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'special_from_date');
        $specialPriceToId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'special_to_date');

        $attributes = array($priceId, $specialPriceId, $specialPriceFromId, $specialPriceToId);

        $productData = $this->getAttributeData($product, $attributes, $store);
        foreach ($productData as $row) {
            switch ($row['attribute_id']) {
                case $priceId:
                    $basePrice = $row['value'];
                break;
                case $specialPriceId:
                    $specialPrice = $row['value'];
                break;
                case $specialPriceFromId:
                    $specialPriceFrom = $row['value'];
                break;
                case $specialPriceToId:
                    $specialPriceTo = $row['value'];
                break;
            }
        }

        $finalPrice = Mage::getSingleton('catalog/product_type')
            ->priceFactory($this->getTypeCode())
            ->calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, false, $store, $group, $product);

        return $finalPrice;
    }

    /**
     * Return minimal prices for specified products
     *
     * @param array $products
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getMinimalPrice($products, $store)
    {
        $priceAttributes = array(
            Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'tier_price'),
            Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'price'));

        $data = $this->getResource()->getMinimalPrice($products, $priceAttributes, $store->getId());

        $this->setMinimalPriceData($data);
        $eventData = array('indexer'=>$this, 'product_ids'=>$products, 'store'=>$store);
        Mage::dispatchEvent('catalogindex_get_minimal_price', $eventData);
        $data = $this->getMinimalPriceData();

        return $data;
    }

    /**
     * Get tax class id for a product
     *
     * @param int $productId
     * @param Mage_Core_Model_Store $store
     * @return int
     */
    public function getTaxClassId($productId, $store)
    {
        $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'tax_class_id');
        $taxClassId  = $this->getResource()->getAttributeData(array($productId), array($attributeId), $store->getId());
        if (is_array($taxClassId) && isset($taxClassId[0]['value'])) {
            $taxClassId = $taxClassId[0]['value'];
        }
        else {
            $taxClassId = 0;
        }
        return $taxClassId;
    }

    /**
     * Return tier data for specified products in specified store
     *
     * @param array $products
     * @param Mage_Core_Model_Store $store
     * @return mixed
     */
    public function getTierPrices($products, $store)
    {
        return $this->getResource()->getTierPrices($products, $store->getWebsiteId());
    }

    /**
     * Retreive specified attribute data for specified products from specified store
     *
     * @param array $products
     * @param array $attributes
     * @param Mage_Core_Model_Store $store
     */
    public function getAttributeData($products, $attributes, $store)
    {
        return $this->getResource()->getAttributeData($products, $attributes, $store->getId());
    }

    /**
     * Retreive product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        Mage::throwException('Define custom data retreiver with getTypeCode function');
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return false;
    }

    /**
     * Returns if type supports children of the specified type
     *
     * @param int $type
     * @return bool
     */
    public function areChildrenIndexable($type)
    {
        if (!$this->_haveChildren || !isset($this->_haveChildren[$type]) || !$this->_haveChildren[$type]) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve Product Type Instance
     *
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    public function getTypeInstance()
    {
        if (is_null($this->_typeInstance)) {
            $product = new Varien_Object();
            $product->setTypeId($this->getTypeCode());
            $this->_typeInstance = Mage::getSingleton('catalog/product_type')
                ->factory($product, true);
        }
        return $this->_typeInstance;
    }
}
