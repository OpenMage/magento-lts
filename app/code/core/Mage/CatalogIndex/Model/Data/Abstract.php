<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * CatalogIndex Data Retriever Abstract Model
 *
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Data_Abstract getResource()
 *
 * @method array getMinimalPriceData()
 * @method $this setMinimalPriceData(array $data)
 */
class Mage_CatalogIndex_Model_Data_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Product Type instance
     *
     * @var Mage_Catalog_Model_Product_Type_Abstract|Mage_Core_Model_Abstract|null
     */
    protected $_typeInstance;

    /**
     * Defines when product type has children
     *
     * @var int[]|bool[]
     */
    protected $_haveChildren = [
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS => true,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES => true,
        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES => true,
    ];

    /**
     * Defines when product type has parents
     *
     * @var bool
     */
    protected $_haveParents = true;

    public const LINK_GET_CHILDREN = 1;
    public const LINK_GET_PARENTS = 1;

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
     * @param int|array $parentIds
     * @return array|false
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
     * @param int|array $childIds
     * @return array|false
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
     * @param int|array $suppliedId
     * @return array
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

        $additional = [];
        if (isset($settings['additional']) && is_array($settings['additional'])) {
            $additional = $settings['additional'];
        }

        return $this->getResource()->fetchLinkInformation($store->getId(), $settings['table'], $idField, $whereField, $suppliedId, $additional);
    }

    /**
     * Fetch final price for product
     *
     * @param array $product
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

        $attributes = [$priceId, $specialPriceId, $specialPriceFromId, $specialPriceToId];

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

        return Mage::getSingleton('catalog/product_type')
            ->priceFactory($this->getTypeCode())
            ->calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, false, $store, $group, $product);
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
        $priceAttributes = [
            Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'tier_price'),
            Mage::getSingleton('eav/entity_attribute')->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'price')];

        $data = $this->getResource()->getMinimalPrice($products, $priceAttributes, $store->getId());

        $this->setMinimalPriceData($data);
        $eventData = ['indexer' => $this, 'product_ids' => $products, 'store' => $store];
        Mage::dispatchEvent('catalogindex_get_minimal_price', $eventData);

        return $this->getMinimalPriceData();
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
        $taxClassId  = $this->getResource()->getAttributeData([$productId], [$attributeId], $store->getId());
        if (is_array($taxClassId) && isset($taxClassId[0]['value'])) {
            $taxClassId = $taxClassId[0]['value'];
        } else {
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
     * Retrieve specified attribute data for specified products from specified store
     *
     * @param array|string $products
     * @param array $attributes
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getAttributeData($products, $attributes, $store)
    {
        return $this->getResource()->getAttributeData($products, $attributes, $store->getId());
    }

    /**
     * Retrieve product type code
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
