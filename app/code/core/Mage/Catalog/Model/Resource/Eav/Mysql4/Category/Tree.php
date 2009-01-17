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
 * Category tree model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree extends Varien_Data_Tree_Dbp
{

    /**
     * Categories resource collection
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    protected $_collection;

    /**
     * Id of 'is_active' category attribute
     *
     * @var int
     */
    protected $_isActiveAttributeId = null;

    protected $_joinUrlRewriteIntoCollection = false;

    /**
     * Enter description here...
     *
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');

        parent::__construct(
            $resource->getConnection('catalog_read'),
            $resource->getTableName('catalog/category'),
            array(
                Varien_Data_Tree_Dbp::ID_FIELD       => 'entity_id',
                Varien_Data_Tree_Dbp::PATH_FIELD     => 'path',
                Varien_Data_Tree_Dbp::ORDER_FIELD    => 'position',
                Varien_Data_Tree_Dbp::LEVEL_FIELD    => 'level',
            )
        );
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection $collection
     * @param boolean $sorted
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    public function addCollectionData($collection=null, $sorted=false, $exclude=array(), $toLoad=true, $onlyActive = false)
    {
        if (is_null($collection)) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }

        if (!is_array($exclude)) {
            $exclude = array($exclude);
        }

        $collection->initCache(
            Mage::app()->getCache(),
            'tree',
            array(Mage_Catalog_Model_Category::CACHE_TAG)
        );

        $nodeIds = array();
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', array('nin'=>$disabledIds));
            }
            $collection->addAttributeToFilter('is_active', 1);
        }

        if ($this->_joinUrlRewriteIntoCollection) {
            $collection->joinUrlRewrite();
            $this->_joinUrlRewriteIntoCollection = false;
        }

        if($toLoad) {
            $collection->load();

            foreach ($collection as $category) {
                if ($this->getNodeById($category->getId())) {
                    $this->getNodeById($category->getId())->addData($category->getData());
                }
            }
        }

        return $this;
    }

    protected function _getDisabledIds($collection)
    {
        $storeId = Mage::app()->getStore()->getId();
        $this->_inactiveItems = $this->_getInactiveItemIds($collection, $storeId);

        $allIds = $collection->getAllIds();
        $disabledIds = array();

        foreach ($allIds as $id) {
            $parents = $this->getNodeById($id)->getPath();
            foreach ($parents as $parent) {
                if (!$this->_getItemIsActive($parent->getId(), $storeId)){
                    $disabledIds[] = $id;
                    continue;
                }
            }
        }
        return $disabledIds;
    }

    protected function _getIsActiveAttributeId()
    {
        if (is_null($this->_isActiveAttributeId)) {
            $select = $this->_conn->select()
                ->from(array('a'=>Mage::getSingleton('core/resource')->getTableName('eav/attribute')), array('attribute_id'))
                ->join(array('t'=>Mage::getSingleton('core/resource')->getTableName('eav/entity_type')), 'a.entity_type_id = t.entity_type_id')
                ->where('entity_type_code = ?', 'catalog_category')
                ->where('attribute_code = ?', 'is_active');

            $this->_isActiveAttributeId = $this->_conn->fetchOne($select);
        }
        return $this->_isActiveAttributeId;
    }

    protected function _getInactiveItemIds($collection, $storeId)
    {
        $filter = $collection->getAllIdsSql();
        $attributeId = $this->_getIsActiveAttributeId();

        $table = Mage::getSingleton('core/resource')->getTableName('catalog/category') . '_int';
        $select = $this->_conn->select()
            ->from(array('d'=>$table), array('d.entity_id'))
            ->where('d.attribute_id = ?', $attributeId)
            ->where('d.store_id = ?', 0)
            ->where('d.entity_id IN (?)', new Zend_Db_Expr($filter))
            ->joinLeft(array('c'=>$table), "c.attribute_id = '{$attributeId}' AND c.store_id = '{$storeId}' AND c.entity_id = d.entity_id", array())
            ->where('IFNULL(c.value, d.value) = ?', 0);

        return $this->_conn->fetchCol($select);
    }

    protected function _getItemIsActive($id)
    {
        if (!in_array($id, $this->_inactiveItems)) {
            return true;
        }
        return false;
    }


    /**
     * Get categories collection
     *
     * @param boolean $sorted
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCollection($sorted=false)
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_getDefaultCollection($sorted);
        }
        return $this->_collection;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection $collection
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    public function setCollection($collection)
    {
        if (!is_null($this->_collection)) {
            destruct($this->_collection);
        }
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param boolean $sorted
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    protected function _getDefaultCollection($sorted=false)
    {
        $this->_joinUrlRewriteIntoCollection = true;
        $collection = Mage::getModel('catalog/category')->getCollection();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('is_active');

        if ($sorted) {
            if (is_string($sorted)) {
                // $sorted is supposed to be attribute name
                $collection->addAttributeToSort($sorted);
            } else {
                $collection->addAttributeToSort('name');
            }
        }

        return $collection;
     }

    /**
     * Executing parents move method and cleaning cache after it
     *
     */
    public function move($category, $newParent, $prevNode = null) {
        Mage::getResourceSingleton('catalog/category')->move($category->getId(), $newParent->getId());
        parent::move($category, $newParent, $prevNode);
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            array(Mage_Catalog_Model_Category::CACHE_TAG));
    }

    /**
     * Load whole category tree, that will include specified categories ids.
     *
     * @param array $ids
     * @param bool $addCollectionData
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    public function loadByIds($ids, $addCollectionData = true)
    {
        // load first two levels, if no ids specified
        if (empty($ids)) {
            $select = $this->_conn->select()
                ->from($this->_table, 'entity_id')
                ->where('`level` <= 2');
            $ids = $this->_conn->fetchCol($select);
        }
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $key => $id) {
            $ids[$key] = (int)$id;
        }

        // collect paths of specified IDs and prepare to collect all their parents and neighbours
        $select = $this->_conn->select()
            ->from($this->_table, array('path', 'level'))
            ->where(sprintf('entity_id IN (%s)', implode(', ', $ids)));
        $where = array('`level`=0' => true);
        foreach ($this->_conn->fetchAll($select) as $item) {
            $path  = explode('/', $item['path']);
            $level = (int)$item['level'];
            while ($level > 0)
            {
                $path[count($path) - 1] = '%';
                $where[sprintf("`level`=%d AND `path` LIKE '%s'", $level, implode('/', $path))] = true;
                array_pop($path);
                $level--;
            }
        }
        $where = array_keys($where);

        // get all required records
        if ($addCollectionData) {
            $select = $this->_getDefaultCollection($this->_orderField)
                ->getSelect();
            // add attributes to select
            foreach (array('name', 'url_key', 'is_active') as $attributeCode) {
                $attribute = Mage::getResourceSingleton('catalog/category')->getAttribute($attributeCode);
                $tableAs   = "_$attributeCode";
                $select->joinLeft(
                    array($tableAs => $attribute->getBackend()->getTable()),
                    sprintf('`%1$s`.entity_id=e.entity_id AND `%1$s`.attribute_id=%2$d AND `%1$s`.entity_type_id=e.entity_type_id',
                        $tableAs, $attribute->getData('attribute_id')
                    ),
                    array($attributeCode => 'value')
                );
            }
            // count products qty
            $select->joinLeft(array('_category_product' => Mage::getSingleton('core/resource')->getTableName('catalog/category_product')),
                'e.entity_id=_category_product.category_id',
                array('product_count' => 'COUNT(_category_product.product_id)')
            )
            ->group('e.entity_id');
        }
        else {
            $select = clone $this->_select;
            $select->order($this->_orderField . ' ASC');
        }
        $select->where(implode(' OR ', $where));

        // get array of records and add them as nodes to the tree
        $arrNodes = $this->_conn->fetchAll($select);
        if (!$arrNodes) {
            return false;
        }
        $childrenItems = array();
        foreach ($arrNodes as $nodeInfo) {
            $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
            array_pop($pathToParent);
            $pathToParent = implode('/', $pathToParent);
            $childrenItems[$pathToParent][] = $nodeInfo;
        }
        $this->addChildNodes($childrenItems, '', null);

        return $this;
    }

}
