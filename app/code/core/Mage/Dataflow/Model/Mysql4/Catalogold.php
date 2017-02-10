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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
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

    public function getProductEntity($field=null)
    {
        if (!$this->_productEntity) {
            $this->_productEntity = Mage::getResourceModel('catalog/product')
                ->loadAllAttributes();
        }
        return is_null($field) ? $this->_productEntity : $this->_productEntity->getData($field);
    }

    public function getSkuAttribute($field='attribute_id')
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
                ->from($this->getTable('catalog/product'), array('entity_id', 'sku'));
            $products = $this->getConnection()->fetchAll($select);

            $this->_productsBySku = array();
            foreach ($products as $p) {
                $this->_productsBySku[$p['sku']] = $p['entity_id'];
            }
        }
        return isset($this->_productsBySku[$sku]) ? $this->_productsBySku[$sku] : false;
    }

    public function addProductToStore($productId, $storeId)
    {
        $write = $this->getConnection();
        $table = $this->getTable('catalog/product_store');
        try {
            if (!$write->fetchOne("select * from $table where product_id=".(int)$productId." and store_id=".(int)$storeId)) {
               $write->query("insert into $table (product_id, store_id) values (".(int)$productId.",".(int)$storeId.")");
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $this;
    }

    public function exportAttributes()
    {
        $attributeFields = array(
            'attribute_code',
            'frontend_label', 'frontend_input', 'frontend_class', 'frontend_model',
            'backend_type', 'backend_table', 'backend_model',
            'source_model', 'attribute_model',
            'is_visible', 'is_user_defined', 'is_global', 'is_required', 'is_unique',
            'is_visible_on_front', 'is_searchable', 'is_filterable', 'is_comparable',
            'default_value', 'apply_to', 'use_in_super_product',
        );

        $select = $this->getSelect()
            ->from(array('et'=>$this->getTable('eav/entity_type')), 'entity_type_code')
            ->join(array('a'=>$this->getTable('eav/attribute')), 'a.entity_type_id=et.entity_type_id', $attributeFields)
            ->where('et.entity_type_code in (?)', array('catalog_product', 'catalog_category'))
            ->order('if(not a.is_user_defined, 1, 2)')->order('attribute_code');

        $attributes = $this->getConnection()->fetchAll($select);

        return $attributes;
    }

    public function exportAttributeSets()
    {
        $select = $this->getSelect()
            ->from(array('et'=>$this->getTable('eav/entity_type')), 'entity_type_code')
            ->join(array('s'=>$this->getTable('eav/attribute_set')), 's.entity_type_id=et.entity_type_id', 'attribute_set_name')
            ->join(array('g'=>$this->getTable('eav/attribute_group')), 'g.attribute_set_id=s.attribute_set_id', 'attribute_group_name')
            ->join(array('ea'=>$this->getTable('eav/entity_attribute')), 'ea.attribute_group_id=g.attribute_group_id', array())
            ->join(array('a'=>$this->getTable('eav/attribute')), 'a.attribute_id=ea.attribute_id', 'attribute_code')
            ->where('et.entity_type_code in (?)', array('catalog_product', 'catalog_category'))
            ->order('et.entity_type_code')->order('s.sort_order')->order('g.sort_order');

        $sets = $this->getConnection()->fetchAll($select);

        return $sets;
    }

    public function exportAttributeOptions()
    {
        $select = $this->getSelect()
            ->from(array('et'=>$this->getTable('eav/entity_type')), 'entity_type_code')
            ->join(array('a'=>$this->getTable('eav/attribute')), 'a.entity_type_id=et.entity_type_id', 'attribute_code')
            ->join(array('ao'=>$this->getTable('eav/attribute_option')), 'ao.attribute_id=a.attribute_id', array())
            ->where('et.entity_type_code in (?)', array('catalog_product', 'catalog_category'))
            ->order('a.attribute_code')->order('ao.sort_order');

        $stores = Mage::getConfig()->getNode('stores')->children();
        foreach ($stores as $storeName=>$storeConfig) {
            $select->joinLeft(
                array($storeName=>$this->getTable('eav/attribute_option_value')),
                "$storeName.option_id=ao.option_id and $storeName.store_id=".$storeConfig->descend('system/store/id'),
                array($storeName=>"$storeName.value")
            );
        }

        $options = $this->getConnection()->fetchAll($select);

        return $options;
    }

    public function exportProductLinks()
    {
        $skuTable = $this->getTable('catalog/product').'_'.$this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id='.$this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(array('lt'=>$this->getTable('catalog/product_link_type')), array('link_type'=>'code'))
            ->join(array('l'=>$this->getTable('catalog/product_link')), 'l.link_type_id=lt.link_type_id', array())
            ->join(array('sku'=>$skuTable), 'sku.entity_id=l.product_id'.$skuCond, array('sku'=>'value'))
            ->join(array('linked'=>$skuTable), 'linked.entity_id=l.product_id'.$skuCond, array('linked'=>'value'))
            ->order('sku')->order('link_type');
        $links = $this->getConnection()->fetchAll($select);

        return $links;
    }

    public function exportProductsInCategories()
    {
        $skuTable = $this->getTable('catalog/product').'_'.$this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id='.$this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(array('cp'=>$this->getTable('catalog/category_product')), array('category_id', 'position'))
            ->join(array('sku'=>$skuTable), 'sku.entity_id=cp.product_id'.$skuCond, array('sku'=>'value'))
            ->order('category_id')->order('position')->order('sku');

        $prodCats = $this->getConnection()->fetchAll($select);

        return $prodCats;
    }

    public function exportProductsInStores()
    {
        $skuTable = $this->getTable('catalog/product').'_'.$this->getSkuAttribute('backend_type');
        $skuCond = ' and sku.store_id=0 and sku.attribute_id='.$this->getSkuAttribute('attribute_id');

        $select = $this->getSelect()
            ->from(array('ps'=>$this->getTable('catalog/product_store')), array())
            ->join(array('s'=>$this->getTable('core/store')), 's.store_id=ps.store_id', array('store'=>'code'))
            ->join(array('sku'=>$skuTable), 'sku.entity_id=ps.product_id'.$skuCond, array('sku'=>'value'))
            ->order('store')->order('sku');

        $prodStores = $this->getConnection()->fetchAll($select);

        return $prodStores;
    }

    public function exportCategories()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect('*')
            ->load();

        $categories = array();
        foreach ($collection as $object) {
            $row = $object->getData();
            $categories[] = $row;
        }

        return $categories;
    }

    public function exportProducts()
    {
        $attrSets = Mage::getResourceModel('eav/entity_attribute_set_collection')->load();
        $attrSetName = array();
        foreach ($attrSets as $attrSet) {
            $attrSetName[$attrSet->getId()] = $attrSet->getAttributeSetName();
        }

        $select = $this->getSelect()
            ->from(array('ao'=>$this->getTable('eav/attribute_option')), array('attribute_id', 'option_id'))
            ->join(array('aov'=>$this->getTable('eav/attribute_option_value')), 'aov.option_id=ao.option_id', array('value_id', 'value'))
            ->where('aov.store_id=0');

        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->load();

        $products = array();
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
        return array();
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
