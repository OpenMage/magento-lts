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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity/Attribute/Model - collection abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Collection_Abstract extends Varien_Data_Collection_Db
{
    protected $_itemsById = array();
    /**
     * Entity object to define collection's attributes
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    protected $_selectEntityTypes=array();

    /**
     * Attributes to be fetched for objects in collection
     *
     * @var array
     */
    protected $_selectAttributes=array();

    /**
     * Attributes to be filtered order sorted by
     *
     * @var array
     */
    protected $_filterAttributes=array();

    protected $_joinEntities = array();
    protected $_joinAttributes = array();
    protected $_joinFields = array();

    /**
     * Collection constructor
     *
     * @param Mage_Core_Model_Mysql4_Abstract $resource
     */
    public function __construct($resource=null)
    {
        parent::__construct();
        $this->_construct();
        $this->setConnection($this->getEntity()->getReadConnection());
        $this->_initSelect();
    }

    /**
     * Initialize collection
     */
    protected function _construct()
    {

    }

    public function getTable($table)
    {
        return $this->getResource()->getTable($table);
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('e'=>$this->getEntity()->getEntityTable()));
        if ($this->getEntity()->getTypeId()) {
            $this->addAttributeToFilter('entity_type_id', $this->getEntity()->getTypeId());
        }
        return $this;
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected function _init($model, $entityModel=null)
    {
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName($model));
        if (is_null($entityModel)) {
            $entityModel = $model;
        }
        $entity = Mage::getResourceModel($entityModel);
        $this->setEntity($entity);
        return $this;
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setEntity($entity)
    {
        if ($entity instanceof Mage_Eav_Model_Entity_Abstract) {
            $this->_entity = $entity;
        } elseif (is_string($entity) || $entity instanceof Mage_Core_Model_Config_Element) {
            $this->_entity = Mage::getModel('eav/entity')->setType($entity);
        } else {
            Mage::throwException(Mage::helper('eav')->__('Invalid entity supplied: %s', print_r($entity,1)));
        }
        return $this;
    }

    /**
     * Get collection's entity object
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        if (empty($this->_entity)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Entity is not initialized'));
        }
        return $this->_entity;
    }

    /**
     * Get resource instance
     *
     * @return Mage_Core_Model_Mysql4_Abstract
     */
    public function getResource()
    {
        return $this->getEntity();
    }

    /**
     * Set template object for the collection
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setObject($object=null)
    {
        if (is_object($object)) {
            $this->setItemObjectClass(get_class($object));
        }
        else {
            $this->setItemObjectClass($object);
        }

        return $this;
    }


    /**
     * Add an object to the collection
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addItem(Varien_Object $object)
    {
        if (get_class($object)!== $this->_itemObjectClass) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Attempt to add an invalid object'));
        }
        return parent::addItem($object);
    }

    /**
     * Retrieve entity attribute
     *
     * @param   string $attributeCode
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attributeCode)
    {
        if (isset($this->_joinAttributes[$attributeCode])) {
            return $this->_joinAttributes[$attributeCode]['attribute'];
        } else {
            return $this->getEntity()->getAttribute($attributeCode);
        }
    }

    /**
     * Add attribute filter to collection
     *
     * If $attribute is an array will add OR condition with following format:
     * array(
     *     array('attribute'=>'firstname', 'like'=>'test%'),
     *     array('attribute'=>'lastname', 'like'=>'test%'),
     * )
     *
     * @see self::_getConditionSql for $condition
     * @param Mage_Eav_Model_Entity_Attribute_Interface|integer|string|array $attribute
     * @param null|string|array $condition
     * @param string $operator
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToFilter($attribute, $condition=null, $joinType='inner')
    {
        if($attribute===null) {
            $this->getSelect();
            return $this;
        }

        if (is_numeric($attribute)) {
            $attribute = $this->getEntity()->getAttribute($attribute)->getAttributeCode();
        }
        elseif ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            $attribute = $attribute->getAttributeCode();
        }

        if (is_array($attribute)) {
            $sqlArr = array();
            foreach ($attribute as $condition) {
                $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
            }
            $conditionSql = '('.join(') OR (', $sqlArr).')';
        } elseif (is_string($attribute)) {
            if (is_null($condition)) {
                $condition = '';
            }
            $conditionSql = $this->_getAttributeConditionSql($attribute, $condition, $joinType);
        }

        if (!empty($conditionSql)) {
            $this->getSelect()->where($conditionSql);
        } else {
            Mage::throwException('Invalid attribute identifier for filter ('.get_class($attribute).')');
        }

        return $this;
    }

    /**
     * Wrapper for compatibility with Varien_Data_Collection_Db
     *
     * @param mixed $attribute
     * @param mixed $condition
     */
    public function addFieldToFilter($attribute, $condition=null){
        return $this->addAttributeToFilter($attribute, $condition);
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToSort($attribute, $dir='asc')
    {
        if (isset($this->_joinFields[$attribute])) {
            $this->getSelect()->order($this->_getAttributeFieldName($attribute).' '.$dir);
            return $this;
        }
        if (isset($this->_joinAttributes[$attribute])) {
            $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
            $entityField = $this->_getAttributeTableAlias($attribute).'.'.$attrInstance->getAttributeCode();
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            $entityField = 'e.'.$attribute;
        }
        if ($attrInstance) {
            if ($attrInstance->getBackend()->isStatic()) {
                $this->getSelect()->order($entityField.' '.$dir);
            } else {
                $this->_addAttributeJoin($attribute, 'left');
                if (isset($this->_joinAttributes[$attribute])) {
                    $this->getSelect()->order($attribute.' '.$dir);
                }
                else {
                    $this->getSelect()->order($this->_getAttributeTableAlias($attribute).'.value '.$dir);
                }
                //
            }
        }
        return $this;
    }

    /**
     * Add attribute to entities in collection
     *
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addAttributeToSelect($attribute, $joinType=false)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $a) {
                $this->addAttributeToSelect($a, $joinType);
            }
            return $this;
        }
        if ($joinType!==false && !$this->getEntity()->getAttribute($attribute)->getBackend()->isStatic()) {
            $this->_addAttributeJoin($attribute, $joinType);
        } elseif ('*'===$attribute) {
            $attributes = $this->getEntity()
                ->loadAllAttributes()
                ->getAttributesByCode();
            foreach ($attributes as $attrCode=>$attr) {
                $this->_selectAttributes[$attrCode] = $attr->getId();
            }
        } else {
            if (isset($this->_joinAttributes[$attribute])) {
                $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
            } else {
                $attrInstance = $this->getEntity()->getAttribute($attribute);
            }
            if (empty($attrInstance)) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute requested: %s', (string)$attribute));
            }
            $this->_selectAttributes[$attrInstance->getAttributeCode()] = $attrInstance->getId();
        }
        return $this;
    }

    public function addEntityTypeToSelect($entityType, $prefix)
    {
        $this->_selectEntityTypes[$entityType] = array(
            'prefix'=>$prefix,
        );
        return $this;
    }

    /**
     * Add attribute expression (SUM, COUNT, etc)
     *
     * Example: ('sub_total', 'SUM({{attribute}})', 'revenue')
     * Example: ('sub_total', 'SUM({{revenue}})', 'revenue')
     *
     * For some functions like SUM use groupByAttribute.
     *
     * @param string $alias
     * @param string $expression
     * @param string $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addExpressionAttributeToSelect($alias, $expression, $attribute)
    {
        // validate alias
        if (isset($this->_joinFields[$alias])) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Joined field or attribute expression with this alias is already declared'));
        }
        if(!is_array($attribute)) {
            $attribute = array($attribute);
        }

        $fullExpression = $expression;
        // Replacing multiple attributes
        foreach($attribute as $attributeItem) {
            $attributeInstance = $this->getAttribute($attributeItem);

            if ($attributeInstance->getBackend()->isStatic()) {
                $attrField = 'e.' . $attributeItem;
            } else {
                $this->_addAttributeJoin($attributeItem, 'left');
                $attrField = $this->_getAttributeFieldName($attributeItem);
            }

            $fullExpression = str_replace('{{attribute}}', $attrField, $fullExpression);
            $fullExpression = str_replace('{{' . $attributeItem . '}}', $attrField, $fullExpression);
        }

        $this->getSelect()->from(null, array($alias=>$fullExpression));

        $this->_joinFields[$alias] = array(
            'table' => false,
            'field' => $fullExpression
        );

        return $this;
    }


    /**
     * Groups results by specified attribute
     *
     * @param string|array $attribute
     */
    public function groupByAttribute($attribute)
    {
        if(is_array($attribute)) {
            foreach ($attribute as $attributeItem) {
                $this->groupByAttribute($attributeItem);
            }
        } else {
            if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->group($this->_getAttributeFieldName($attribute));
                return $this;
            }

            if (isset($this->_joinAttributes[$attribute])) {
                $attrInstance = $this->_joinAttributes[$attribute]['attribute'];
                $entityField = $this->_getAttributeTableAlias($attribute).'.'.$attrInstance->getAttributeCode();
            } else {
                $attrInstance = $this->getEntity()->getAttribute($attribute);
                $entityField = 'e.'.$attribute;
            }

            if ($attrInstance->getBackend()->isStatic()) {
                $this->getSelect()->group($entityField);
            } else {
                $this->_addAttributeJoin($attribute);
                $this->getSelect()->group($this->_getAttributeTableAlias($attribute).'.value');
            }
        }

        return $this;
    }

    /**
     * Add attribute from joined entity to select
     *
     * Examples:
     * ('billing_firstname', 'customer_address/firstname', 'default_billing')
     * ('billing_lastname', 'customer_address/lastname', 'default_billing')
     * ('shipping_lastname', 'customer_address/lastname', 'default_billing')
     * ('shipping_postalcode', 'customer_address/postalcode', 'default_shipping')
     * ('shipping_city', $cityAttribute, 'default_shipping')
     *
     * Developer is encouraged to use existing instances of attributes and entities
     * After first use of string entity name it will be cached in the collection
     *
     * @todo connect between joined attributes of same entity
     * @param string $alias alias for the joined attribute
     * @param string|Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $bind attribute of the main entity to link with joined $filter
     * @param string $filter primary key for the joined entity (entity_id default)
     * @param string $joinType inner|left
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinAttribute($alias, $attribute, $bind, $filter=null, $joinType='inner', $storeId=null)
    {
        // validate alias
        if (isset($this->_joinAttributes[$alias])) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid alias, already exists in joined attributes'));
        }

        // validate bind attribute
        if (is_string($bind)) {
            $bindAttribute = $this->getAttribute($bind);
        }

        if (!$bindAttribute || (!$bindAttribute->getBackend()->isStatic() && !$bindAttribute->getId())) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid foreign key'));
        }

        // try to explode combined entity/attribute if supplied
        if (is_string($attribute)) {
            $attrArr = explode('/', $attribute);
            if (isset($attrArr[1])) {
                $entity = $attrArr[0];
                $attribute = $attrArr[1];
            }
        }

        // validate entity
        if (empty($entity) && $attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $entity = $attribute->getEntity();
        } elseif (is_string($entity)) {
            // retrieve cached entity if possible
            if (isset($this->_joinEntities[$entity])) {
                $entity = $this->_joinEntities[$entity];
            } else {
                $entity = Mage::getModel('eav/entity')->setType($attrArr[0]);
            }
        }
        if (!$entity || !$entity->getTypeId()) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity type'));
        }

        // cache entity
        if (!isset($this->_joinEntities[$entity->getType()])) {
            $this->_joinEntities[$entity->getType()] = $entity;
        }

        // validate attribute
        if (is_string($attribute)) {
            $attribute = $entity->getAttribute($attribute);
        }
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute type'));
        }

        if (empty($filter)) {
            $filter = $entity->getEntityIdField();
        }

        // add joined attribute
        $this->_joinAttributes[$alias] = array(
            'bind'          => $bind,
            'bindAttribute' => $bindAttribute,
            'attribute'     => $attribute,
            'filter'        => $filter,
            'store_id'      => $storeId,
        );

        $this->_addAttributeJoin($alias, $joinType);

        return $this;
    }

    /**
     * Join regular table field and use an attribute as fk
     *
     * Examples:
     * ('country_name', 'directory/country_name', 'name', 'country_id=shipping_country', "{{table}}.language_code='en'", 'left')
     *
     * @param string $alias 'country_name'
     * @param string $table 'directory/country_name'
     * @param string $field 'name'
     * @param string $bind 'PK(country_id)=FK(shipping_country_id)'
     * @param string|array $cond "{{table}}.language_code='en'" OR array('language_code'=>'en')
     * @param string $joinType 'left'
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinField($alias, $table, $field, $bind, $cond=null, $joinType='inner')
    {
        // validate alias
        if (isset($this->_joinFields[$alias])) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Joined field with this alias is already declared'));
        }

        // validate table
        if (strpos($table, '/')!==false) {
            $table = Mage::getSingleton('core/resource')->getTableName($table);
        }
        $tableAlias = $this->_getAttributeTableAlias($alias);

        // validate bind
        list($pk, $fk) = explode('=', $bind);
        $bindCond = $tableAlias.'.'.$pk.'='.$this->_getAttributeFieldName($fk);

        // process join type
        switch ($joinType) {
            case 'left':
                $joinMethod = 'joinLeft';
                break;

            default:
                $joinMethod = 'join';
        }
        $condArr = array($bindCond);

        // add where condition if needed
        if (!is_null($cond)) {
            if (is_array($cond)) {
                foreach ($cond as $k=>$v) {
                    $condArr[] = $this->_getConditionSql($tableAlias.'.'.$k, $v);
                }
            } else {
                $condArr[] = str_replace('{{table}}', $tableAlias, $cond);
            }
        }
        $cond = '('.join(') AND (', $condArr).')';

        // join table
        $this->getSelect()->$joinMethod(array($tableAlias=>$table), $cond, array($alias=>$field));

        // save joined attribute
        $this->_joinFields[$alias] = array(
            'table'=>$tableAlias,
            'field'=>$field,
        );

        return $this;
    }

    /**
     * Join a table
     *
     * @param string $table
     * @param string $bind
     * @param string|array $fields
     * @param null|array $cond
     * @param string $joinType
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function joinTable($table, $bind, $fields=null, $cond=null, $joinType='inner')
    {
        // validate table
        if (strpos($table, '/')!==false) {
            $table = Mage::getSingleton('core/resource')->getTableName($table);
        }
        $tableAlias = $table;

        // validate fields and aliases
        if (!$fields) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid joined fields'));
        }
        foreach ($fields as $alias=>$field) {
            if (isset($this->_joinFields[$alias])) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Joined field with this alias (%s) is already declared', $alias));
            }
            $this->_joinFields[$alias] = array(
                'table'=>$tableAlias,
                'field'=>$field,
            );
        }

        // validate bind
        list($pk, $fk) = explode('=', $bind);
        $bindCond = $tableAlias.'.'.$pk.'='.$this->_getAttributeFieldName($fk);

        // process join type
        switch ($joinType) {
            case 'left':
                $joinMethod = 'joinLeft';
                break;

            default:
                $joinMethod = 'join';
        }
        $condArr = array($bindCond);

        // add where condition if needed
        if (!is_null($cond)) {
            if (is_array($cond)) {
                foreach ($cond as $k=>$v) {
                    $condArr[] = $this->_getConditionSql($tableAlias.'.'.$k, $v);
                }
            } else {
                $condArr[] = str_replace('{{table}}', $tableAlias, $cond);
            }
        }
        $cond = '('.join(') AND (', $condArr).')';

// join table
        $this->getSelect()->$joinMethod(array($tableAlias=>$table), $cond, $fields);

        return $this;
    }

    /**
     * Remove an attribute from selection list
     *
     * @param string $attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function removeAttributeToSelect($attribute=null)
    {
        if (is_null($attribute)) {
            $this->_selectAttributes = array();
        } else {
            unset($this->_selectAttributes[$attribute]);
        }
        return $this;
    }

    /**
     * Set collection page start and records to show
     *
     * @param integer $pageNum
     * @param integer $pageSize
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }

    /**
     * Load collection data into object items
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_beforeLoad();

        $this->_loadEntities($printQuery, $logQuery);
        $this->_loadAttributes($printQuery, $logQuery);

        foreach ($this->_items as $item) {
            $item->setOrigData();
        }

        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, 'e.'.$this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retrive all ids sql
     *
     * @return array
     */
    public function getAllIdsSql()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::GROUP);
        $idsSelect->from(null, 'e.'.$this->getEntity()->getIdFieldName());
        return $idsSelect;
    }

    /**
     * Save all the entities in the collection
     *
     * @todo make batch save directly from collection
     */
    public function save()
    {
        foreach ($this->getItems() as $item) {
            $item->save();
        }
        return $this;
    }


    /**
     * Delete all the entities in the collection
     *
     * @todo make batch delete directly from collection
     */
    public function delete()
    {
        foreach ($this->getItems() as $k=>$item) {
            $this->getEntity()->delete($item);
            unset($this->_items[$k]);
        }
        return $this;
    }

    /**
     * Import 2D array into collection as objects
     *
     * If the imported items already exist, update the data for existing objects
     *
     * @param array $arr
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function importFromArray($arr)
    {
        $entityIdField = $this->getEntity()->getEntityIdField();
        foreach ($arr as $row) {
            $entityId = $row[$entityIdField];
            if (!isset($this->_items[$entityId])) {
                $this->_items[$entityId] = $this->getNewEmptyItem();
                $this->_items[$entityId]->setData($row);
            }  else {
                $this->_items[$entityId]->addData($row);
            }
        }
        return $this;
    }

    /**
     * Get collection data as a 2D array
     *
     * @return array
     */
    public function exportToArray()
    {
        $result = array();
        $entityIdField = $this->getEntity()->getEntityIdField();
        foreach ($this->getItems() as $item) {
            $result[$item->getData($entityIdField)] = $item->getData();
        }
        return $result;
    }


    public function getRowIdFieldName()
    {
        if (is_null($this->_idFieldName)) {
            $this->_setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $this->getIdFieldName();
    }

    public function setRowIdFieldName($fieldName)
    {
        return $this->_setIdFieldName($fieldName);
    }

    /**
     * Load entities records into items
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        $entity = $this->getEntity();
        $entityIdField = $entity->getEntityIdField();

        if ($this->_pageSize) {
            $this->getSelect()->limitPage($this->getCurPage(), $this->_pageSize);
        }

        $this->printLogQuery($printQuery, $logQuery);

        try {
            $rows = $this->_fetchAll($this->getSelect());
        } catch (Exception $e) {
            Mage::printException($e, $this->getSelect());
            $this->printLogQuery(true, true, $this->getSelect());
            throw $e;
        }

        foreach ($rows as $v) {
            $object = $this->getNewEmptyItem()
                ->setData($v);
            $this->addItem($object);
            if (isset($this->_itemsById[$object->getId()])) {
                $this->_itemsById[$object->getId()][] = $object;
            }
            else {
                $this->_itemsById[$object->getId()] = array($object);
            }
        }
        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items) || empty($this->_itemsById) || empty($this->_selectAttributes)) {
            return $this;
        }

        $entity = $this->getEntity();
        $entityIdField = $entity->getEntityIdField();

        foreach ($entity->getAttributesByTable() as $table=>$attributes) {
            $select = $this->_getLoadAttributesSelect($table);
            try {
                $values = $this->_fetchAll($select);
            } catch (Exception $e) {
                Mage::printException($e, $select);
                $this->printLogQuery(true, true, $select);
                throw $e;
            }

            foreach ($values as $value) {
                $this->_setItemAttributeValue($value);
            }
        }

        return $this;
    }

    /**
     * Retrieve attributes load select
     *
     * @param   string $table
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table)
    {
        $entityIdField = $this->getEntity()->getEntityIdField();
        $select = $this->getConnection()->select()
            ->from($table, array($entityIdField, 'attribute_id', 'value'))
            ->where('entity_type_id=?', $this->getEntity()->getTypeId())
            ->where("$entityIdField in (?)", array_keys($this->_itemsById))
            ->where('attribute_id in (?)', $this->_selectAttributes);
        return $select;
    }

    /**
     * Initialize entity ubject property value
     *
     * $valueInfo is _getLoadAttributesSelect fetch result row
     *
     * @param   array $valueInfo
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _setItemAttributeValue($valueInfo)
    {
        $entityIdField  = $this->getEntity()->getEntityIdField();
        $entityId       = $valueInfo[$entityIdField];
        if (!isset($this->_itemsById[$entityId])) {
            Mage::throwException('Mage_Eav',
                Mage::helper('eav')->__('Data integrity: No header row found for attribute')
            );
        }
        $attributeCode = $this->getEntity()->getAttribute($valueInfo['attribute_id'])
            ->getAttributeCode();
        foreach ($this->_itemsById[$entityId] as $object) {
            $object->setData($attributeCode, $valueInfo['value']);
        }
        return $this;
    }

    /**
     * Get alias for attribute value table
     *
     * @param string $attributeCode
     * @return string
     */
    protected function _getAttributeTableAlias($attributeCode)
    {
        return '_table_'.$attributeCode;
    }

    protected function _getAttributeFieldName($attributeCode)
    {
        if (isset($this->_joinAttributes[$attributeCode]['condition_alias'])) {
            return $this->_joinAttributes[$attributeCode]['condition_alias'];
        }
        if (isset($this->_joinFields[$attributeCode])) {
            $attr = $this->_joinFields[$attributeCode];
            return $attr['table'] ? $attr['table'] .'.'.$attr['field'] : $attr['field'];
        }

        $attribute = $this->getAttribute($attributeCode);
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute name: %s', $attributeCode));
        }

        if ($attribute->getBackend()->isStatic()) {
            if (isset($this->_joinAttributes[$attributeCode])) {
                $fieldName = $this->_getAttributeTableAlias($attributeCode).'.'.$attributeCode;
            } else {
                $fieldName = 'e.'.$attributeCode;
            }
        } else {
            $fieldName = $this->_getAttributeTableAlias($attributeCode).'.value';
        }
        return $fieldName;
    }

    /**
     * Add attribute value table to the join if it wasn't added previously
     *
     * @param   string $attributeCode
     * @param   string $joinType inner|left
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _addAttributeJoin($attributeCode, $joinType='inner')
    {
        if (!empty($this->_filterAttributes[$attributeCode])) {
            return $this;
        }

        $attrTable = $this->_getAttributeTableAlias($attributeCode);
        if (isset($this->_joinAttributes[$attributeCode])) {
            $attribute      = $this->_joinAttributes[$attributeCode]['attribute'];
            $entity         = $attribute->getEntity();
            $entityIdField  = $entity->getEntityIdField();
            $fkName         = $this->_joinAttributes[$attributeCode]['bind'];
            $fkAttribute    = $this->_joinAttributes[$attributeCode]['bindAttribute'];
            $fkTable        = $this->_getAttributeTableAlias($fkName);

            if ($fkAttribute->getBackend()->isStatic()) {
                if (isset($this->_joinAttributes[$fkName])) {
                    $fk = $fkTable.".".$fkAttribute->getAttributeCode();
                } else {
                    $fk = "e.".$fkAttribute->getAttributeCode();
                }
            } else {
                $this->_addAttributeJoin($fkAttribute->getAttributeCode(), $joinType);
                $fk = "$fkTable.value";
            }
            $pk = $attrTable.'.'.$this->_joinAttributes[$attributeCode]['filter'];
        } else {
            $entity         = $this->getEntity();
            $entityIdField  = $entity->getEntityIdField();
            $attribute      = $entity->getAttribute($attributeCode);
            $fk             = "e.$entityIdField";
            $pk             = "$attrTable.$entityIdField";
        }

        if (!$attribute) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid attribute name: %s', $attributeCode));
        }

        if ($attribute->getBackend()->isStatic()) {
            $attrFieldName = "$attrTable.".$attribute->getAttributeCode();
        } else {
            $attrFieldName = "$attrTable.value";
        }

        $condArr = array("$pk = $fk");
        if (!$attribute->getBackend()->isStatic()) {
            $condArr[] = $this->getConnection()->quoteInto("$attrTable.attribute_id=?", $attribute->getId());
        }

        /**
         * process join type
         */
        $joinMethod = ($joinType == 'left') ? 'joinLeft' : 'join';

        $this->_joinAttributeToSelect($joinMethod, $attribute, $attrTable, $condArr, $attributeCode, $attrFieldName);

        $this->removeAttributeToSelect($attributeCode);
        $this->_filterAttributes[$attributeCode] = $attribute->getId();

        /**
         * Fix double join for using same as filter
         */
        $this->_joinFields[$attributeCode] = array(
            'table' => '',
            'field' => $attrFieldName,
        );

        return $this;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param   string $method
     * @param   object $attribute
     * @param   string $tableAlias
     * @param   array $condition
     * @param   string $fieldCode
     * @param   string $fieldAlias
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        $this->getSelect()->$method(
            array($tableAlias => $attribute->getBackend()->getTable()),
            '('.join(') AND (', $condition).')',
            array($fieldCode=>$fieldAlias)
        );
        return $this;
    }

    /**
     * Get condition sql for the attribute
     *
     * @see self::_getConditionSql
     * @param string $attribute
     * @param mixed $condition
     * @return string
     */
    protected function _getAttributeConditionSql($attribute, $condition, $joinType='inner')
    {
        if (isset($this->_joinFields[$attribute])) {
            return $this->_getConditionSql($this->_getAttributeFieldName($attribute), $condition);
        }
        // process linked attribute
        if (isset($this->_joinAttributes[$attribute])) {
            $entity = $this->getAttribute($attribute)->getEntity();
            $entityTable = $entity->getEntityTable();
        } else {
            $entity = $this->getEntity();
            $entityTable = 'e';
        }

        if ($entity->isAttributeStatic($attribute)) {
            $conditionSql = $this->_getConditionSql('e.'.$attribute, $condition);
        } else {
            $this->_addAttributeJoin($attribute, $joinType);
            if (isset($this->_joinAttributes[$attribute]['condition_alias'])) {
                $field = $this->_joinAttributes[$attribute]['condition_alias'];
            }
            else {
                $field = $this->_getAttributeTableAlias($attribute).'.value';
            }
            $conditionSql = $this->_getConditionSql($field, $condition);
        }
        return $conditionSql;
    }

    /**
     * Set sorting order
     *
     * $attribute can also be an array of attributes
     *
     * @param string|array $attribute
     * @param string $dir
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function setOrder($attribute, $dir='desc')
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attr) {
                $this->addAttributeToSort($attr, $dir);
            }
        } else {
            $this->addAttributeToSort($attribute, $dir);
        }
        return $this;
    }


    public function toArray($arrAttributes = array())
    {
        $arr = array();
        foreach ($this->_items as $k=>$item) {
            $arr[$k] = $item->toArray($arrAttributes);
        }
        return $arr;
    }

    protected function _beforeLoad()
    {
        return $this;
    }

    protected function _afterLoad()
    {
        return $this;
    }

    /**
     * Reset collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _reset()
    {
        parent::_reset();

        $this->_selectEntityTypes = array();
        $this->_selectAttributes = array();
        $this->_filterAttributes = array();
        $this->_joinEntities = array();
        $this->_joinAttributes = array();
        $this->_joinFields = array();

        return $this;
    }

    /**
     * Returns already loaded element ids
     *
     * return array
     */
    public function getLoadedIds()
    {
        return array_keys($this->_items);
    }
}
