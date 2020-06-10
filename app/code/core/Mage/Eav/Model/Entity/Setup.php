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
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * EAV Entity Setup Model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method array getDefaultEntities()
 */
class Mage_Eav_Model_Entity_Setup extends Mage_Core_Model_Resource_Setup
{
    /**
     * General Attribute Group Name
     *
     * @var string
     */
    protected $_generalGroupName        = 'General';

    /**
     * Default attribute group name to id pairs
     *
     * @var array
     */
    public $defaultGroupIdAssociations  = array(
        'General'   => 1
    );

    /**
     * Default attribute group name
     *
     * @var string
     */
    protected $_defaultGroupName         = 'Default';

    /**
     * Default attribute set name
     *
     * @var string
     */
    protected $_defaultAttributeSetName  = 'Default';

    /**
     * Clean cache
     *
     * @return $this
     */
    public function cleanCache()
    {
        Mage::app()->cleanCache(array('eav'));
        return $this;
    }

    /**
     * Install Default Group Ids
     *
     * @return $this
     */
    public function installDefaultGroupIds()
    {
        $setIds = $this->getAllAttributeSetIds();
        foreach ($this->defaultGroupIdAssociations as $defaultGroupName => $defaultGroupId) {
            foreach ($setIds as $set) {
                $groupId = $this->getTableRow(
                    'eav/attribute_group',
                    'attribute_group_name',
                    $defaultGroupName,
                    'attribute_group_id',
                    'attribute_set_id',
                    $set
                );
                if (!$groupId) {
                    $groupId = $this->getTableRow(
                        'eav/attribute_group',
                        'attribute_set_id',
                        $set,
                        'attribute_group_id'
                    );
                }
                $this->updateTableRow(
                    'eav/attribute_group',
                    'attribute_group_id',
                    $groupId,
                    'default_id',
                    $defaultGroupId
                );
            }
        }

        return $this;
    }


/******************* ENTITY TYPES *****************/

    /**
     * Add an entity type
     *
     * If already exists updates the entity type with params data
     *
     * @param string $code
     * @param array $params
     * @return $this
     */
    public function addEntityType($code, array $params)
    {
        $data = array(
            'entity_type_code'              => $code,
            'entity_model'                  => $params['entity_model'],
            'attribute_model'               => $this->_getValue($params, 'attribute_model'),
            'entity_table'                  => $this->_getValue($params, 'table', 'eav/entity'),
            'value_table_prefix'            => $this->_getValue($params, 'table_prefix'),
            'entity_id_field'               => $this->_getValue($params, 'id_field'),
            'increment_model'               => $this->_getValue($params, 'increment_model'),
            'increment_per_store'           => $this->_getValue($params, 'increment_per_store', 0),
            'increment_pad_length'          => $this->_getValue($params, 'increment_pad_length', 8),
            'increment_pad_char'            => $this->_getValue($params, 'increment_pad_char', 0),
            'additional_attribute_table'    => $this->_getValue($params, 'additional_attribute_table'),
            'entity_attribute_collection'   => $this->_getValue($params, 'entity_attribute_collection'),
        );

        if ($this->getEntityType($code, 'entity_type_id')) {
            $this->updateEntityType($code, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/entity_type'), $data);
        }

        if (!empty($params['default_group'])) {
            $defaultGroup = $params['default_group'];
        } else {
            $defaultGroup = $this->_defaultGroupName;
        }

        $this->addAttributeSet($code, $this->_defaultAttributeSetName);
        $this->addAttributeGroup($code, $this->_defaultGroupName, $this->_generalGroupName);

        return $this;
    }

    /**
     * Update entity row
     *
     * @param string $code
     * @param string $field
     * @param string $value
     * @return $this
     */
    public function updateEntityType($code, $field, $value = null)
    {
        $this->updateTableRow(
            'eav/entity_type',
            'entity_type_id',
            $this->getEntityTypeId($code),
            $field,
            $value
        );
        return $this;
    }

    /**
     * Retrieve Entity Type Data
     *
     * @param int|string $id
     * @param string $field
     * @return mixed
     */
    public function getEntityType($id, $field = null)
    {
        return $this->getTableRow(
            'eav/entity_type',
            is_numeric($id) ? 'entity_type_id' : 'entity_type_code',
            $id,
            $field
        );
    }

    /**
     * Retrieve Entity Type Id By Id or Code
     *
     * @param mixed $entityTypeId
     * @return int
     */
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

    /**
     * Remove entity type by Id or Code
     *
     * @param mixed $id
     * @return $this
     */
    public function removeEntityType($id)
    {
        if (is_numeric($id)) {
            $this->deleteTableRow('eav/entity_type', 'entity_type_id', $id);
        } else {
            $this->deleteTableRow('eav/entity_type', 'entity_type_code', (string)$id);
        }

        return $this;
    }

/******************* ATTRIBUTE SETS *****************/

    /**
     * Retrieve Attribute Set Sort order
     *
     * @param mixed $entityTypeId
     * @param int $sortOrder
     * @return int
     */
    public function getAttributeSetSortOrder($entityTypeId, $sortOrder = null)
    {
        if (!is_numeric($sortOrder)) {
            $bind   = array('entity_type_id' => $this->getEntityTypeId($entityTypeId));
            $select = $this->_conn->select()
                ->from($this->getTable('eav/attribute_set'), 'MAX(sort_order)')
                ->where('entity_type_id = :entity_type_id');

            $sortOrder = $this->_conn->fetchOne($select, $bind) + 1;
        }

        return $sortOrder;
    }

    /**
     * Add Attribute Set
     *
     * @param mixed $entityTypeId
     * @param string $name
     * @param int $sortOrder
     * @return $this
     */
    public function addAttributeSet($entityTypeId, $name, $sortOrder = null)
    {
        $data = array(
            'entity_type_id'        => $this->getEntityTypeId($entityTypeId),
            'attribute_set_name'    => $name,
            'sort_order'            => $this->getAttributeSetSortOrder($entityTypeId, $sortOrder),
        );

        $setId = $this->getAttributeSet($entityTypeId, $name, 'attribute_set_id');
        if ($setId) {
            $this->updateAttributeSet($entityTypeId, $setId, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute_set'), $data);

            $this->addAttributeGroup($entityTypeId, $name, $this->_generalGroupName);
        }

        return $this;
    }

    /**
     * Update attribute set data
     *
     * @param mixed $entityTypeId
     * @param int $id
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function updateAttributeSet($entityTypeId, $id, $field, $value = null)
    {
        $this->updateTableRow(
            'eav/attribute_set',
            'attribute_set_id',
            $this->getAttributeSetId($entityTypeId, $id),
            $field,
            $value,
            'entity_type_id',
            $this->getEntityTypeId($entityTypeId)
        );
        return $this;
    }

    /**
     * Retrieve Attribute set data by id or name
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @param string $field
     * @return mixed
     */
    public function getAttributeSet($entityTypeId, $id, $field = null)
    {
        return $this->getTableRow(
            'eav/attribute_set',
            is_numeric($id) ? 'attribute_set_id' : 'attribute_set_name',
            $id,
            $field,
            'entity_type_id',
            $this->getEntityTypeId($entityTypeId)
        );
    }

    /**
     * Retrieve Attribute Set Id By Id or Name
     *
     * @throws Mage_Eav_Exception
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @return int
     */
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

    /**
     * Remove Attribute Set
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @return $this
     */
    public function removeAttributeSet($entityTypeId, $id)
    {
        $this->deleteTableRow('eav/attribute_set', 'attribute_set_id', $this->getAttributeSetId($entityTypeId, $id));
        return $this;
    }

    /**
     * Set Default Attribute Set to Entity Type
     *
     * @param string $entityType
     * @param string $attributeSet
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Mage_Eav_Exception
     */
    public function setDefaultSetToEntityType($entityType, $attributeSet = 'Default')
    {
        $entityTypeId = $this->getEntityTypeId($entityType);
        $setId        = $this->getAttributeSetId($entityTypeId, $attributeSet);
        $this->updateEntityType($entityTypeId, 'default_attribute_set_id', $setId);
        return $this;
    }

    /**
     * Get identifiers of all attribute sets
     *
     * @param int $entityTypeId
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getAllAttributeSetIds($entityTypeId = null)
    {
        $select = $this->_conn->select()
            ->from($this->getTable('eav/attribute_set'), 'attribute_set_id');

        $bind = array();
        if ($entityTypeId !== null) {
            $bind['entity_type_id'] = $this->getEntityTypeId($entityTypeId);
            $select->where('entity_type_id = :entity_type_id');
        }

        return $this->_conn->fetchCol($select, $bind);
    }

    /**
     * Retrieve Default Attribute Set for Entity Type
     *
     * @param string|int $entityType
     * @return int
     */
    public function getDefaultAttributeSetId($entityType)
    {
        $bind = array('entity_type' => $entityType);
        if (is_numeric($entityType)) {
            $where = 'entity_type_id = :entity_type';
        } else {
            $where = 'entity_type_code = :entity_type';
        }
        $select = $this->getConnection()->select()
            ->from($this->getTable('eav/entity_type'), 'default_attribute_set_id')
            ->where($where);

        return $this->getConnection()->fetchOne($select, $bind);
    }

/******************* ATTRIBUTE GROUPS *****************/

    /**
     * Retrieve Attribute Group Sort order
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param int $sortOrder
     * @return int
     */
    public function getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder = null)
    {
        if (!is_numeric($sortOrder)) {
            $bind   = array('attribute_set_id' => $this->getAttributeSetId($entityTypeId, $setId));
            $select = $this->_conn->select()
                ->from($this->getTable('eav/attribute_group'), 'MAX(sort_order)')
                ->where('attribute_set_id = :attribute_set_id');

            $sortOrder = $this->_conn->fetchOne($select, $bind) + 1;
        }

        return $sortOrder;
    }

    /**
     * Add Attribute Group
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param string $name
     * @param int $sortOrder
     * @return $this
     */
    public function addAttributeGroup($entityTypeId, $setId, $name, $sortOrder = null)
    {
        $setId  = $this->getAttributeSetId($entityTypeId, $setId);
        $data   = array(
            'attribute_set_id'      => $setId,
            'attribute_group_name'  => $name,
        );

        if (isset($this->defaultGroupIdAssociations[$name])) {
            $data['default_id'] = $this->defaultGroupIdAssociations[$name];
        }

        if ($sortOrder !== null) {
            $data['sort_order'] = $sortOrder;
        }

        $groupId = $this->getAttributeGroup($entityTypeId, $setId, $name, 'attribute_group_id');
        if ($groupId) {
            $this->updateAttributeGroup($entityTypeId, $setId, $groupId, $data);
        } else {
            if ($sortOrder === null) {
                $data['sort_order'] = $this->getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder);
            }
            $this->_conn->insert($this->getTable('eav/attribute_group'), $data);
        }

        return $this;
    }

    /**
     * Update Attribute Group Data
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $id
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function updateAttributeGroup($entityTypeId, $setId, $id, $field, $value = null)
    {
        $this->updateTableRow(
            'eav/attribute_group',
            'attribute_group_id',
            $this->getAttributeGroupId($entityTypeId, $setId, $id),
            $field,
            $value,
            'attribute_set_id',
            $this->getAttributeSetId($entityTypeId, $setId)
        );

        return $this;
    }

    /**
     * Retrieve Attribute Group Data
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $id
     * @param string $field
     * @return mixed
     */
    public function getAttributeGroup($entityTypeId, $setId, $id, $field = null)
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

        return $this->getTableRow(
            'eav/attribute_group',
            $searchField,
            $searchId,
            $field,
            'attribute_set_id',
            $this->getAttributeSetId($entityTypeId, $setId)
        );
    }

    /**
     * Retrieve Attribute Group Id by Id or Name
     *
     * @param int $entityTypeId
     * @param int $setId
     * @param int|string $groupId
     * @return int
     */
    public function getAttributeGroupId($entityTypeId, $setId, $groupId)
    {
        if (!is_numeric($groupId)) {
            $groupId = $this->getAttributeGroup($entityTypeId, $setId, $groupId, 'attribute_group_id');
        }

        if (!is_numeric($groupId)) {
            $groupId = $this->getDefaultAttributeGroupId($entityTypeId, $setId);
        }

        if (!is_numeric($groupId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong attribute group ID'));
        }
        return $groupId;
    }

    /**
     * Remove Attribute Group By Id or Name
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $id
     * @return $this
     */
    public function removeAttributeGroup($entityTypeId, $setId, $id)
    {
        $this->deleteTableRow(
            'eav/attribute_group',
            'attribute_group_id',
            $this->getAttributeGroupId($entityTypeId, $setId, $id)
        );
        return $this;
    }

    /**
     * Retrieve Default Attribute Group Id By Entity Type and Attribute Set
     *
     * @param string|int $entityType
     * @param int $attributeSetId
     * @return int
     */
    public function getDefaultAttributeGroupId($entityType, $attributeSetId = null)
    {
        $entityType = $this->getEntityTypeId($entityType);
        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getDefaultAttributeSetId($entityType);
        }
        $bind   = array('attribute_set_id' => $attributeSetId);
        $select = $this->getConnection()->select()
            ->from($this->getTable('eav/attribute_group'), 'attribute_group_id')
            ->where('attribute_set_id = :attribute_set_id')
            ->order(array('default_id ' . Varien_Db_Select::SQL_DESC, 'sort_order'))
            ->limit(1);

        return $this->getConnection()->fetchOne($select, $bind);
    }

/******************* ATTRIBUTES *****************/

    /**
     * Retrieve value from array by key or return default value
     *
     * @param array $array
     * @param string $key
     * @param string $default
     * @return string
     */
    protected function _getValue($array, $key, $default = null)
    {
        if (isset($array[$key]) && is_bool($array[$key])) {
            $array[$key] = (int) $array[$key];
        }
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Prepare attribute values to save
     *
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = array(
            'backend_model'   => $this->_getValue($attr, 'backend'),
            'backend_type'    => $this->_getValue($attr, 'type', 'varchar'),
            'backend_table'   => $this->_getValue($attr, 'table'),
            'frontend_model'  => $this->_getValue($attr, 'frontend'),
            'frontend_input'  => $this->_getValue($attr, 'input', 'text'),
            'frontend_label'  => $this->_getValue($attr, 'label'),
            'frontend_class'  => $this->_getValue($attr, 'frontend_class'),
            'source_model'    => $this->_getValue($attr, 'source'),
            'is_required'     => $this->_getValue($attr, 'required', 1),
            'is_user_defined' => $this->_getValue($attr, 'user_defined', 0),
            'default_value'   => $this->_getValue($attr, 'default'),
            'is_unique'       => $this->_getValue($attr, 'unique', 0),
            'note'            => $this->_getValue($attr, 'note'),
            'is_global'       => $this->_getValue($attr, 'global', 1),
        );

        return $data;
    }

    /**
     * Validate attribute data before insert into table
     *
     * @param  array $data
     * @throws Mage_Eav_Exception
     * @return true
     */
    protected function _validateAttributeData($data)
    {
        $attributeCodeMaxLength = Mage_Eav_Model_Entity_Attribute::ATTRIBUTE_CODE_MAX_LENGTH;

        if (isset($data['attribute_code']) &&
           !Zend_Validate::is($data['attribute_code'], 'StringLength', array('max' => $attributeCodeMaxLength))) {
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('Maximum length of attribute code must be less then %s symbols', $attributeCodeMaxLength)
            );
        }

        return true;
    }

    /**
     * Add attribute to an entity type
     *
     * If attribute is system will add to all existing attribute sets
     *
     * @param string|integer $entityTypeId
     * @param string $code
     * @param array $attr
     * @return $this
     */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $data = array_merge(
            array(
                'entity_type_id' => $entityTypeId,
                'attribute_code' => $code
            ),
            $this->_prepareValues($attr)
        );

        $this->_validateAttributeData($data);

        $sortOrder = isset($attr['sort_order']) ? $attr['sort_order'] : null;
        $attributeId = $this->getAttribute($entityTypeId, $code, 'attribute_id');
        if ($attributeId) {
            $this->updateAttribute($entityTypeId, $attributeId, $data, null, $sortOrder);
        } else {
            $this->_insertAttribute($data);
        }

        if (!empty($attr['group']) || empty($attr['user_defined'])) {
            $select = $this->_conn->select()
                ->from($this->getTable('eav/attribute_set'))
                ->where('entity_type_id = :entity_type_id');
            $sets = $this->_conn->fetchAll($select, array('entity_type_id' => $entityTypeId));
            foreach ($sets as $set) {
                if (!empty($attr['group'])) {
                    $this->addAttributeGroup(
                        $entityTypeId,
                        $set['attribute_set_id'],
                        $attr['group']
                    );
                    $this->addAttributeToSet(
                        $entityTypeId,
                        $set['attribute_set_id'],
                        $attr['group'],
                        $code,
                        $sortOrder
                    );
                } else {
                    $this->addAttributeToSet(
                        $entityTypeId,
                        $set['attribute_set_id'],
                        $this->_generalGroupName,
                        $code,
                        $sortOrder
                    );
                }
            }
        }

        if (isset($attr['option']) && is_array($attr['option'])) {
            $option = $attr['option'];
            $option['attribute_id'] = $this->getAttributeId($entityTypeId, $code);
            $this->addAttributeOption($option);
        }

        return $this;
    }

    /**
     * Add Attribure Option
     *
     * @param array $option
     */
    public function addAttributeOption($option)
    {
        $optionTable        = $this->getTable('eav/attribute_option');
        $optionValueTable   = $this->getTable('eav/attribute_option_value');

        if (isset($option['value'])) {
            foreach ($option['value'] as $optionId => $values) {
                $intOptionId = (int) $optionId;
                if (!empty($option['delete'][$optionId])) {
                    if ($intOptionId) {
                        $condition = array('option_id =?' => $intOptionId);
                        $this->_conn->delete($optionTable, $condition);
                    }
                    continue;
                }

                if (!$intOptionId) {
                    $data = array(
                        'attribute_id'  => $option['attribute_id'],
                        'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                    );
                    $this->_conn->insert($optionTable, $data);
                    $intOptionId = $this->_conn->lastInsertId($optionTable);
                } else {
                    $data = array(
                        'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                    );
                    $this->_conn->update($optionTable, $data, array('option_id=?' => $intOptionId));
                }

                // Default value
                if (!isset($values[0])) {
                    Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                }
                $condition = array('option_id =?' => $intOptionId);
                $this->_conn->delete($optionValueTable, $condition);
                foreach ($values as $storeId => $value) {
                    $data = array(
                        'option_id' => $intOptionId,
                        'store_id'  => $storeId,
                        'value'     => $value,
                    );
                    $this->_conn->insert($optionValueTable, $data);
                }
            }
        } elseif (isset($option['values'])) {
            foreach ($option['values'] as $sortOrder => $label) {
                // add option
                $data = array(
                    'attribute_id' => $option['attribute_id'],
                    'sort_order'   => $sortOrder,
                );
                $this->_conn->insert($optionTable, $data);
                $intOptionId = $this->_conn->lastInsertId($optionTable);

                $data = array(
                    'option_id' => $intOptionId,
                    'store_id'  => 0,
                    'value'     => $label,
                );
                $this->_conn->insert($optionValueTable, $data);
            }
        }
    }

    /**
     * Update Attribute data and Attribute additional data
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @param string $field
     * @param mixed $value
     * @param int $sortOrder
     * @return $this
     */
    public function updateAttribute($entityTypeId, $id, $field, $value = null, $sortOrder = null)
    {
        $this->_updateAttribute($entityTypeId, $id, $field, $value, $sortOrder);
        $this->_updateAttributeAdditionalData($entityTypeId, $id, $field, $value);
        return $this;
    }

    /**
     * Update Attribute data
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @param string $field
     * @param mixed $value
     * @param int $sortOrder
     * @return $this
     */
    protected function _updateAttribute($entityTypeId, $id, $field, $value = null, $sortOrder = null)
    {
        if ($sortOrder !== null) {
            $this->updateTableRow(
                'eav/entity_attribute',
                'attribute_id',
                $this->getAttributeId($entityTypeId, $id),
                'sort_order',
                $sortOrder
            );
        }

        $attributeFields = $this->_getAttributeTableFields();
        if (is_array($field)) {
            $bind = array();
            foreach ($field as $k => $v) {
                if (isset($attributeFields[$k])) {
                    $bind[$k] = $this->getConnection()->prepareColumnValue($attributeFields[$k], $v);
                }
            }
            if (!$bind) {
                return $this;
            }
            $field = $bind;
        } else {
            if (!isset($attributeFields[$field])) {
                return $this;
            }
        }

        $this->updateTableRow(
            'eav/attribute',
            'attribute_id',
            $this->getAttributeId($entityTypeId, $id),
            $field,
            $value,
            'entity_type_id',
            $this->getEntityTypeId($entityTypeId)
        );

        return $this;
    }

    /**
     * Update Attribute Additional data
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    protected function _updateAttributeAdditionalData($entityTypeId, $id, $field, $value = null)
    {
        $additionalTable = $this->getEntityType($entityTypeId, 'additional_attribute_table');
        if (!$additionalTable) {
            return $this;
        }
        $additionalTableExists = $this->getConnection()->isTableExists($this->getTable($additionalTable));
        if ($additionalTable && $additionalTableExists) {
            $attributeFields = $this->getConnection()->describeTable($this->getTable($additionalTable));
            if (is_array($field)) {
                $bind = array();
                foreach ($field as $k => $v) {
                    if (isset($attributeFields[$k])) {
                        $bind[$k] = $this->getConnection()->prepareColumnValue($attributeFields[$k], $v);
                    }
                }
                if (!$bind) {
                    return $this;
                }
                $field = $bind;
            } else {
                if (!isset($attributeFields[$field])) {
                    return $this;
                }
            }
            $this->updateTableRow(
                $this->getTable($additionalTable),
                'attribute_id',
                $this->getAttributeId($entityTypeId, $id),
                $field,
                $value
            );
        }

        return $this;
    }

    /**
     * Retrieve Attribute Data By Id or Code
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @param string $field
     * @return mixed
     */
    public function getAttribute($entityTypeId, $id, $field = null)
    {
        $additionalTable    = $this->getEntityType($entityTypeId, 'additional_attribute_table');
        $entityTypeId       = $this->getEntityTypeId($entityTypeId);
        $idField            = is_numeric($id) ? 'attribute_id' : 'attribute_code';
        if (!$additionalTable) {
            return $this->getTableRow('eav/attribute', $idField, $id, $field, 'entity_type_id', $entityTypeId);
        }

        $mainTable          = $this->getTable('eav/attribute');
        if (empty($this->_setupCache[$mainTable][$entityTypeId][$id])) {
            $additionalTable = $this->getTable($additionalTable);
            $bind = array(
                'id'                => $id,
                'entity_type_id'    => $entityTypeId
            );
            $select = $this->_conn->select()
                ->from(array('main' => $mainTable))
                ->join(
                    array('additional' => $additionalTable),
                    'main.attribute_id = additional.attribute_id'
                )
                ->where("main.{$idField} = :id")
                ->where('main.entity_type_id = :entity_type_id');

            $row = $this->_conn->fetchRow($select, $bind);
            if (!$row) {
                $this->_setupCache[$mainTable][$entityTypeId][$id] = false;
            } else {
                $this->_setupCache[$mainTable][$entityTypeId][$row['attribute_id']] = $row;
                $this->_setupCache[$mainTable][$entityTypeId][$row['attribute_code']] = $row;
            }
        }

        $row = $this->_setupCache[$mainTable][$entityTypeId][$id];
        if ($field !== null) {
            return isset($row[$field]) ? $row[$field] : false;
        }

        return $row;
    }

    /**
     * Retrieve Attribute Id Data By Id or Code
     *
     * @param mixed $entityTypeId
     * @param mixed $id
     * @return int
     */
    public function getAttributeId($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            $id = $this->getAttribute($entityTypeId, $id, 'attribute_id');
        }
        if (!is_numeric($id)) {
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
        $entityKeyName    = is_numeric($entityTypeId) ? 'entity_type_id' : 'entity_type_code';
        $attributeKeyName = is_numeric($id) ? 'attribute_id' : 'attribute_code';

        $bind = array(
            'id'                => $id,
            'entity_type_id'    => $entityTypeId
        );
        $select = $this->getConnection()->select()
            ->from(
                array('entity_type' => $this->getTable('eav/entity_type')),
                array('entity_table')
            )
            ->join(
                array('attribute' => $this->getTable('eav/attribute')),
                'attribute.entity_type_id = entity_type.entity_type_id',
                array('backend_type')
            )
            ->where("entity_type.{$entityKeyName} = :entity_type_id")
            ->where("attribute.{$attributeKeyName} = :id")
            ->limit(1);

        $result = $this->getConnection()->fetchRow($select, $bind);
        if ($result) {
            $table = $this->getTable($result['entity_table']);
            if ($result['backend_type'] != 'static') {
                $table .= '_' . $result['backend_type'];
            }
            return $table;
        }

        return false;
    }

    /**
     * Remove Attribute
     *
     * @param mixed $entityTypeId
     * @param mixed $code
     * @return $this
     */
    public function removeAttribute($entityTypeId, $code)
    {
        $mainTable  = $this->getTable('eav/attribute');
        $attribute  = $this->getAttribute($entityTypeId, $code);
        if ($attribute) {
            $this->deleteTableRow('eav/attribute', 'attribute_id', $attribute['attribute_id']);
            if (isset($this->_setupCache[$mainTable][$attribute['entity_type_id']][$attribute['attribute_code']])) {
                unset($this->_setupCache[$mainTable][$attribute['entity_type_id']][$attribute['attribute_code']]);
            }
        }
        return $this;
    }

    /**
     * Retrieve Attribute Sort Order
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $groupId
     * @param int $sortOrder
     * @return int|string
     */
    public function getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder = null)
    {
        if (!is_numeric($sortOrder)) {
            $bind = array('attribute_group_id' => $this->getAttributeGroupId($entityTypeId, $setId, $groupId));
            $select = $this->_conn->select()
                ->from($this->getTable('eav/entity_attribute'), 'MAX(sort_order)')
                ->where('attribute_group_id = :attribute_group_id');

            $sortOrder = $this->_conn->fetchOne($select, $bind) + 1;
        }

        return $sortOrder;
    }

    /**
     * Add Attribute to All Groups on Attribute Set
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param mixed $groupId
     * @param mixed $attributeId
     * @param int $sortOrder
     * @return $this
     */
    public function addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId, $sortOrder = null)
    {
        $entityTypeId   = $this->getEntityTypeId($entityTypeId);
        $setId          = $this->getAttributeSetId($entityTypeId, $setId);
        $groupId        = $this->getAttributeGroupId($entityTypeId, $setId, $groupId);
        $attributeId    = $this->getAttributeId($entityTypeId, $attributeId);
        $table          = $this->getTable('eav/entity_attribute');

        $bind = array(
            'attribute_set_id' => $setId,
            'attribute_id'     => $attributeId
        );
        $select = $this->_conn->select()
            ->from($table)
            ->where('attribute_set_id = :attribute_set_id')
            ->where('attribute_id = :attribute_id');
        $result = $this->_conn->fetchRow($select, $bind);

        if ($result) {
            if ($result['attribute_group_id'] != $groupId) {
                $where = array('entity_attribute_id =?' => $result['entity_attribute_id']);
                $data  = array('attribute_group_id' => $groupId);
                $this->_conn->update($table, $data, $where);
            }
        } else {
            $data = array(
                'entity_type_id'        => $entityTypeId,
                'attribute_set_id'      => $setId,
                'attribute_group_id'    => $groupId,
                'attribute_id'          => $attributeId,
                'sort_order'            => $this->getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder),
            );

            $this->_conn->insert($table, $data);
        }

        return $this;
    }

    /**
     * Add or update attribute to group
     *
     * @param int|string $entityType
     * @param int|string $setId
     * @param int|string $groupId
     * @param int|string $attributeId
     * @param int $sortOrder
     * @return $this
     */
    public function addAttributeToGroup($entityType, $setId, $groupId, $attributeId, $sortOrder = null)
    {
        $entityType  = $this->getEntityTypeId($entityType);
        $setId       = $this->getAttributeSetId($entityType, $setId);
        $groupId     = $this->getAttributeGroupId($entityType, $setId, $groupId);
        $attributeId = $this->getAttributeId($entityType, $attributeId);

        $data = array(
            'entity_type_id'        => $entityType,
            'attribute_set_id'      => $setId,
            'attribute_group_id'    => $groupId,
            'attribute_id'          => $attributeId,
        );

        $bind = array(
            'entity_type_id'    => $entityType,
            'attribute_set_id'  => $setId,
            'attribute_id'      => $attributeId
        );
        $select = $this->getConnection()->select()
            ->from($this->getTable('eav/entity_attribute'))
            ->where('entity_type_id = :entity_type_id')
            ->where('attribute_set_id = :attribute_set_id')
            ->where('attribute_id = :attribute_id');
        $row = $this->getConnection()->fetchRow($select, $bind);
        if ($row) {
            // update
            if ($sortOrder !== null) {
                $data['sort_order'] = $sortOrder;
            }

            $this->getConnection()->update(
                $this->getTable('eav/entity_attribute'),
                $data,
                $this->getConnection()->quoteInto('entity_attribute_id=?', $row['entity_attribute_id'])
            );
        } else {
            if ($sortOrder === null) {
                $select = $this->getConnection()->select()
                    ->from($this->getTable('eav/entity_attribute'), 'MAX(sort_order)')
                    ->where('entity_type_id = :entity_type_id')
                    ->where('attribute_set_id = :attribute_set_id')
                    ->where('attribute_id = :attribute_id');

                $sortOrder = $this->getConnection()->fetchOne($select, $bind) + 10;
            }
            $sortOrder = is_numeric($sortOrder) ? $sortOrder : 1;
            $data['sort_order'] = $sortOrder;
            $this->getConnection()->insert($this->getTable('eav/entity_attribute'), $data);
        }

        return $this;
    }

/******************* BULK INSTALL *****************/

    /**
     * Install entities
     *
     * @param array $entities
     * @return $this
     */
    public function installEntities($entities = null)
    {
        $this->cleanCache();

        if ($entities === null) {
            $entities = $this->getDefaultEntities();
        }

        foreach ($entities as $entityName => $entity) {
            $this->addEntityType($entityName, $entity);

            $frontendPrefix = isset($entity['frontend_prefix']) ? $entity['frontend_prefix'] : '';
            $backendPrefix  = isset($entity['backend_prefix']) ? $entity['backend_prefix'] : '';
            $sourcePrefix   = isset($entity['source_prefix']) ? $entity['source_prefix'] : '';

            foreach ($entity['attributes'] as $attrCode => $attr) {
                if (!empty($attr['backend'])) {
                    if ('_' === $attr['backend']) {
                        $attr['backend'] = $backendPrefix;
                    } elseif ('_' === $attr['backend'][0]) {
                        $attr['backend'] = $backendPrefix.$attr['backend'];
                    }
                }
                if (!empty($attr['frontend'])) {
                    if ('_' === $attr['frontend']) {
                        $attr['frontend'] = $frontendPrefix;
                    } elseif ('_' === $attr['frontend']{0}) {
                        $attr['frontend'] = $frontendPrefix.$attr['frontend'];
                    }
                }
                if (!empty($attr['source'])) {
                    if ('_' === $attr['source']) {
                        $attr['source'] = $sourcePrefix;
                    } elseif ('_' === $attr['source']{0}) {
                        $attr['source'] = $sourcePrefix . $attr['source'];
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
     * Create entity tables
     *
     * @param string $baseTableName
     * @param array $options
     * - no-main
     * - no-default-types
     * - types
     * @return Mage_Eav_Model_Entity_Setup
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Exception
     */
    public function createEntityTables($baseTableName, array $options = array())
    {
        $isNoCreateMainTable = $this->_getValue($options, 'no-main', false);
        $isNoDefaultTypes    = $this->_getValue($options, 'no-default-types', false);
        $customTypes         = $this->_getValue($options, 'types', array());
        $tables              = array();

        if (!$isNoCreateMainTable) {
            /**
             * Create table main eav table
             */
            $connection = $this->getConnection();
            $mainTable = $connection
                ->newTable($this->getTable($baseTableName))
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                 ), 'Entity Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Entity Type Id')
                ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Attribute Set Id')
                ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
                    'nullable'  => false,
                    'default'   => '',
                ), 'Increment Id')
                ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Store Id')
                ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Created At')
                ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Updated At')
                ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '1',
                ), 'Defines Is Entity Active')
                ->addIndex(
                    $this->getIdxName($baseTableName, array('entity_type_id')),
                    array('entity_type_id')
                )
                ->addIndex(
                    $this->getIdxName($baseTableName, array('store_id')),
                    array('store_id')
                )
                ->addForeignKey(
                    $this->getFkName($baseTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id',
                    $this->getTable('eav/entity_type'),
                    'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE,
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($baseTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id',
                    $this->getTable('core/store'),
                    'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE,
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )
                ->setComment('Eav Entity Main Table');

            $tables[$this->getTable($baseTableName)] = $mainTable;
        }

        $types = array();
        if (!$isNoDefaultTypes) {
            $types = array(
                'datetime'  => array(Varien_Db_Ddl_Table::TYPE_DATETIME, null),
                'decimal'   => array(Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4'),
                'int'       => array(Varien_Db_Ddl_Table::TYPE_INTEGER, null),
                'text'      => array(Varien_Db_Ddl_Table::TYPE_TEXT, '64k'),
                'varchar'   => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255'),
                'char'   => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255')
            );
        }

        if (!empty($customTypes)) {
            foreach ($customTypes as $type => $fieldType) {
                if (count($fieldType) != 2) {
                    throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong type definition for %s', $type));
                }
                $types[$type] = $fieldType;
            }
        }

        /**
         * Create table array($baseTableName, $type)
         */
        foreach ($types as $type => $fieldType) {
            $eavTableName = array($baseTableName, $type);

            $eavTable = $connection->newTable($this->getTable($eavTableName));
            $eavTable
                ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'unsigned'  => true,
                    ), 'Value Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Type Id')
                ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Attribute Id')
                ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Store Id')
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                    ), 'Entity Id')
                ->addColumn('value', $fieldType[0], $fieldType[1], array(
                    'nullable'  => false,
                    ), 'Attribute Value')
                ->addIndex(
                    $this->getIdxName($eavTableName, array('entity_type_id')),
                    array('entity_type_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('attribute_id')),
                    array('attribute_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('store_id')),
                    array('store_id')
                )
                ->addIndex(
                    $this->getIdxName($eavTableName, array('entity_id')),
                    array('entity_id')
                );
            if ($type !== 'text') {
                $eavTable->addIndex(
                    $this->getIdxName($eavTableName, array('attribute_id', 'value')),
                    array('attribute_id', 'value')
                );
                $eavTable->addIndex(
                    $this->getIdxName($eavTableName, array('entity_type_id', 'value')),
                    array('entity_type_id', 'value')
                );
            }

            $eavTable
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'entity_id', $baseTableName, 'entity_id'),
                    'entity_id',
                    $this->getTable($baseTableName),
                    'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE,
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id',
                    $this->getTable('eav/entity_type'),
                    'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE,
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $this->getFkName($eavTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id',
                    $this->getTable('core/store'),
                    'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE,
                    Varien_Db_Ddl_Table::ACTION_CASCADE
                )
                ->setComment('Eav Entity Value Table');

            $tables[$this->getTable($eavTableName)] = $eavTable;
        }

        // DDL operations are forbidden within transactions
        // See Varien_Db_Adapter_Pdo_Mysql::_checkDdlTransaction()
        try {
            foreach ($tables as $tableName => $table) {
                $connection->createTable($table);
            }
        } catch (Exception $e) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Can\'t create table: %s', $tableName));
        }

        return $this;
    }

    /**
     * Retrieve attribute table fields
     *
     * @return array
     */
    protected function _getAttributeTableFields()
    {
        return $this->getConnection()->describeTable($this->getTable('eav/attribute'));
    }

    /**
     * Insert attribute and filter data
     *
     * @param array $data
     * @return $this
     */
    protected function _insertAttribute(array $data)
    {
        $bind   = array();

        $fields = $this->_getAttributeTableFields();

        foreach ($data as $k => $v) {
            if (isset($fields[$k])) {
                $bind[$k] = $this->getConnection()->prepareColumnValue($fields[$k], $v);
            }
        }
        if (!$bind) {
            return $this;
        }

        $this->getConnection()->insert($this->getTable('eav/attribute'), $bind);
        $attributeId = $this->getConnection()->lastInsertId($this->getTable('eav/attribute'));
        $this->_insertAttributeAdditionalData(
            $data['entity_type_id'],
            array_merge(array('attribute_id' => $attributeId), $data)
        );

        return $this;
    }

    /**
     * Insert attribute additional data
     *
     * @param int $entityTypeId
     * @param array $data
     * @return $this
     */
    protected function _insertAttributeAdditionalData($entityTypeId, array $data)
    {
        $additionalTable = $this->getEntityType($entityTypeId, 'additional_attribute_table');
        if (!$additionalTable) {
            return $this;
        }
        $additionalTableExists = $this->getConnection()->isTableExists($this->getTable($additionalTable));
        if ($additionalTable && $additionalTableExists) {
            $bind   = array();
            $fields = $this->getConnection()->describeTable($this->getTable($additionalTable));
            foreach ($data as $k => $v) {
                if (isset($fields[$k])) {
                    $bind[$k] = $this->getConnection()->prepareColumnValue($fields[$k], $v);
                }
            }
            if (!$bind) {
                return $this;
            }
            $this->getConnection()->insert($this->getTable($additionalTable), $bind);
        }

        return $this;
    }
}
