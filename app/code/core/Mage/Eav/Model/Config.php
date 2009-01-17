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


class Mage_Eav_Model_Config
{
    /**
     * Array data loaded from cache
     *
     * @var array
     */
    protected $_data;
    protected $_objects;
    protected $_references;

    /**
     * Reset object state
     *
     * @return Mage_Eav_Model_Config
     */
    public function clear()
    {
        $this->_data        = null;
        $this->_objects     = null;
        $this->_references  = null;
        return $this;
    }

    protected function _load($id)
    {
        if (isset($this->_references[$id])) {
            $id = $this->_references[$id];
        }
        return isset($this->_objects[$id]) ? $this->_objects[$id] : null;
    }

    protected function _save($obj, $id)
    {
        $this->_objects[$id] = $obj;
        return $this;
    }

    protected function _reference($ref, $id)
    {
        $this->_references[$ref] = $id;
        return $this;
    }

    /**
     * Initialize all entity types data
     *
     * @return Mage_Eav_Model_Config
     */
    protected function _initEntityTypes()
    {
        if (isset($this->_data)) {
            return $this;
        }


        $useCache = Mage::app()->useCache('eav');
        if ($useCache && $cache = Mage::app()->loadCache('EAV_ENTITY_TYPE_CODES')) {
            $this->_data['entity_type_codes'] = unserialize($cache);

            return $this;
        }
        Varien_Profiler::start('EAV: '.__METHOD__);

        $entityTypes = Mage::getModel('eav/entity_type')->getCollection();

        $codes = array();
        foreach ($entityTypes as $id=>$type) {
            if (!$type->getAttributeModel()) {
                $type->setAttributeModel('eav/entity_attribute');
            }

            $code = $type->getEntityTypeCode();
            $this->_save($type, 'EAV_ENTITY_TYPE/'.$code);
            $this->_reference('EAV_ENTITY_TYPE/'.$id, 'EAV_ENTITY_TYPE/'.$code);

            $codes[$id] = $code;
            if ($useCache) {
                Mage::app()->saveCache(serialize($type->getData()), 'EAV_ENTITY_TYPE_'.$code,
                    array('eav', Mage_Eav_Model_Entity_Attribute::CACHE_TAG)
                );
            }
        }

        $this->_data['entity_type_codes'] = $codes;
        if ($useCache) {
            Mage::app()->saveCache(serialize($this->_data['entity_type_codes']), 'EAV_ENTITY_TYPE_CODES',
                array('eav', Mage_Eav_Model_Entity_Attribute::CACHE_TAG)
            );
        }

        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $this;
    }

    /**
     * Retrieve entity type object by entity type code
     *
     * @param   mixed $code
     * @return  Mage_Eav_Model_Entity_Type
     */
    public function getEntityType($code)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Type) {
            return $code;
        }

        $this->_initEntityTypes();

        if ($entityType = $this->_load('EAV_ENTITY_TYPE/'.$code)) {
            return $entityType;
        }

        Varien_Profiler::start('EAV: '.__METHOD__);

        if (is_numeric($code)) {
            if (isset($this->_data['entity_type_codes'][$code])) {
                $code = $this->_data['entity_type_codes'][$code];
            } else {
                Mage::throwException(Mage::helper('eav')->__('Invalid entity_type specified: %s', $code));
            }
        }

        $entityType = Mage::getModel('eav/entity_type');
        if ($cache = Mage::app()->loadCache('EAV_ENTITY_TYPE_'.$code)) {
            $entityType->setData(unserialize($cache));
        }
        else {
            $entityType->loadByCode($code);
            if (!$entityType->getId()) {
                Mage::throwException(Mage::helper('eav')->__('Invalid entity_type specified: %s', $code));
            }
        }

        $this->_save($entityType, 'EAV_ENTITY_TYPE/'.$code);

        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $entityType;
    }

    /**
     * Initialize all attributes for entity type
     *
     * @param   string $entityType
     * @return  Mage_Eav_Model_Config
     */
    protected function _initAttributes($entityType)
    {
        $entityType     = $this->getEntityType($entityType);
        $entityTypeCode = $entityType->getEntityTypeCode();

        if (isset($this->_data['attributes'][$entityTypeCode]) || $entityType->getAttributeCodes()) {
            return $this;
        }
        Varien_Profiler::start('EAV: '.__METHOD__);

        $useCache = Mage::app()->useCache('eav');

        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityType->getId())
            ->addSetInfo();

        $defaultAttributeModel = $entityType->getAttributeModel();
        $codes = array();
        $attributesData = array();
        foreach ($attributes as $attribute) {
            if (!$attribute->getAttributeModel()) {
                $attribute->setAttributeModel($defaultAttributeModel);
            }

            if ($attribute->getAttributeModel()!=='eav/entity_attribute') {
                $attribute = Mage::getModel($attribute->getAttributeModel(), $attribute->getData());
            }

            $code = $attribute->getAttributeCode();
            $this->_save($attribute, 'EAV_ATTRIBUTE/'.$entityTypeCode.'/'.$code);
            $this->_reference($attribute->getId(), $code);
            $codes[$attribute->getId()] = $code;
            if ($useCache) {
                Mage::app()->saveCache(serialize($attribute->getData()), 'EAV_ATTRIBUTE_'.$entityTypeCode.'__'.$code,
                    array('eav', Mage_Eav_Model_Entity_Attribute::CACHE_TAG)
                );
            }
        }

        $entityType->setAttributeCodes($codes);
        if ($useCache) {
            $data = $entityType->getData();
            Mage::app()->saveCache(serialize($data), 'EAV_ENTITY_TYPE_'.$entityTypeCode,
                array('eav', Mage_Eav_Model_Entity_Attribute::CACHE_TAG)
            );
        }
        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $this;
    }

    /**
     * Get attribute by code for entity type
     *
     * @param   mixed $entityType
     * @param   mixed $code
     * @return  Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($entityType, $code)
    {
        if ($code instanceof Mage_Eav_Model_Entity_Attribute_Interface) {
            return $code;
        }

        Varien_Profiler::start('EAV: '.__METHOD__);

        $this->_initAttributes($entityType);

        $entityTypeCode = $this->getEntityType($entityType)->getEntityTypeCode();
        $entityType     = $this->getEntityType($entityType);
        $attrCodes      = $entityType->getAttributeCodes();

        /**
         * Validate attribute code
         */
        if (is_numeric($code)) {
            if (isset($attrCodes[$code])) {
                $code = $attrCodes[$code];
            } else {
                return false;
            }
        }

        /**
         * Try use loaded attribute
         */
        if ($attribute = $this->_load('EAV_ATTRIBUTE/'.$entityTypeCode.'/'.$code)) {
            Varien_Profiler::stop('EAV: '.__METHOD__);
            return $attribute;
        }

        $attribute = false;
        if (in_array($code, $attrCodes)) {
            if ($cache = Mage::app()->loadCache('EAV_ATTRIBUTE_'.$entityTypeCode.'__'.$code)) {
                $data = unserialize($cache);
                if (isset($data['attribute_model'])) {
                    $attribute = Mage::getModel($data['attribute_model'], $data);
                }
            }
            else {
                $attribute = Mage::getModel($entityType->getAttributeModel())->loadByCode($entityTypeCode, $code);
            }
        }

        if ($attribute) {
            $attribute->setEntityType($entityType);
            $this->_save($attribute, 'EAV_ATTRIBUTE/'.$entityTypeCode.'/'.$code);
        }
        Varien_Profiler::stop('EAV: '.__METHOD__);
        return $attribute;
    }

    /**
     * Get codes of all entity type attributes
     *
     * @param   string $entityType
     * @return  array
     */
    public function getEntityAttributeCodes($entityType)
    {
        $this->_initAttributes($entityType);
        return $this->getEntityType($entityType)->getAttributeCodes();
    }
}