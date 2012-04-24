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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog indexer price processor
 *
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice _getResource()
 * @method Mage_CatalogIndex_Model_Resource_Indexer_Minimalprice getResource()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setCustomerGroupId(int $value)
 * @method float getQty()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setQty(float $value)
 * @method float getValue()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setValue(float $value)
 * @method int getTaxClassId()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setTaxClassId(int $value)
 * @method int getWebsiteId()
 * @method Mage_CatalogIndex_Model_Indexer_Minimalprice setWebsiteId(int $value)
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Indexer_Minimalprice extends Mage_CatalogIndex_Model_Indexer_Abstract
{
    protected $_customerGroups = array();
    protected $_runOnce = true;
    protected $_processChildren = false;

    protected function _construct()
    {
        $this->_init('catalogindex/indexer_minimalprice');
        $this->_currencyModel = Mage::getModel('directory/currency');
        $this->_customerGroups = Mage::getModel('customer/group')->getCollection();

        return parent::_construct();
    }

    public function getTierPriceAttribute()
    {
        $data = $this->getData('tier_price_attribute');
        if (is_null($data)) {
            $data = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'tier_price');
            $this->setData('tier_price_attribute', $data);
        }
        return $data;
    }

    public function getPriceAttribute()
    {
        $data = $this->getData('price_attribute');
        if (is_null($data)) {
            $data = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'price');
            $this->setData('price_attribute', $data);
        }
        return $data;
    }

    public function createIndexData(Mage_Catalog_Model_Product $object, Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null)
    {
        $searchEntityId = $object->getId();
        $priceAttributeId = $this->getTierPriceAttribute()->getId();
        if ($object->isGrouped()) {
            $priceAttributeId = $this->getPriceAttribute()->getId();
            $associated = $object->getTypeInstance(true)->getAssociatedProducts($object);
            $searchEntityId = array();

            foreach ($associated as $product) {
                $searchEntityId[] = $product->getId();
            }
        }

        if (!count($searchEntityId)) {
            return false;
        }

        $result = array();
        $data = array();

        $data['store_id'] = $object->getStoreId();
        $data['entity_id'] = $object->getId();

        $search['store_id'] = $object->getStoreId();
        $search['entity_id'] = $searchEntityId;
        $search['attribute_id'] = $priceAttributeId;

        foreach ($this->_customerGroups as $group) {
            $search['customer_group_id'] = $group->getId();
            $data['customer_group_id'] = $group->getId();

            $value = $this->_getResource()->getMinimalValue($search);
            if (is_null($value))
                continue;
            $data['value'] = $value;
            $result[] = $data;
        }

        return $result;
    }

    public function isAttributeIdUsed()
    {
        return false;
    }

    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        if ($attribute->getAttributeCode() != 'minimal_price')
            return false;

        return true;
    }
}
