<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Class Mage_Catalog_Model_Config
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Config extends Mage_Eav_Model_Config
{
    public const XML_PATH_LIST_DEFAULT_SORT_BY     = 'catalog/frontend/default_sort_by';

    protected $_attributeSetsById;

    protected $_attributeSetsByName;

    protected $_attributeGroupsById;

    protected $_attributeGroupsByName;

    /**
     * @var array
     */
    protected $_productTypesById;

    /**
     * @var array
     */
    protected $_productTypesByName;

    /**
     * Array of attributes codes needed for product load
     *
     * @var null|array
     */
    protected $_productAttributes;

    /**
     * Product Attributes used in product listing
     *
     * @var null|array
     */
    protected $_usedInProductListing;

    /**
     * Product Attributes For Sort By
     *
     * @var null|array
     */
    protected $_usedForSortBy;

    protected $_storeId = null;

    public const XML_PATH_PRODUCT_COLLECTION_ATTRIBUTES = 'frontend/product/collection/attributes';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalog/config');
    }

    /**
     * @param  int   $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId ?? Mage::app()->getStore()->getId();
    }

    /**
     * @return $this
     */
    public function loadAttributeSets()
    {
        if ($this->_attributeSetsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->load();

        $this->_attributeSetsById = [];
        $this->_attributeSetsByName = [];
        foreach ($attributeSetCollection as $id => $attributeSet) {
            $entityTypeId = $attributeSet->getEntityTypeId();
            $name = $attributeSet->getAttributeSetName();
            $this->_attributeSetsById[$entityTypeId][$id] = $name;
            $this->_attributeSetsByName[$entityTypeId][strtolower($name)] = $id;
        }

        return $this;
    }

    /**
     * @param  int  $entityTypeId
     * @param  int  $id
     * @return bool
     */
    public function getAttributeSetName($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }

        return $this->_attributeSetsById[$entityTypeId][$id] ?? false;
    }

    /**
     * @param  int         $entityTypeId
     * @param  string      $name
     * @return bool|string
     */
    public function getAttributeSetId($entityTypeId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }

        $name = strtolower($name);
        return $this->_attributeSetsByName[$entityTypeId][$name] ?? false;
    }

    /**
     * @return $this
     */
    public function loadAttributeGroups()
    {
        if ($this->_attributeGroupsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->load();

        $this->_attributeGroupsById = [];
        $this->_attributeGroupsByName = [];
        foreach ($attributeSetCollection as $id => $attributeGroup) {
            $attributeSetId = $attributeGroup->getAttributeSetId();
            $name = $attributeGroup->getAttributeGroupName();
            $this->_attributeGroupsById[$attributeSetId][$id] = $name;
            $this->_attributeGroupsByName[$attributeSetId][strtolower($name)] = $id;
        }

        return $this;
    }

    /**
     * @param  int  $attributeSetId
     * @param  int  $id
     * @return bool
     */
    public function getAttributeGroupName($attributeSetId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }

        return $this->_attributeGroupsById[$attributeSetId][$id] ?? false;
    }

    /**
     * @param  int         $attributeSetId
     * @param  string      $name
     * @return bool|string
     */
    public function getAttributeGroupId($attributeSetId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }

        $name = strtolower($name);
        return $this->_attributeGroupsByName[$attributeSetId][$name] ?? false;
    }

    /**
     * @return $this
     */
    public function loadProductTypes()
    {
        if ($this->_productTypesById) {
            return $this;
        }

        /*
        $productTypeCollection = Mage::getResourceModel('catalog/product_type_collection')
            ->load();
        */
        $productTypeCollection = Mage::getModel('catalog/product_type')
            ->getOptionArray();

        $this->_productTypesById = [];
        $this->_productTypesByName = [];
        foreach ($productTypeCollection as $id => $type) {
            //$name = $type->getCode();
            $name = $type;
            $this->_productTypesById[$id] = $name;
            $this->_productTypesByName[strtolower($name)] = $id;
        }

        return $this;
    }

    /**
     * @param  string      $name
     * @return bool|string
     */
    public function getProductTypeId($name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadProductTypes();

        $name = strtolower($name);
        return $this->_productTypesByName[$name] ?? false;
    }

    /**
     * @param  int|string   $id
     * @return false|string
     */
    public function getProductTypeName($id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadProductTypes();

        return $this->_productTypesById[$id] ?? false;
    }

    /**
     * @param  Mage_Eav_Model_Entity_Attribute_Source_Interface $source
     * @param  string                                           $value
     * @return null|string
     */
    public function getSourceOptionId($source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value) == 0 || $option['value'] == $value) {
                return $option['value'];
            }
        }

        return null;
    }

    /**
     * Load Product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        if (is_null($this->_productAttributes)) {
            $this->_productAttributes = array_keys($this->getAttributesUsedInProductListing());
        }

        return $this->_productAttributes;
    }

    /**
     * Retrieve Product Collection Attributes from XML config file
     * Used only for install/upgrade
     *
     * @return array
     */
    public function getProductCollectionAttributes()
    {
        $attributes = Mage::getConfig()
            ->getNode(self::XML_PATH_PRODUCT_COLLECTION_ATTRIBUTES)
            ->asArray();
        return array_keys($attributes);
    }

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Config
     */
    protected function _getResource()
    {
        return Mage::getResourceModel('catalog/config');
    }

    /**
     * Retrieve Attributes used in product listing
     *
     * @return array
     */
    public function getAttributesUsedInProductListing()
    {
        if (is_null($this->_usedInProductListing)) {
            $allAttributes = Mage::getSingleton('eav/config')
                ->getAttributes(Mage_Catalog_Model_Product::ENTITY);
            $this->_usedInProductListing = [];
            foreach ($allAttributes as $attribute) {
                if ($attribute->getData('used_in_product_listing')) {
                    $this->_usedInProductListing[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        return $this->_usedInProductListing;
    }

    /**
     * Retrieve Attributes array used for sort by
     *
     * @return array
     */
    public function getAttributesUsedForSortBy()
    {
        if (is_null($this->_usedForSortBy)) {
            $allAttributes = Mage::getSingleton('eav/config')
                ->getAttributes(Mage_Catalog_Model_Product::ENTITY);
            $this->_usedForSortBy = [];
            foreach ($allAttributes as $attribute) {
                if ($attribute->getData('used_for_sort_by')) {
                    $this->_usedForSortBy[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        return $this->_usedForSortBy;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = [
            'position'  => Mage::helper('catalog')->__('Position'),
        ];
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute_Abstract $attribute */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }

    /**
     * Retrieve Product List Default Sort By
     *
     * @param  mixed  $store
     * @return string
     */
    public function getProductListDefaultSortBy($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
