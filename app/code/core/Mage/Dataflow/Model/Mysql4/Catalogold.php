<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Dataflow
 * @deprecated after 1.5.0.1
 */
class Mage_Catalog_Model_Mysql4_Convert
{
    protected $_productsBySku;
    protected $_productEntity;
    protected $_skuAttribute;

    public function getConnection()
    {
        return Mage::getSingleton('core/resource')->getConnection('catalog_write');
    }

    public function getSelect()
    {
        return $this->getConnection()->select();
    }

    public function getTable($table)
    {
        return Mage::getSingleton('core/resource')->getTableName($table);
    }

    public function getProductEntity($field = null)
    {
        if (!$this->_productEntity) {
            $this->_productEntity = Mage::getResourceModel('catalog/product')
                ->loadAllAttributes();
        }
        return is_null($field) ? $this->_productEntity : $this->_productEntity->getData($field);
    }

    public function getSkuAttribute($field = 'attribute_id')
    {
        if (!$this->_skuAttribute) {
            $this->_skuAttribute = $this->getProductEntity()->getAttribute('sku');
        }
        return $this->_skuAttribute->getData($field);
    }

    public function getProductIdBySku($sku)
    {
        if (!$this->_productsBySku) {
            $select = $this->getSelect()
                ->from($this->getTable('catalog/product'), ['entity_id', 'sku']);
            $products = $this->getConnection()->fetchAll($select);

            $this->_productsBySku = [];
            foreach ($products as $p) {
                $this->_productsBySku[$p['sku']] = $p['entity_id'];
            }
        }
        return $this->_productsBySku[$sku] ?? false;
    }

    public function addProductToStore($productId, $storeId)
    {
        $write = $this->getConnection();
        $table = $this->getTable('catalog/product_store');
        try {
            if (!$write->fetchOne("select * from $table where product_id=" . (int)$productId . " and store_id=" . (int)$storeId)) {
                $write->query("insert into $table (product_id, store_id) values (" . (int)$productId . "," . (int)$storeId . ")");
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this;
    }

    public function exportAttributes()
    {
        $attributeFields = [
            'attribute_code',
            'frontend_label', 'frontend_input', 'frontend_class', 'frontend_model',
            'backend_type', 'backend_table', 'backend_model',
            'source_model', 'attribute_model',
            'is_visible', 'is_user_defined', 'is_global', 'is_required', 'is_unique',
            'is_visible_on_front', 'is_searchable', 'is_filterable', 'is_comparable',
            'default_value', 'apply_to', 'use_in_super_product',
        ];

        $select = $this->getSelect()
            ->from(['et' => $this->getTable('eav/entity_type')], 'entity_type_code')
            ->join(['a' => $this->getTable('eav/attribute')], 'a.entity_type_id=et.entity_type_id', $attributeFields)
            ->where('et.entity_type_code in (?)', ['catalog_product', 'catalog_category'])
            ->order('if(not a.is_user_defined, 1, 2)')->order('attribute_code');

        return $this->getConnection()->fetchAll($select);
    }

    public function exportAttributeSets()
    {
        $select = $this->getSelect()
            ->from(['et' => $this->getTable('eav/entity_type')], 'entity_type_code')
            ->join(['s' => $this->getTable('eav/attribute_set')], 's.entity_type_id=et.entity_type_id', 'attribute_set_name')
            ->join(['g' => $this->getTable('eav/attribute_group')], 'g.attribute_set_id=s.attribute_set_id', 'attribute_group_name')
            ->join(['ea' => $this->getTable('eav/entity_attribute')], 'ea.attribute_group_id=g.attribute_group_id', [])
            ->join(['a' => $this->getTable('eav/attribute')], 'a.attribute_id=ea.attribute_id', 'attribute_code')
            ->where('et.entity_type_code in (?)', ['catalog_product', 'catalog_category'])
            ->order('et.entity_type_code')->order('s.sort_order')->order('g.sort_order');

        return $this->getConnection()->fetchAll($select);
    }

    public function exportAttributeOptions()
    {
        $select = $this->getSelect()
            ->from(['et' => $this->getTable('eav/entity_type')], 'entity_type_code')
            ->join(['a' => $this->getTable('eav/attribute')], 'a.entity_type_id=et.entity_type_id', 'attribute_code')
            ->join(['ao' => $this->getTable('eav/attribute_option')], 'ao.attribute_id=a.attribute_id', [])
            ->where('et.entity_type_code in (?)', ['catalog_product', 'catalog_category'])
            ->order('a.attribute_code')->order('ao.sort_order');

        $stores = Mage::getConfig()->getNode('stores')->children();
        foreach ($stores as $storeName => $storeConfig) {
            $select->joinLeft(
                [$storeName => $this->getTable('eav/attribute_option_value')],
                "$storeName.option_id=ao.option_id and $storeName.store_id=" . $storeConfig->descend('system/store/id'),
                [$storeName => "$storeName.value"]
            );
        }

        return $this->getConnection()->fetchAll($select);
    }

    public function exportProductLinks()
    {
        $skuTable = $this->getTable('catalog/product') . '_' . $this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id=' . $this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(['lt' => $this->getTable('catalog/product_link_type')], ['link_type' => 'code'])
            ->join(['l' => $this->getTable('catalog/product_link')], 'l.link_type_id=lt.link_type_id', [])
            ->join(['sku' => $skuTable], 'sku.entity_id=l.product_id' . $skuCond, ['sku' => 'value'])
            ->join(['linked' => $skuTable], 'linked.entity_id=l.product_id' . $skuCond, ['linked' => 'value'])
            ->order('sku')->order('link_type');
        return $this->getConnection()->fetchAll($select);
    }

    public function exportProductsInCategories()
    {
        $skuTable = $this->getTable('catalog/product') . '_' . $this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id=' . $this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(['cp' => $this->getTable('catalog/category_product')], ['category_id', 'position'])
            ->join(['sku' => $skuTable], 'sku.entity_id=cp.product_id' . $skuCond, ['sku' => 'value'])
            ->order('category_id')->order('position')->order('sku');

        return $this->getConnection()->fetchAll($select);
    }

    public function exportProductsInStores()
    {
        $skuTable = $this->getTable('catalog/product') . '_' . $this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id=' . $this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(['ps' => $this->getTable('catalog/product_store')], [])
            ->join(['s' => $this->getTable('core/store')], 's.store_id=ps.store_id', ['store' => 'code'])
            ->join(['sku' => $skuTable], 'sku.entity_id=ps.product_id' . $skuCond, ['sku' => 'value'])
            ->order('store')->order('sku');

        return $this->getConnection()->fetchAll($select);
    }

    public function exportCategories()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->load();

        $categories = [];
        foreach ($collection as $object) {
            $row = $object->getData();
            $categories[] = $row;
        }

        return $categories;
    }

    public function exportProducts()
    {
        $attrSets = Mage::getResourceModel('eav/entity_attribute_set_collection')->load();
        $attrSetName = [];
        foreach ($attrSets as $attrSet) {
            $attrSetName[$attrSet->getId()] = $attrSet->getAttributeSetName();
        }

        $select = $this->getSelect()
            ->from(['ao' => $this->getTable('eav/attribute_option')], ['attribute_id', 'option_id'])
            ->join(['aov' => $this->getTable('eav/attribute_option_value')], 'aov.option_id=ao.option_id', ['value_id', 'value'])
            ->where('aov.store_id=0');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->load();

        $products = [];
        foreach ($collection as $object) {
            $r = $object->getData();

            unset($r['entity_id'], $r['entity_type_id']);
            $r['attribute_set_id'] = $attrSetName[$r['attribute_set_id']];

            $products[] = $r;
        }

        return $products;
    }

    public function exportImageGallery()
    {
        return [];
    }

    public function getProductAttributeOption($attribute, $value)
    {
        #$attribute = Mage::get
    }

    public function importProducts(array $data)
    {
        /*
        $entity = Mage::getResourceModel('catalog/product')
           ->loadAllAttributes();

        $options =

        foreach ($data as $row) {
            if (empty($row['sku'])) {
                continue;
            }
            $sku = $row['sku'];
        }
        */
    }
}
