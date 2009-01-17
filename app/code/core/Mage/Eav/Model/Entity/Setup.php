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


class Mage_Eav_Model_Entity_Setup extends Mage_Core_Model_Resource_Setup
{
    protected $_generalGroupName = 'General';

    public $defaultGroupIdAssociations = array('General'=>1);

    public function cleanCache()
    {
        Mage::app()->cleanCache(array('eav'));
        return $this;
    }

    public function installDefaultGroupIds()
    {
        $setIds = $this->getAllAttributeSetIds();
        foreach ($this->defaultGroupIdAssociations as $defaultGroupName=>$defaultGroupId) {
            foreach ($setIds as $set) {
                $groupId = $this->getTableRow('eav/attribute_group',
                    'attribute_group_name', $defaultGroupName, 'attribute_group_id', 'attribute_set_id', $set
                );
                if (!$groupId) {
                    $groupId = $this->getTableRow('eav/attribute_group',
                        'attribute_set_id', $set, 'attribute_group_id'
                    );
                }
                $this->updateTableRow('eav/attribute_group',
                    'attribute_group_id', $groupId,
                    'default_id', $defaultGroupId
                );
            }
        }
    }


/******************* ENTITY TYPES *****************/

    /**
     * Add an entity type
     *
     * If already exists updates the entity type with params data
     *
     * @param string $code
     * @param array $params
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function addEntityType($code, array $params)
    {
        $data = array(
            'entity_type_code'      => $code,
            'entity_model'          => $params['entity_model'],
            'attribute_model'       => isset($params['attribute_model']) ? $params['attribute_model'] : '',
            'entity_table'          => isset($params['table']) ? $params['table'] : 'eav/entity',
            'increment_model'       => isset($params['increment_model']) ? $params['increment_model'] : '',
            'increment_per_store'   => isset($params['increment_per_store']) ? $params['increment_per_store'] : 0,
        );

        if ($this->getEntityType($code, 'entity_type_id')) {
            $this->updateEntityType($code, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/entity_type'), $data);
        }

        $this->addAttributeSet($code, 'Default');
        $this->addAttributeGroup($code, 'Default', $this->_generalGroupName);

        return $this;
    }

    /**
     * Update entity row
     *
     * @param unknown_type $code
     * @param unknown_type $field
     * @param unknown_type $value
     * @return unknown
     */
    public function updateEntityType($code, $field, $value=null)
    {
        $this->updateTableRow('eav/entity_type',
            'entity_type_id', $this->getEntityTypeId($code),
            $field, $value
        );
        return $this;
    }

    public function getEntityType($id, $field=null)
    {
        return $this->getTableRow('eav/entity_type',
            is_numeric($id) ? 'entity_type_id' : 'entity_type_code', $id,
            $field
        );
    }

    public function getEntityTypeId($entityTypeId)
    {
        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId, 'entity_type_id');
        }
        if (!is_numeric($entityTypeId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong entity ID'));
        }
        return $entityTypeId;
    }

    public function removeEntityType($id)
    {
        if (is_numeric($id)) {
            $this->_conn->delete($this->getTable('eav/entity_type'),
                $this->_conn->quoteInto('entity_type_id=?', $id)
            );
        }
        else {
            $this->_conn->delete($this->getTable('eav/entity_type'),
                $this->_conn->quoteInto('entity_type_code=?', (string)$id)
            );
        }
        return $this;
    }

/******************* ATTRIBUTE SETS *****************/

    public function getAttributeSetSortOrder($entityTypeId, $sortOrder=null)
    {
        if (!is_numeric($sortOrder)) {
            $sortOrder = $this->_conn->fetchOne("select max(sort_order)
                from ".$this->getTable('eav/attribute_set')."
                where entity_type_id=".$this->getEntityTypeId($entityTypeId)
            );
            $sortOrder++;
        }
        return $sortOrder;
    }

    public function addAttributeSet($entityTypeId, $name, $sortOrder=null)
    {
        $data = array(
            'entity_type_id'=>$this->getEntityTypeId($entityTypeId),
            'attribute_set_name'=>$name,
            'sort_order'=>$this->getAttributeSetSortOrder($entityTypeId, $sortOrder),
        );

        if ($id = $this->getAttributeSet($entityTypeId, $name, 'attribute_set_id')) {
            $this->updateAttributeSet($entityTypeId, $id, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute_set'), $data);

            $this->addAttributeGroup($entityTypeId, $name, $this->_generalGroupName);
        }

        return $this;
    }

    public function updateAttributeSet($entityTypeId, $id, $field, $value=null)
    {
        $this->updateTableRow('eav/attribute_set',
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $id),
            $field, $value,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
        return $this;
    }

    public function getAttributeSet($entityTypeId, $id, $field=null)
    {
        return $this->getTableRow('eav/attribute_set',
            is_numeric($id) ? 'attribute_set_id' : 'attribute_set_name', $id,
            $field,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
    }

    public function getAttributeSetId($entityTypeId, $setId)
    {
        if (!is_numeric($setId)) {
            $setId = $this->getAttributeSet($entityTypeId, $setId, 'attribute_set_id');
        }
        if (!is_numeric($setId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong attribute set ID'));
        }
        return $setId;
    }

    public function removeAttributeSet($entityTypeId, $id)
    {
        $this->_conn->delete($this->getTable('eav/attribute_set'),
            $this->_conn->quoteInto('attribute_set_id=?', $this->getAttributeSetId($entityTypeId, $id))
        );
        return $this;
    }

    public function setDefaultSetToEntityType($entityType)
    {
        $entityTypeId = $this->getEntityTypeId($entityType);
        $setId = $this->getAttributeSetId($entityTypeId, 'Default');
        $this->updateEntityType($entityTypeId, 'default_attribute_set_id', $setId);
        return $this;
    }

/******************* ATTRIBUTE GROUPS *****************/

    public function getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder=null)
    {
        if (!is_numeric($sortOrder)) {
            $sortOrder = $this->_conn->fetchOne("select max(sort_order)
                from ".$this->getTable('eav/attribute_group')."
                where attribute_set_id=".$this->getAttributeSetId($entityTypeId, $setId)
            );
            $sortOrder++;
        }
        return $sortOrder;
    }

    public function addAttributeGroup($entityTypeId, $setId, $name, $sortOrder=null)
    {
        $setId = $this->getAttributeSetId($entityTypeId, $setId);
        $data = array(
            'attribute_set_id'=>$setId,
            'attribute_group_name'=>$name,
        );
        if (isset($this->defaultGroupIdAssociations[$name])) {
            $data['default_id'] = $this->defaultGroupIdAssociations[$name];
        }
        if (!is_null($sortOrder)) {
            $data['sort_order'] = $sortOrder;
        }

        if ($id = $this->getAttributeGroup($entityTypeId, $setId, $name, 'attribute_group_id')) {
            $this->updateAttributeGroup($entityTypeId, $setId, $id, $data);
        } else {
            if (is_null($sortOrder)) {
                $data['sort_order'] = $this->getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder);
            }
            $this->_conn->insert($this->getTable('eav/attribute_group'), $data);
        }

        return $this;
    }

    public function updateAttributeGroup($entityTypeId, $setId, $id, $field, $value=null)
    {
        $this->updateTableRow('eav/attribute_group',
            'attribute_group_id', $this->getAttributeGroupId($entityTypeId, $setId, $id),
            $field, $value,
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $setId)
        );
        return $this;
    }

    public function getAttributeGroup($entityTypeId, $setId, $id, $field=null)
    {
        $searchId = $id;
        if (is_numeric($id)) {
            $searchField = 'attribute_group_id';
        } else {
            if (isset($this->defaultGroupIdAssociations[$id])) {
                $searchField = 'default_id';
                $searchId = $this->defaultGroupIdAssociations[$id];
            } else {
                $searchField = 'attribute_group_name';
            }
        }

        return $this->getTableRow('eav/attribute_group',
            $searchField, $searchId, $field,
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $setId)
        );
    }

    public function getAttributeGroupId($entityTypeId, $setId, $groupId)
    {
        if (!is_numeric($groupId)) {
            $groupId = $this->getAttributeGroup($entityTypeId, $setId, $groupId, 'attribute_group_id');
        }
        if (!is_numeric($groupId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong attribute group ID'));
        }
        return $groupId;
    }

    public function removeAttributeGroup($entityTypeId, $setId, $id)
    {
        $this->_conn->delete($this->getTable('eav/attribute_group'),
            $this->_conn->quoteInto('attribute_group_id=?', $this->getAttributeGroupId($entityTypeId, $setId, $id))
        );
        return $this;
    }

/******************* ATTRIBUTES *****************/

    /**
     * Add attribute to an entity type
     *
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return Mage_Eav_Model_Entity_Setup
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $data = array(
            'entity_type_id'        => $entityTypeId,
            'attribute_code'        => $code,
            'backend_model'         => isset($attr['backend']) ? $attr['backend'] : '',
            'backend_type'          => isset($attr['type']) ? $attr['type'] : 'varchar',
            'backend_table'         => isset($attr['table']) ? $attr['table'] : '',
            'frontend_model'        => isset($attr['frontend']) ? $attr['frontend'] : '',
//            'frontend_block'        => isset($attr['frontend_block']) ? $attr['frontend_block'] : '',
            'frontend_input'        => isset($attr['input']) ? $attr['input'] : 'text',
            'frontend_label'        => isset($attr['label']) ? $attr['label'] : '',
            'source_model'          => isset($attr['source']) ? $attr['source'] : '',
            'is_global'             => isset($attr['global']) ? $attr['global'] : 1,
            'is_visible'            => isset($attr['visible']) ? (int) $attr['visible'] : 1,
            'is_required'           => isset($attr['required']) ? $attr['required'] : 1,
            'is_user_defined'       => isset($attr['user_defined']) ? $attr['user_defined'] : 0,
            'default_value'         => isset($attr['default']) ? $attr['default'] : '',
            'is_searchable'         => isset($attr['searchable']) ? $attr['searchable'] : 0,
            'is_filterable'         => isset($attr['filterable']) ? $attr['filterable'] : 0,
            'is_comparable'         => isset($attr['comparable']) ? $attr['comparable'] : 0,
            'is_visible_on_front'   => isset($attr['visible_on_front']) ? $attr['visible_on_front'] : 0,
            'is_visible_in_advanced_search' => isset($attr['visible_in_advanced_search']) ? $attr['visible_in_advanced_search'] : 0,
            'is_unique'             => isset($attr['unique']) ? $attr['unique'] : 0,
            'apply_to'              => isset($attr['apply_to']) ? $attr['apply_to'] : '',
            'is_configurable'       => isset($attr['is_configurable']) ? $attr['is_configurable'] : 1,
            'note'                  => isset($attr['note']) ? $attr['note'] : '',
            'position'              => isset($attr['position']) ? $attr['position'] : 0,
        );

        $sortOrder = isset($attr['sort_order']) ? $attr['sort_order'] : null;

        if ($id = $this->getAttribute($entityTypeId, $code, 'attribute_id')) {
            $this->updateAttribute($entityTypeId, $id, $data, null, $sortOrder);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute'), $data);
        }

        if (!empty($attr['group'])) {
            $sets = $this->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
            foreach ($sets as $set) {
                $this->addAttributeGroup($entityTypeId, $set['attribute_set_id'], $attr['group']);
                $this->addAttributeToSet($entityTypeId, $set['attribute_set_id'], $attr['group'], $code, $sortOrder);
            }
        }
        if (empty($attr['is_user_defined'])) {
            $sets = $this->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
            foreach ($sets as $set) {
                $this->addAttributeToSet($entityTypeId, $set['attribute_set_id'], $this->_generalGroupName, $code, $sortOrder);
            }
        }

        if (isset($attr['option']) && is_array($attr['option'])) {
            $option = $attr['option'];
            $option['attribute_id'] = $this->getAttributeId($entityTypeId, $code);
            $this->addAttributeOption($option);
        }

        return $this;
    }

    public function addAttributeOption($option)
    {
        if (isset($option['value'])) {
            $optionTable        = $this->getTable('eav/attribute_option');
            $optionValueTable   = $this->getTable('eav/attribute_option_value');

            foreach ($option['value'] as $optionId => $values) {
                $intOptionId = (int) $optionId;
                if (!empty($option['delete'][$optionId])) {
                    if ($intOptionId) {
                        $condition = $this->_conn->quoteInto('option_id=?', $intOptionId);
                        $write->delete($optionTable, $condition);
                    }
                    continue;
                }

                if (!$intOptionId) {
                    $data = array(
                        'attribute_id'  => $option['attribute_id'],
                        'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                    );
                    $this->_conn->insert($optionTable, $data);
                    $intOptionId = $this->_conn->lastInsertId();
                } else {
                    $data = array(
                        'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                    );
                    $this->_conn->update($optionTable, $data, $this->_conn->quoteInto('option_id=?', $intOptionId));
                }

                // Default value
                if (!isset($values[0])) {
                    Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                }

                $this->_conn->delete($optionValueTable, $this->_conn->quoteInto('option_id=?', $intOptionId));
                foreach ($values as $storeId => $value) {
                    $data = array(
                        'option_id' => $intOptionId,
                        'store_id'  => $storeId,
                        'value'     => $value,
                    );
                    $this->_conn->insert($optionValueTable, $data);
                }
            }
        }
    }

    public function updateAttribute($entityTypeId, $id, $field, $value=null, $sortOrder=null)
    {
        if (!is_null($sortOrder)) {
            $this->updateTableRow('eav/entity_attribute',
                'attribute_id', $this->getAttributeId($entityTypeId, $id),
                'sort_order', $sortOrder
            );
        }
        $this->updateTableRow('eav/attribute',
            'attribute_id', $this->getAttributeId($entityTypeId, $id),
            $field, $value,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
        return $this;
    }

    public function getAttribute($entityTypeId, $id, $field=null)
    {
        return $this->getTableRow('eav/attribute',
            is_numeric($id) ? 'attribute_id' : 'attribute_code', $id,
            $field,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
    }

    public function getAttributeId($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            $id = $this->getAttribute($entityTypeId, $id, 'attribute_id');
        }
        if (!is_numeric($id)) {
            //throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong attribute ID'));
            return false;
        }
        return $id;
    }

    /**
     * Return table name for eav attribute
     *
     * @param int|string $entityTypeId Entity Type id or Entity Type code
     * @param int|string $id Attribute id or Attribute code
     * @return string
     */
    public function getAttributeTable($entityTypeId, $id)
    {
        $entityKeyName = is_numeric($entityTypeId) ? 'entity_type_id' : 'entity_type_code';
        $attributeKeyName = is_numeric($id) ? 'attribute_id' : 'attribute_code';

        $select = $this->getConnection()->select()
            ->from(
                array('e' => $this->getTable('eav/entity_type')),
                array('entity_table'))
            ->join(
                array('a' => $this->getTable('eav/attribute')),
                'a.entity_type_id=e.entity_type_id',
                array('backend_type'))
            ->where("e.{$entityKeyName}=?", $entityTypeId)
            ->where("a.{$attributeKeyName}=?", $id)
            ->limit(1);
        if ($result = $this->getConnection()->fetchRow($select)) {
            $table = $this->getTable($result['entity_table']);
            if ($result['backend_type'] != 'static') {
                $table .= '_' . $result['backend_type'];
            }
            return $table;
        }

        return false;
    }

    public function removeAttribute($entityTypeId, $code)
    {
        if ($attributeId = $this->getAttributeId($entityTypeId, $code)) {
            $this->_conn->delete($this->getTable('eav/attribute'),
                $this->_conn->quoteInto('attribute_id=?', $attributeId)
            );
        }
        return $this;
    }

    public function getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder=null)
    {
        if (!is_numeric($sortOrder)) {
            $sortOrder = $this->_conn->fetchOne("select max(sort_order)
                from ".$this->getTable('eav/entity_attribute')."
                where attribute_group_id=".$this->getAttributeGroupId($entityTypeId, $setId, $groupId)
            );
            $sortOrder++;
        }
        return $sortOrder;
    }

    public function addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId, $sortOrder=null)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $setId = $this->getAttributeSetId($entityTypeId, $setId);
        $groupId = $this->getAttributeGroupId($entityTypeId, $setId, $groupId);
        $attributeId = $this->getAttributeId($entityTypeId, $attributeId);
        $generalGroupId = $this->getAttributeGroupId($entityTypeId, $setId, $this->_generalGroupName);

        $oldId = $this->_conn->fetchOne("select entity_attribute_id from ".$this->getTable('eav/entity_attribute')." where attribute_set_id=$setId and attribute_id=$attributeId");
        if ($oldId) {
            if ($groupId && $groupId != $generalGroupId) {
                $newGroupData = array('attribute_group_id'=>$groupId);
                $condition = $this->_conn->quoteInto('entity_attribute_id = ?', $oldId);
                $this->_conn->update($this->getTable('eav/entity_attribute'), $newGroupData, $condition);
            }
            return $this;
        }
        $this->_conn->insert($this->getTable('eav/entity_attribute'), array(
            'entity_type_id'=>$entityTypeId,
            'attribute_set_id'=>$setId,
            'attribute_group_id'=>$groupId,
            'attribute_id'=>$attributeId,
            'sort_order'=>$this->getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder),
        ));

        return $this;
    }

/******************* BULK INSTALL *****************/

    public function installEntities($entities=null)
    {
        $this->cleanCache();

        if (is_null($entities)) {
            $entities = $this->getDefaultEntities();
        }

        foreach ($entities as $entityName=>$entity) {
            $this->addEntityType($entityName, $entity);

            $frontendPrefix = isset($entity['frontend_prefix']) ? $entity['frontend_prefix'] : '';
            $backendPrefix = isset($entity['backend_prefix']) ? $entity['backend_prefix'] : '';
            $sourcePrefix = isset($entity['source_prefix']) ? $entity['source_prefix'] : '';

            foreach ($entity['attributes'] as $attrCode=>$attr) {
                if (!empty($attr['backend'])) {
                    if ('_'===$attr['backend']) {
                        $attr['backend'] = $backendPrefix;
                    } elseif ('_'===$attr['backend']{0}) {
                        $attr['backend'] = $backendPrefix.$attr['backend'];
                    } else {
                        $attr['backend'] = $attr['backend'];
                    }
                }
                if (!empty($attr['frontend'])) {
                    if ('_'===$attr['frontend']) {
                        $attr['frontend'] = $frontendPrefix;
                    } elseif ('_'===$attr['frontend']{0}) {
                        $attr['frontend'] = $frontendPrefix.$attr['frontend'];
                    } else {
                        $attr['frontend'] = $attr['frontend'];
                    }
                }
                if (!empty($attr['source'])) {
                    if ('_'===$attr['source']) {
                        $attr['source'] = $sourcePrefix;
                    } elseif ('_'===$attr['source']{0}) {
                        $attr['source'] = $sourcePrefix.$attr['source'];
                    } else {
                        $attr['source'] = $attr['source'];
                    }
                }

                $this->addAttribute($entityName, $attrCode, $attr);
            }
            $this->setDefaultSetToEntityType($entityName);
        }

        return $this;
    }


/****************************** CREATE ENTITY TABLES ***********************************/

    /**
     * Enter description here...
     *
     * @param unknown_type $baseName
     * @param array $options
     * - no-main
     * - no-default-types
     * - types
     * @return unknown
     */
    public function createEntityTables($baseName, array $options=array())
    {
        if (empty($options['no-main'])) {
            $sql = "
DROP TABLE IF EXISTS `{$baseName}`;
CREATE TABLE `{$baseName}` (
`entity_id` int(10) unsigned NOT NULL auto_increment,
`entity_type_id` smallint(8) unsigned NOT NULL default '0',
`attribute_set_id` smallint(5) unsigned NOT NULL default '0',
`increment_id` varchar(50) NOT NULL default '',
`parent_id` int(10) unsigned NULL default '0',
`store_id` smallint(5) unsigned NOT NULL default '0',
`created_at` datetime NOT NULL default '0000-00-00 00:00:00',
`updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
`is_active` tinyint(1) unsigned NOT NULL default '1',
PRIMARY KEY  (`entity_id`),
CONSTRAINT `FK_{$baseName}_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        }

        $types = array(
            'datetime'=>'datetime',
            'decimal'=>'decimal(12,4)',
            'int'=>'int',
            'text'=>'text',
            'varchar'=>'varchar(255)',
        );
        if (!empty($options['types']) && is_array($options['types'])) {
            if ($options['no-default-types']) {
                $types = array();
            }
            $types = array_merge($types, $options['types']);
        }

        foreach ($types as $type=>$fieldType) {
            $sql .= "
DROP TABLE IF EXISTS `{$baseName}_{$type}`;
CREATE TABLE `{$baseName}_{$type}` (
`value_id` int(11) NOT NULL auto_increment,
`entity_type_id` smallint(8) unsigned NOT NULL default '0',
`attribute_id` smallint(5) unsigned NOT NULL default '0',
`store_id` smallint(5) unsigned NOT NULL default '0',
`entity_id` int(10) unsigned NOT NULL default '0',
`value` {$fieldType} NOT NULL,
PRIMARY KEY  (`value_id`),
UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
".($type!=='text' ? "
KEY `value_by_attribute` (`attribute_id`,`value`),
KEY `value_by_entity_type` (`entity_type_id`,`value`),
" : "")."
CONSTRAINT `FK_{$baseName}_{$type}` FOREIGN KEY (`entity_id`) REFERENCES `{$baseName}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_{$baseName}_{$type}_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        }

        try {
            $this->_conn->multi_query($sql);
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Get identifiers of all attribute sets
     *
     * @return array
     */
    public function getAllAttributeSetIds($entityTypeId=null)
    {
        $where = '';
        if (!is_null($entityTypeId)) {
            $where = " WHERE `entity_type_id` = '" . $this->getEntityTypeId($entityTypeId) . "'";
        }
        $sql = "SELECT `attribute_set_id` FROM `{$this->getTable('eav/attribute_set')}`" . $where;
        return $this->_conn->fetchCol($sql);
    }
}