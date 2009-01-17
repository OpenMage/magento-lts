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
 * Entity/Attribute/Model - attribute abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Entity_Attribute_Abstract
    extends Mage_Core_Model_Abstract
    implements Mage_Eav_Model_Entity_Attribute_Interface
{
    /**
     * Attribute name
     *
     * @var string
     */
    protected $_name;

    /**
     * Entity instance
     *
     * @var Mage_Eav_Model_Entity_Abstract
     */
    protected $_entity;

    /**
     * Backend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    protected $_backend;

    /**
     * Frontend instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    protected $_frontend;

    /**
     * Source instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    protected $_source;

    /**
     * Attribute id cache
     *
     * @var array
     */
    protected $_attributeIdCache = array();

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('eav/entity_attribute');
    }

    /**
     * Load attribute data by code
     *
     * @param   mixed $entityType
     * @param   string $code
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function loadByCode($entityType, $code)
    {
        if (is_numeric($entityType)) {
            $entityTypeId = $entityType;
        } elseif (is_string($entityType)) {
            $entityType = Mage::getModel('eav/entity_type')->loadByCode($entityType);
        }
        if ($entityType instanceof Mage_Eav_Model_Entity_Type) {
            $entityTypeId = $entityType->getId();
        }
        if (empty($entityTypeId)) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid entity supplied'));
        }
        $this->_getResource()->loadByCode($this, $entityTypeId, $code);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Retrieve attribute configuration (deprecated)
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getConfig()
    {
        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getName()
    {
        return (isset($this->_data['attribute_code'])) ? $this->_data['attribute_code'] : null;
    }

    public function setAttributeId($data)
    {
        $this->_data['attribute_id'] = $data;
        return $this;
    }

    public function getAttributeId()
    {
        return (isset($this->_data['attribute_id'])) ? $this->_data['attribute_id'] : null;
    }

    public function setAttributeCode($data)
    {
        return $this->setData('attribute_code', $data);
    }

    public function getAttributeCode()
    {
        return (isset($this->_data['attribute_code'])) ? $this->_data['attribute_code'] : null;
    }

    public function setAttributeModel($data)
    {
        return $this->setData('attribute_model', $data);
    }

    public function getAttributeModel()
    {
        return (isset($this->_data['attribute_model'])) ? $this->_data['attribute_model'] : null;
    }

    public function setBackendType($data)
    {
        return $this->setData('backend_type', $data);
    }

    public function getBackendType()
    {
        return (isset($this->_data['backend_type'])) ? $this->_data['backend_type'] : null;
    }

    public function setBackendModel($data)
    {
        return $this->setData('backend_model', $data);
    }

    public function getBackendModel()
    {
        return (isset($this->_data['backend_model'])) ? $this->_data['backend_model'] : null;
    }

    public function setBackendTable($data)
    {
        return $this->setData('backend_table', $data);
    }

    public function getBackendTable()
    {
        return (isset($this->_data['backend_table'])) ? $this->_data['backend_table'] : null;
    }

    public function getIsVisibleOnFront()
    {
        return (isset($this->_data['is_visible_on_front'])) ? $this->_data['is_visible_on_front'] : null;
    }

    public function getDefaultValue()
    {
        return (isset($this->_data['default_value'])) ? $this->_data['default_value'] : null;
    }

    public function getAttributeSetId()
    {
        return isset($this->_data['attribute_set_id'] ) ? $this->_data['attribute_set_id'] : null;
    }

    public function setAttributeSetId($id)
    {
        $this->_data['attribute_set_id'] = $id;
        return $this;
    }

    public function getEntityTypeId()
    {
        return $this->getData('entity_type_id');
    }

    public function setEntityTypeId($id)
    {
        $this->_data['entity_type_id'] = $id;
        return $this;
    }

    public function setEntityType($type)
    {
        $this->setData('entity_type', $type);
        return $this;
    }

    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Get attribute alias as "entity_type/attribute_code"
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity exclude this entity
     * @return string
     */
    public function getAlias($entity=null)
    {
        $alias = '';
        if (is_null($entity) || ($entity->getType() !== $this->getEntity()->getType())) {
            $alias .= $this->getEntity()->getType() . '/';
        }
        $alias .= $this->getAttributeCode();
        return  $alias;
    }

    /**
     * Set attribute name
     *
     * @param   string $name
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setName($name)
    {
        return $this->setData('attribute_code', $name);
    }

    public function getEntityType()
    {
        return Mage::getSingleton('eav/config')->getEntityType($this->getEntityTypeId());
    }

    /**
     * Set attribute entity instance
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Retrieve entity instance
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getEntity()
    {
        if (!$this->_entity) {
            $this->_entity = $this->getEntityType();
        }
        return $this->_entity;
    }

    public function getEntityIdField()
    {
        return $this->getEntity()->getValueEntityIdField();
    }

    /**
     * Retrieve backend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function getBackend()
    {
        if (empty($this->_backend)) {
            if (!$this->getBackendModel()) {
                $this->setBackendModel($this->_getDefaultBackendModel());
            }
            $backend = Mage::getModel($this->getBackendModel());
            if (!$backend) {
                throw Mage::exception('Mage_Eav', 'Invalid backend model specified: '.$this->getBackendModel());
            }
            $this->_backend = $backend->setAttribute($this);
        }
        return $this->_backend;
    }

    /**
     * Retrieve frontend instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function getFrontend()
    {
        if (empty($this->_frontend)) {
            if (!$this->getFrontendModel()) {
                $this->setFrontendModel($this->_getDefaultFrontendModel());
            }
            $this->_frontend = Mage::getModel($this->getFrontendModel())
                ->setAttribute($this);
        }
        return $this->_frontend;
    }

    /**
     * Retrieve source instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSource()
    {
        if (empty($this->_source)) {
            if (!$this->getSourceModel()) {
                $this->setSourceModel($this->_getDefaultSourceModel());
            }
            $this->_source = Mage::getModel($this->getSourceModel())
                ->setAttribute($this);
        }
        return $this->_source;
    }

    public function usesSource()
    {
        return $this->getFrontendInput()==='select' || $this->getFrontendInput()==='multiselect';
    }

    protected function _getDefaultBackendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_BACKEND_MODEL;
    }

    protected function _getDefaultFrontendModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_FRONTEND_MODEL;
    }

    protected function _getDefaultSourceModel()
    {
        return $this->getEntity()->getDefaultAttributeSourceModel();
    }

    public function isValueEmpty($value)
    {
        $attrType = $this->getBackend()->getType();
        $isEmpty = is_array($value)
            || is_null($value)
            || $value===false && $attrType!='int'
            || $value==='' && ($attrType=='int' || $attrType=='decimal' || $attrType=='datetime');
        return $isEmpty;
    }

    /**
     * Check if attribute in specified set
     *
     * @param int|array $setId
     * @return boolean
     */
    public function isInSet($setId)
    {
        if (!$this->hasAttributeSetInfo()) {
            return true;
        }

        if (is_array($setId)
            && count(array_intersect($setId, array_keys($this->getAttributeSetInfo())))) {
            return true;
        }

        if (!is_array($setId)
            && array_key_exists($setId, $this->getAttributeSetInfo())) {
            return true;
        }

        return false;
    }

    /**
     * Check if attribute in specified group
     *
     * @param int $setId
     * @param int $groupId
     * @return boolean
     */
    public function isInGroup($setId, $groupId)
    {
        if ($this->isInSet($setId) && $this->getData('attribute_set_info/' . $setId . '/group_id') == $groupId) {
            return true;
        }

        return false;
    }

    /**
     * Return attribute id
     *
     * @param string $entityType
     * @param string $code
     * @return int
     */
    public function getIdByCode($entityType, $code)
    {
        $k = "{$entityType}|{$code}";
        if (!isset($this->_attributeIdCache[$k])) {
            $this->_attributeIdCache[$k] = $this->getResource()->getIdByCode($entityType, $code);
        }
        return $this->_attributeIdCache[$k];
    }
}
