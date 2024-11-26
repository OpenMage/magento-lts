<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * EAV Entity Form Model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Model_Form
{
    /**
     * Current module pathname
     *
     * @var string
     */
    protected $_moduleName = '';

    /**
     * Current EAV entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = '';

    /**
     * Current store instance
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store;

    /**
     * Current entity type instance
     *
     * @var Mage_Eav_Model_Entity_Type|null
     */
    protected $_entityType;

    /**
     * Current entity instance
     *
     * @var Mage_Core_Model_Abstract|null
     */
    protected $_entity;

    /**
     * Current form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Array of form attributes
     *
     * @var array|null
     */
    protected $_attributes;

    /**
     * Array of form system attributes
     *
     * @var array|null
     */
    protected $_systemAttributes;

    /**
     * Array of form user defined attributes
     *
     * @var array|null
     */
    protected $_userAttributes;

    /**
     * Is AJAX request flag
     *
     * @var bool
     */
    protected $_isAjax          = false;

    /**
     * Whether the invisible form fields need to be filtered/ignored
     *
     * @var bool
     */
    protected $_ignoreInvisible = true;

    /**
     * Checks correct module choice
     *
     * @throws Mage_Core_Exception
     */
    public function __construct()
    {
        if (empty($this->_moduleName)) {
            Mage::throwException(Mage::helper('eav')->__('Current module pathname is undefined'));
        }
        if (empty($this->_entityTypeCode)) {
            Mage::throwException(Mage::helper('eav')->__('Current module EAV entity is undefined'));
        }
    }

    /**
     * Get EAV Entity Form Attribute Collection
     *
     * @return mixed
     */
    protected function _getFormAttributeCollection()
    {
        return Mage::getResourceModel($this->_moduleName . '/form_attribute_collection');
    }

    /**
     * Set current store
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Set entity instance
     *
     * @return $this
     */
    public function setEntity(Mage_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;
        if ($entity->getEntityTypeId()) {
            $this->setEntityType($entity->getEntityTypeId());
        }
        return $this;
    }

    /**
     * Set entity type instance
     *
     * @param Mage_Eav_Model_Entity_Type|string|int $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($entityType);
        return $this;
    }

    /**
     * Set form code
     *
     * @param string $formCode
     * @return $this
     */
    public function setFormCode($formCode)
    {
        $this->_formCode = $formCode;
        return $this;
    }

    /**
     * Return current store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Return current form code
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getFormCode()
    {
        if (empty($this->_formCode)) {
            Mage::throwException(Mage::helper('eav')->__('Form code is not defined'));
        }
        return $this->_formCode;
    }

    /**
     * Return entity type instance
     * Return EAV entity type if entity type is not defined
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->setEntityType($this->_entityTypeCode);
        }
        return $this->_entityType;
    }

    /**
     * Return current entity instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            Mage::throwException(Mage::helper('eav')->__('Entity instance is not defined'));
        }
        return $this->_entity;
    }

    /**
     * Return array of form attributes
     *
     * @return Mage_Customer_Model_Attribute[]
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            /** @var Mage_Eav_Model_Resource_Form_Attribute_Collection $collection */
            $collection = $this->_getFormAttributeCollection();

            $collection->setStore($this->getStore())
                ->setEntityType($this->getEntityType())
                ->addFormCodeFilter($this->getFormCode())
                ->setSortOrder();

            $this->_attributes      = [];
            $this->_userAttributes  = [];
            foreach ($collection as $attribute) {
                /** @var Mage_Eav_Model_Entity_Attribute $attribute */
                $this->_attributes[$attribute->getAttributeCode()] = $attribute;
                if ($attribute->getIsUserDefined()) {
                    $this->_userAttributes[$attribute->getAttributeCode()] = $attribute;
                } else {
                    $this->_systemAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }
        return $this->_attributes;
    }

    /**
     * Return attribute instance by code or false
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute|false
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (isset($attributes[$attributeCode])) {
            return $attributes[$attributeCode];
        }
        return false;
    }

    /**
     * Return array of form user defined attributes
     *
     * @return array
     */
    public function getUserAttributes()
    {
        if (is_null($this->_userAttributes)) {
            // load attributes
            $this->getAttributes();
        }
        return $this->_userAttributes;
    }

    /**
     * Return array of form system attributes
     *
     * @return array
     */
    public function getSystemAttributes()
    {
        if (is_null($this->_systemAttributes)) {
            // load attributes
            $this->getAttributes();
        }
        return $this->_systemAttributes;
    }

    /**
     * Return attribute data model by attribute
     *
     * @return Mage_Eav_Model_Attribute_Data_Abstract
     */
    protected function _getAttributeDataModel(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $dataModel = Mage_Eav_Model_Attribute_Data::factory($attribute, $this->getEntity());
        $dataModel->setIsAjaxRequest($this->getIsAjaxRequest());

        return $dataModel;
    }

    /**
     * Prepare request with data and returns it
     *
     * @return Zend_Controller_Request_Http
     */
    public function prepareRequest(array $data)
    {
        $request = clone Mage::app()->getRequest();
        $request->setParamSources();
        $request->clearParams();
        $request->setParams($data);

        return $request;
    }

    /**
     * Extract data from request and return associative data array
     *
     * @param string $scope the request scope
     * @param bool $scopeOnly search value only in scope or search value in global too
     * @return array
     */
    public function extractData(Zend_Controller_Request_Http $request, $scope = null, $scopeOnly = true)
    {
        $data = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setRequestScope($scope);
            $dataModel->setRequestScopeOnly($scopeOnly);
            $data[$attribute->getAttributeCode()] = $dataModel->extractValue($request);
        }
        return $data;
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @return bool|array
     */
    public function validateData(array $data)
    {
        $errors = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Compact data array to current entity
     *
     * @return $this
     */
    public function compactData(array $data)
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $dataModel->compactValue($data[$attribute->getAttributeCode()]);
        }

        return $this;
    }

    /**
     * Restore data array from SESSION to current entity
     *
     * @return $this
     */
    public function restoreData(array $data)
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $dataModel->restoreValue($data[$attribute->getAttributeCode()]);
        }
        return $this;
    }

    /**
     * Return array of entity formatted values
     *
     * @param string $format
     * @return array
     */
    public function outputData($format = Mage_Eav_Model_Attribute_Data::OUTPUT_FORMAT_TEXT)
    {
        $data = [];
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            $data[$attribute->getAttributeCode()] = $dataModel->outputValue($format);
        }
        return $data;
    }

    /**
     * Restore entity original data
     *
     * @return $this
     */
    public function resetEntityData()
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $value = $this->getEntity()->getOrigData($attribute->getAttributeCode());
            $this->getEntity()->setData($attribute->getAttributeCode(), $value);
        }
        return $this;
    }

    /**
     * Set is AJAX Request flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsAjaxRequest($flag = true)
    {
        $this->_isAjax = (bool)$flag;
        return $this;
    }

    /**
     * Return is AJAX Request
     *
     * @return bool
     */
    public function getIsAjaxRequest()
    {
        return $this->_isAjax;
    }

    /**
     * Set default attribute values for new entity
     *
     * @return $this
     */
    public function initDefaultValues()
    {
        if (!$this->getEntity()->getId()) {
            foreach ($this->getAttributes() as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default != '') {
                    $this->getEntity()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }
        return $this;
    }

    /**
     * Combined getter/setter whether to omit invisible attributes during rendering/validation
     *
     * @param mixed $setValue
     * @return bool|$this
     */
    public function ignoreInvisible($setValue = null)
    {
        if ($setValue !== null) {
            $this->_ignoreInvisible = (bool)$setValue;
            return $this;
        }
        return $this->_ignoreInvisible;
    }

    /**
     * Whether the specified attribute needs to skip rendering/validation
     *
     * @param Mage_Eav_Model_Attribute $attribute
     * @return bool
     */
    protected function _isAttributeOmitted($attribute)
    {
        if ($this->_ignoreInvisible && !$attribute->getIsVisible()) {
            return true;
        }
        return false;
    }
}
