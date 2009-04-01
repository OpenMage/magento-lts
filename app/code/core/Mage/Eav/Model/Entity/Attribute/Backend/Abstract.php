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
 * Entity/Attribute/Model - attribute backend abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @var integer
     */
    protected $_valueId;

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
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
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
     * @return boolean
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
                $this->_table = $entity->getValueTablePrefix()
                    .'_'.$this->getType();
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

    public function setValueId($valueId)
    {
        $this->_valueId = $valueId;
        return $this;
    }

    public function getValueId()
    {
        return $this->_valueId;
    }

    public function getDefaultValue()
    {
        if (is_null($this->_defaultValue)) {
            if ($this->getAttribute()->getDefaultValue()) {
                $this->_defaultValue = $this->getAttribute()->getDefaultValue();
            } else {
                $this->_defaultValue = "";
            }
        }
        return $this->_defaultValue;
    }

    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($this->getAttribute()->getIsRequired() && !$object->getData($attrCode)) {
            return false;
        }
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if ($this->getAttribute()->getIsUnique() && !$this->getAttribute()->getIsRequired() && ($value == '' || $this->getAttribute()->isValueEmpty($value))) {
            return true;
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                Mage::throwException(Mage::helper('eav')->__('Value of attribute "%s" must be unique', $label));
            }
        }
        return true;
    }

    public function afterLoad($object)
    {

    }

    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if (!$object->hasData($attrCode) && $this->getDefaultValue()) {
            $object->setData($attrCode, $this->getDefaultValue());
        }
    }

    public function afterSave($object)
    {

    }

    public function beforeDelete($object)
    {

    }

    public function afterDelete($object)
    {

    }
}
