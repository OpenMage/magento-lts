<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog indexer price processor
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice getResource()
 * @method $this setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method float getQty()
 * @method $this setQty(float $value)
 * @method float getValue()
 * @method $this setValue(float $value)
 * @method int getTaxClassId()
 * @method $this setTaxClassId(int $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 */
class Mage_CatalogIndex_Model_Indexer_Minimalprice extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $_currencyModel;

    /**
     * @var Mage_Customer_Model_Resource_Group_Collection
     */
    protected $_customerGroups;

    protected $_runOnce = true;
    protected $_processChildren = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_minimalprice');
        $this->_currencyModel = Mage::getModel('directory/currency');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();

        parent::_construct();
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Abstract|mixed
     * @throws Mage_Core_Exception
     */
    public function getTierPriceAttribute()
    {
        $data = $this->getData('tier_price_attribute');
        if (is_null($data)) {
            $data = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'tier_price');
            $this->setData('tier_price_attribute', $data);
        }
        return $data;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute_Abstract|mixed
     * @throws Mage_Core_Exception
     */
    public function getPriceAttribute()
    {
        $data = $this->getData('price_attribute');
        if (is_null($data)) {
            $data = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price');
            $this->setData('price_attribute', $data);
        }
        return $data;
    }

    /**
     * @return array|bool
     */
    public function createIndexData(Mage_Catalog_Model_Product $object, ?Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
    {
        $searchEntityId = $object->getId();
        $priceAttributeId = $this->getTierPriceAttribute()->getId();
        if ($object->isGrouped()) {
            $priceAttributeId = $this->getPriceAttribute()->getId();
            /** @var Mage_Catalog_Model_Product_Type_Grouped $productType */
            $productType = $object->getTypeInstance(true);
            $associated = $productType->getAssociatedProducts($object);
            $searchEntityId = [];

            foreach ($associated as $product) {
                $searchEntityId[] = $product->getId();
            }
        }

        if (!count($searchEntityId)) {
            return false;
        }

        $result = [];
        $data = [];

        $data['store_id'] = $object->getStoreId();
        $data['entity_id'] = $object->getId();

        $search['store_id'] = $object->getStoreId();
        $search['entity_id'] = $searchEntityId;
        $search['attribute_id'] = $priceAttributeId;

        foreach ($this->_customerGroups as $group) {
            $search['customer_group_id'] = $group->getId();
            $data['customer_group_id'] = $group->getId();

            $value = $this->_getResource()->getMinimalValue($search);
            if (is_null($value)) {
                continue;
            }
            $data['value'] = $value;
            $result[] = $data;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isAttributeIdUsed()
    {
        return false;
    }

    /**
     * @return bool
     */
    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getAttributeCode() != 'minimal_price') {
            return false;
        }

        return true;
    }
}
