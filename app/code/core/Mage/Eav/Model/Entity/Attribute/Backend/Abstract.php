<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity/Attribute/Model - attribute backend abstract
 *
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Entity_Attribute_Backend_Abstract implements Mage_Eav_Model_Entity_Attribute_Backend_Interface
{
    /**
     * Reference to the attribute instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attribute;

    /**
     * PK value_id for loaded entity (for faster updates)
     *
     * @var int
     */
    protected $_valueId;

    /**
     * PK value_ids for each loaded entity
     *
     * @var array
     */
    protected $_valueIds = [];

    /**
     * Table name for this attribute
     *
     * @var string
     */
    protected $_table;

    /**
     * Name of the entity_id field for the value table of this attribute
     *
     * @var string
     */
    protected $_entityIdField;

    /**
     * Default value for the attribute
     *
     * @var mixed
     */
    protected $_defaultValue = null;

    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Get backend type of the attribute
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute()->getBackendType();
    }

    /**
     * Check whether the attribute is a real field in the entity table
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->getAttribute()->isStatic();
    }

    /**
     * Get table name for the values of the attribute
     *
     * @return string
     */
    public function getTable()
    {
        if (empty($this->_table)) {
            if ($this->isStatic()) {
                $this->_table = $this->getAttribute()->getEntityType()->getValueTablePrefix();
            } elseif ($this->getAttribute()->getBackendTable()) {
                $this->_table = $this->getAttribute()->getBackendTable();
            } else {
                $entity = $this->getAttribute()->getEntity();
                $tableName = sprintf('%s_%s', $entity->getValueTablePrefix(), $this->getType());
                $this->_table = $tableName;
            }
        }

        return $this->_table;
    }

    /**
     * Get entity_id field in the attribute values tables
     *
     * @return string
     */
    public function getEntityIdField()
    {
        if (empty($this->_entityIdField)) {
            if ($this->getAttribute()->getEntityIdField()) {
                $this->_entityIdField = $this->getAttribute()->getEntityIdField();
            } else {
                $this->_entityIdField = $this->getAttribute()->getEntityType()->getValueEntityIdField();
            }
        }

        return $this->_entityIdField;
    }

    /**
     * Set value id
     *
     * @param int $valueId
     * @return $this
     */
    public function setValueId($valueId)
    {
        $this->_valueId = $valueId;
        return $this;
    }

    /**
     * Set entity value id
     *
     * @param Varien_Object $entity
     * @param int $valueId
     * @return $this
     */
    public function setEntityValueId($entity, $valueId)
    {
        if (!$entity || !$entity->getId()) {
            return $this->setValueId($valueId);
        }

        $this->_valueIds[$entity->getId()] = $valueId;
        return $this;
    }

    /**
     * Retrieve value id
     *
     * @return int
     */
    public function getValueId()
    {
        return $this->_valueId;
    }

    /**
     * Get entity value id
     *
     * @param Varien_Object $entity
     * @return int
     */
    public function getEntityValueId($entity)
    {
        if (!$entity || !$entity->getId() || !array_key_exists($entity->getId(), $this->_valueIds)) {
            return $this->getValueId();
        }

        return $this->_valueIds[$entity->getId()];
    }

    /**
     * Retrieve default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->_defaultValue === null) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->_defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->_defaultValue = '';
            }
        }

        return $this->_defaultValue;
    }

    /**
     * Validate object
     *
     * @param Varien_Object $object
     * @return $this|bool
     * @throws Mage_Core_Exception|Mage_Eav_Exception
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($this->getAttribute()->getIsRequired() && $this->getAttribute()->isValueEmpty($value)) {
            return false;
        }

        //Validate serialized data
        if (!Mage::helper('core/string')->validateSerializedObject($value)) {
            $label = $this->getAttribute()->getFrontend()->getLabel();
            throw Mage::exception(
                'Mage_Eav',
                Mage::helper('eav')->__('The value of attribute "%s" contains invalid data.', $label),
            );
        }

        if ($this->getAttribute()->getIsUnique()
            && !$this->getAttribute()->getIsRequired()
            && ($value == '' || $this->getAttribute()->isValueEmpty($value))
        ) {
            return true;
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                throw Mage::exception(
                    'Mage_Eav',
                    Mage::helper('eav')->__('The value of attribute "%s" must be unique', $label),
                );
            }
        }

        return true;
    }

    /**
     * After load method
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        return $this;
    }

    /**
     * Before save method
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if (!$object->hasData($attrCode) && $this->getDefaultValue()) {
            $object->setData($attrCode, $this->getDefaultValue());
        }

        return $this;
    }

    /**
     * After save method
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterSave($object)
    {
        return $this;
    }

    /**
     * Before delete method
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeDelete($object)
    {
        return $this;
    }

    /**
     * After delete method
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function afterDelete($object)
    {
        return $this;
    }
}
