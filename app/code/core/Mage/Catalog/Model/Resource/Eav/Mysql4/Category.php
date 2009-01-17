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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
{
    /**
     * Category tree object
     *
     * @var Varien_Data_Tree_Db
     */
    protected $_tree;

    /**
     * Catalog products table name
     *
     * @var string
     */
    protected $_categoryProductTable;


    /**
     * Id of 'is_active' category attribute
     *
     * @var int
     */
    protected $_isActiveAttributeId = null;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('catalog_category')
            ->setConnection(
                $resource->getConnection('catalog_read'),
                $resource->getConnection('catalog_write')
            );
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
    }

    /**
     * Retrieve category tree object
     *
     * @return Varien_Data_Tree_Db
     */
    protected function _getTree()
    {
        if (!$this->_tree) {
            $this->_tree = Mage::getResourceModel('catalog/category_tree')
                ->load();
        }
        return $this->_tree;
    }

    protected function _beforeDelete(Varien_Object $object){
        parent::_beforeDelete($object);

        $toUpdateChild = explode('/',substr($object->getPath(),0,strrpos($object->getPath(),'/')));
         $child = $this->getChildrenCount($object->getId());
         $child+=1; 
        // BUG here
        $this->_getWriteAdapter()->update(
		        $this->getEntityTable(),
		        array('children_count'=>new Zend_Db_Expr('`children_count`-'.$child)),
		        $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $toUpdateChild))
      		  ;

        if ($child = $this->_getTree()->getNodeById($object->getId())) {
            $children = $child->getChildren();
            foreach ($children as $child) {
                $childObject = Mage::getModel('catalog/category')->load($child->getId())->delete();
            }
        }

        return $this;
    }

    protected function _beforeSave(Varien_Object $object)
    {
        parent::_beforeSave($object);

        if (!$object->getId()) {
            $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            $path  = explode('/', $object->getPath());
            $level = count($path);
            $object->setLevel($level);
            if ($level) {
                $object->setParentId($path[$level - 1]);
            }
            $object->setPath($object->getPath() . '/');

           $toUpdateChild = explode('/',$object->getPath());

            $this->_getWriteAdapter()->update(
		        $this->getEntityTable(),
		        array('children_count'=>new Zend_Db_Expr('`children_count`+1')),
		        $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $toUpdateChild))
      		  ;

        }
        return $this;
    }

    protected function _afterSave(Varien_Object $object)
    {
        $this->_saveCategoryProducts($object);

        /**
         * Add identifier for new category
         */
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
            //$this->save($object);
        }
        $categoryIds = explode('/', $object->getPath());




        $this->refreshProductIndex($categoryIds);
        //$this->_saveCountChidren($object);
        return parent::_afterSave($object);
    }

    protected function _saveCountChidren($object)
    {
        $chidren = $object->getChildren();
        if (strlen($chidren)>0) {
            $chidrenCount = count(explode(',', $chidren));
        } else {
            $chidrenCount = 0;
        }
        $this->_getWriteAdapter()->update($this->getEntityTable(),
            array('children_count'=>$chidrenCount),
            $this->_getWriteAdapter()->quoteInto('entity_id=?', $object->getId())
        );

        return $this;
    }

    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update($this->getEntityTable(),
                array('path'=>$object->getPath()),
                $this->_getWriteAdapter()->quoteInto('entity_id=?', $object->getId())
            );
        }
        return $this;
    }

    protected function _getMaxPosition($path)
    {
        $select = $this->getReadConnection()->select();
        $select->from($this->getTable('catalog/category'), 'MAX(position)');
        $select->where('path ?', new Zend_Db_Expr("regexp '{$path}/[0-9]+\$'"));

        $result = 0;
        try {
            $result = (int) $this->getReadConnection()->fetchOne($select);
        } catch (Exception $e) {

        }
        return $result;
    }

    protected function _saveInStores(Varien_Object $object)
    {
        if (!$object->getMultistoreSaveFlag()) {
            $stores = $object->getStoreIds();
            foreach ($stores as $storeId) {
                if ($object->getStoreId() != $storeId) {
                    $newObject = clone $object;
                    $newObject->setStoreId($storeId)
                       ->setMultistoreSaveFlag(true)
                       ->save();
                }
            }
        }
        return $this;
    }

    /**
     * save category products
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Model_Entity_Category
     */
    protected function _saveCategoryProducts($category)
    {
        $category->setIsChangedProductList(false);
        // new category-product relationships
        $products = $category->getPostedProducts();

        // no category-product updates requested, returning
        if (is_null($products)) {
            return $this;
        }

        $catId = $category->getId();

        $prodTable = $this->getTable('catalog/product');

        // old category-product relationships
        $oldProducts = $category->getProductsPosition();

        $insert = array_diff_key($products, $oldProducts);
        $delete = array_diff_key($oldProducts, $products);
        $update = array_intersect_key($products, $oldProducts);

        $write = $this->getWriteConnection();
        $updateProducts = array();

        if (!empty($delete)) {
            $write->delete($this->_categoryProductTable,
                $write->quoteInto('product_id in(?)', array_keys($delete)) .
                $write->quoteInto(' AND category_id=?', $catId)
            );
            $select = $write->select()
                ->from($prodTable, array('entity_id', 'category_ids'))
                ->where('entity_id IN (?)', array_keys($delete));
            $prods = $write->fetchPairs($select);
            foreach ($prods as $k=>$v) {
                $a = !empty($v) ? explode(',', $v) : array();
                $key = array_search($catId, $a);
                if ($key!==false) {
                    unset($a[$key]);
                }
                $updateProducts[$k] = "when ".(int)$k." then '".implode(',', array_unique($a))."'";
            }
        }

        if (!empty($insert)) {
            $insertSql = array();
            foreach ($insert as $k=>$v) {
                $insertSql[] = '('.(int)$catId.','.(int)$k.','.(int)$v.')';
            }

            $write->query("insert into {$this->_categoryProductTable} (category_id, product_id, position) values ".join(',', $insertSql));

            $select = $write->select()
                ->from($prodTable, array('entity_id', 'category_ids'))
                ->where('entity_id IN (?)', array_keys($insert));

            $prods = $write->fetchPairs($select);
            foreach ($prods as $k=>$v) {
                $a = !empty($v) ? explode(',', $v) : array();
                $a[] = (int)$catId;
                $updateProducts[$k] = "when ".(int)$k." then '".implode(',', array_unique($a))."'";
            }
        }

        if (!empty($updateProducts)) {
            $write->update($prodTable,
                array('category_ids'=>new Zend_Db_Expr('case entity_id '.join(' ', $updateProducts).' end')),
                $write->quoteInto('entity_id in (?)', array_keys($updateProducts))
            );
        }

        if (!empty($update)) {
            $updateProductsPosition = array();
            foreach ($update as $k=>$v) {
                if ($v!=$oldProducts[$k]) {
                    $updateProductsPosition[$k] = 'when '.(int)$k.' then '.(int)$v;
                }
            }
            if (!empty($updateProductsPosition)) {
                $write->update($this->_categoryProductTable,
                    array('position'=>new Zend_Db_Expr('case product_id '.join(' ', $updateProductsPosition).' end')),
                    $write->quoteInto('product_id in (?)', array_keys($updateProductsPosition))
                    .' and '.$write->quoteInto('category_id=?', $catId)
                );
            }
        }

        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $category->setIsChangedProductList(true);
        }

        return $this;
    }

    protected function _updateCategoryPath($category, $path)
    {
        return $this;
        if ($category->getNotUpdateDepends()) {
            return $this;
        }
        foreach ($path as $pathItem) {
            if ($pathItem->getId()>1 && $category->getId() != $pathItem->getId()) {
                $category = Mage::getModel('catalog/category')
                    ->load($pathItem->getId())
                    ->save();
            }
        }
        return $this;
    }

    public function getStoreIds($category)
    {
        if (!$category->getId()) {
            return array();
        }

        $nodePath = $this->_getTree()
            ->getNodeById($category->getId())
                ->getPath();

        $nodes = array();
        foreach ($nodePath as $node) {
            $nodes[] = $node->getId();
        }

        $stores = array();
        $storeCollection = Mage::getModel('core/store')->getCollection()->loadByCategoryIds($nodes);
        foreach ($storeCollection as $store) {
            $stores[$store->getId()] = $store->getId();
        }

        $entityStoreId = $category->getStoreId();
        if (!in_array($entityStoreId, $stores)) {
            array_unshift($stores, $entityStoreId);
        }
        if (!in_array(0, $stores)) {
            array_unshift($stores, 0);
        }
        return $stores;
    }

    /**
     * Retrieve category product id's
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return  array
     */
    public function getProductsPosition($category)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryProductTable, array('product_id', 'position'))
            ->where('category_id=?', $category->getId());
        $positions = $this->_getWriteAdapter()->fetchPairs($select);
        return $positions;
    }
    
    public function getChildrenCount($categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'children_count')
            ->where('entity_id=?', $categoryId);

        $child = $this->_getReadAdapter()->fetchOne($select);
        
        return $child;
    }

   // public function move(Mage_Catalog_Model_Category $category, $newParentId)
    public function move($categoryId, $newParentId)
    {
        $category   = Mage::getModel('catalog/category')->load($categoryId);
        $oldParent  = $category->getParentCategory();
        $newParent  = Mage::getModel('catalog/category')->load($newParentId);
        
        $child = $this->getChildrenCount($category->getId());
        $child+=1;


        $toUpdateChild = explode('/',$newParent->getPath());
        $this->_getWriteAdapter()->update(
		        $this->getEntityTable(),
		        array('children_count'=>new Zend_Db_Expr('`children_count`+' . $child)),
		        $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $toUpdateChild))
      		  ;

      	$toUpdateChild = explode('/', $oldParent->getPath());
      	$this->_getWriteAdapter()->update(
		        $this->getEntityTable(),
		        array('children_count'=>new Zend_Db_Expr('`children_count`-' . $child)),
		        $this->_getWriteAdapter()->quoteInto('entity_id IN(?)', $toUpdateChild))
      		  ;
        return ;


        $oldStoreId = $category->getStoreId();
        $parent = Mage::getModel('catalog/category')
            ->setStoreId($category->getStoreId())
            ->load($category->getParentId());

        $newParent = Mage::getModel('catalog/category')
            ->setStoreId($category->getStoreId())
            ->load($newParentId);
        $oldParentStores = $parent->getStoreIds();
        $newParentStores = $newParent->getStoreIds();

        $category->setParentId($newParentId)
            ->save();
        $parent->save();
        $newParent->save();

        // Add to new stores
        $addToStores = array_diff($newParentStores, $oldParentStores);
        foreach ($addToStores as $storeId) {
            $newCategory = clone $category;
            $newCategory->setStoreId($storeId)
               ->save();
            $children = $category->getAllChildren();

            if ($children && $arrChildren = explode(',', $children)) {
                foreach ($arrChildren as $childId) {
                    if ($childId == $category->getId()) {
                        continue;
                    }

                    $child = Mage::getModel('catalog/category')
                       ->setStoreId($oldStoreId)
                       ->load($childId)
                       ->setStoreId($storeId)
                       ->save();
                }
            }
        }
        return $this;
    }

    public function checkId($id)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('entity_id=?', $id);
        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function verifyIds(array $ids)
    {
        $validIds = array();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('entity_id IN(?)', $ids);
        $query = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            $validIds[] = $row['entity_id'];
        }
        return $validIds;
    }

    public function getChildrenAmount($category, $isActiveFlag = true)
    {
        $storeId = Mage::app()->getStore()->getId();
        $attributeId = $this->_getIsActiveAttributeId();
        $table = Mage::getSingleton('core/resource')->getTableName('catalog/category') . '_int';

        $select = $this->_getReadAdapter()->select()
            ->from(array('m'=>$this->getEntityTable()), array('COUNT(m.entity_id)'))
            ->joinLeft(array('d'=>$table), "d.attribute_id = '{$attributeId}' AND d.store_id = 0 AND d.entity_id = m.entity_id", array())
            ->joinLeft(array('c'=>$table), "c.attribute_id = '{$attributeId}' AND c.store_id = '{$storeId}' AND c.entity_id = m.entity_id", array())
            ->where('m.path like ?', $category->getPath() . '/%')
            ->where('(IFNULL(c.value, d.value) = ?)', $isActiveFlag);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    protected function _getIsActiveAttributeId()
    {
        if (is_null($this->_isActiveAttributeId)) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('a'=>$this->getTable('eav/attribute')), array('attribute_id'))
                ->join(array('t'=>$this->getTable('eav/entity_type')), 'a.entity_type_id = t.entity_type_id')
                ->where('entity_type_code = ?', 'catalog_category')
                ->where('attribute_code = ?', 'is_active');

            $this->_isActiveAttributeId = $this->_getReadAdapter()->fetchOne($select);
        }
        return $this->_isActiveAttributeId;
    }

    public function refreshProductIndex($categoryIds = array())
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalog/category'))
            ->order('level')
            ->order('path');
        if (is_array($categoryIds) && !empty($categoryIds)) {
            $select->where('entity_id IN (?)', $categoryIds);
        }
        elseif (is_numeric($categoryIds)) {
            $select->where('entity_id=?', $categoryIds);
        }

        $categories = $this->_getWriteAdapter()->fetchAll($select);
        $indexTable = $this->getTable('catalog/category_product_index');
        foreach ($categories as $category) {
            $categoryId = $category['entity_id'];
            $this->_getWriteAdapter()->delete($indexTable, 'category_id='.$categoryId);

            $query = "INSERT INTO {$indexTable}
            SELECT $categoryId, product_id, position, $categoryId=category_id as is_parent
             FROM {$this->getTable('catalog/category_product')}
            WHERE category_id IN(
                SELECT entity_id FROM {$this->getTable('catalog/category')}
                WHERE path LIKE '{$category['path']}%'
            )
            GROUP BY product_id
            ORDER BY is_parent desc";

            $this->_getWriteAdapter()->query($query);
        }
        return $this;
    }

    public function findWhereAttributeIs($entityIdsFilter, $attribute, $expectedValue)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($attribute->getBackend()->getTable(), array('entity_id'))
            ->where('attribute_id = ?', $attribute->getId())
            ->where('value = ?', $expectedValue)
            ->where('entity_id in (?)', $entityIdsFilter);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getProductCount($category)
    {

        $productTable =Mage::getSingleton('core/resource')->getTableName('catalog/category_product');

            $select =  $this->getReadConnection()->select();
            $select->from(
                    array('main_table'=>$productTable),
                    array(new Zend_Db_Expr('COUNT(main_table.product_id)'))
                )
                ->where('main_table.category_id = ?', $category->getId())
                ->group('main_table.category_id');

            $counts =$this->getReadConnection()->fetchOne($select);

            return intval($counts);

    }
}
