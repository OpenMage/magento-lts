<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import entity abstract product type model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
{
    /**
     * Product type attribute sets and attributes parameters.
     *
     * [attr_set_name_1] => array(
     *     [attr_code_1] => array(
     *         'options' => array(),
     *         'type' => 'text', 'price', 'textarea', 'select', etc.
     *         'id' => ..
     *     ),
     *     ...
     * ),
     * ...
     *
     * @var array
     */
    protected $_attributes = [];

    /**
     * Attributes' codes which will be allowed anyway, independently from its visibility property.
     *
     * @var array
     */
    protected $_forcedAttributesCodes = [];

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = [];

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [];

    /**
     * Column names that holds values with particular meaning.
     *
     * @var array
     */
    protected $_particularAttributes = [];

    /**
     * Product entity object.
     *
     * @var Mage_ImportExport_Model_Import_Entity_Product
     */
    protected $_entityModel;

    /**
     * Product type (simple, configurable, etc.).
     *
     * @var string
     */
    protected $_type;

    /**
     * Object constructor.
     *
     * @param array $params
     * @throws Exception
     */
    final public function __construct(array $params)
    {
        if ($this->isSuitable()) {
            if (!isset($params[0]) || !isset($params[1])
                || !is_object($params[0]) || !($params[0] instanceof Mage_ImportExport_Model_Import_Entity_Product)) {
                Mage::throwException(Mage::helper('importexport')->__('Invalid parameters'));
            }
            $this->_entityModel = $params[0];
            $this->_type        = $params[1];

            foreach ($this->_messageTemplates as $errorCode => $message) {
                $this->_entityModel->addMessageTemplate($errorCode, $message);
            }

            if (isset($params['attributes'])) {
                $this->_attributes = $params['attributes'];
            } else {
                $this->_initAttributes();
            }
        }
    }

    /**
     * Add attribute parameters to appropriate attribute set.
     *
     * @param string $attrSetName Name of attribute set.
     * @param array $attrParams Refined attribute parameters.
     * @return Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
     */
    protected function _addAttributeParams($attrSetName, array $attrParams)
    {
        if (!$attrParams['apply_to'] || in_array($this->_type, $attrParams['apply_to'])) {
            $this->_attributes[$attrSetName][$attrParams['code']] = $attrParams;
        }
        return $this;
    }

    /**
     * Return product attributes for its attribute set specified in row data.
     *
     * @param array|string $attrSetData Product row data or simply attribute set name.
     * @return array
     */
    protected function _getProductAttributes($attrSetData)
    {
        if (is_array($attrSetData)) {
            return $this->_attributes[$attrSetData[Mage_ImportExport_Model_Import_Entity_Product::COL_ATTR_SET]];
        } else {
            return $this->_attributes[$attrSetData];
        }
    }

    /**
     * Initialize attributes parameters for all attributes' sets.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
     */
    protected function _initAttributes()
    {
        // temporary storage for attributes' parameters to avoid double querying inside the loop
        $attributesCache = [];

        foreach (Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($this->_entityModel->getEntityTypeId()) as $attributeSet) {
            foreach (Mage::getResourceModel('catalog/product_attribute_collection')
                ->setAttributeSetFilter($attributeSet->getId()) as $attribute) {
                /** @var Mage_Eav_Model_Entity_Attribute $attribute */
                $attributeCode = $attribute->getAttributeCode();
                $attributeId   = $attribute->getId();

                if ($attribute->getIsVisible() || in_array($attributeCode, $this->_forcedAttributesCodes)) {
                    if (!isset($attributesCache[$attributeId])) {
                        $attributesCache[$attributeId] = [
                            'id'               => $attributeId,
                            'code'             => $attributeCode,
                            'for_configurable' => $attribute->getIsConfigurable(),
                            'is_global'        => $attribute->getIsGlobal(),
                            'is_required'      => $attribute->getIsRequired(),
                            'is_unique'        => $attribute->getIsUnique(),
                            'frontend_label'   => $attribute->getFrontendLabel(),
                            'is_static'        => $attribute->isStatic(),
                            'apply_to'         => $attribute->getApplyTo(),
                            'type'             => Mage_ImportExport_Model_Import::getAttributeType($attribute),
                            'default_value'    => strlen($attribute->getDefaultValue())
                                                  ? $attribute->getDefaultValue() : null,
                            'options'          => $this->_entityModel
                                                      ->getAttributeOptions($attribute, $this->_indexValueAttributes)
                        ];
                    }
                    $this->_addAttributeParams($attributeSet->getAttributeSetName(), $attributesCache[$attributeId]);
                }
            }
        }
        return $this;
    }

    /**
     * Have we check attribute for is_required? Used as last chance to disable this type of check.
     *
     * @param string $attrCode
     * @return bool
     */
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        return true;
    }

    /**
     * Validate particular attributes columns.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    protected function _isParticularAttributesValid(array $rowData, $rowNum)
    {
        return true;
    }

    /**
     * Check price correction value validity (signed integer or float with or without percentage sign).
     *
     * @param string $value
     * @return int
     */
    protected function _isPriceCorr($value)
    {
        return preg_match('/^-?\d+\.?\d*%?$/', $value);
    }

    /**
     * Particular attribute names getter.
     *
     * @return array
     */
    public function getParticularAttributes()
    {
        return $this->_particularAttributes;
    }

    /**
     * Validate row attributes. Pass VALID row data ONLY as argument.
     *
     * @param array $rowData
     * @param int $rowNum
     * @param bool $isNewProduct OPTIONAL.
     * @return bool
     */
    public function isRowValid(array $rowData, $rowNum, $isNewProduct = true)
    {
        $error    = false;
        $rowScope = $this->_entityModel->getRowScope($rowData);

        if (Mage_ImportExport_Model_Import_Entity_Product::SCOPE_NULL != $rowScope) {
            foreach ($this->_getProductAttributes($rowData) as $attrCode => $attrParams) {
                // check value for non-empty in the case of required attribute?
                if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                    $error |= !$this->_entityModel->isAttributeValid($attrCode, $attrParams, $rowData, $rowNum);
                } elseif ($this->_isAttributeRequiredCheckNeeded($attrCode)
                    && $attrParams['is_required']) {
                    // For the default scope - if this is a new product or
                    // for an old product, if the imported doc has the column present for the attrCode
                    if (Mage_ImportExport_Model_Import_Entity_Product::SCOPE_DEFAULT == $rowScope &&
                            ($isNewProduct || array_key_exists($attrCode, $rowData))) {
                        $this->_entityModel->addRowError(
                            Mage_ImportExport_Model_Import_Entity_Product::ERROR_VALUE_IS_REQUIRED,
                            $rowNum,
                            $attrCode
                        );
                        $error = true;
                    }
                }
            }
        }
        $error |= !$this->_isParticularAttributesValid($rowData, $rowNum);

        return !$error;
    }

    /**
     * Additional check for model availability. If method returns FALSE - model is not suitable for data processing.
     *
     * @return bool
     */
    public function isSuitable()
    {
        return true;
    }

    /**
     * Prepare attributes values for save: remove non-existent, remove empty values, remove static.
     *
     * @param array $rowData
     * @param bool $withDefaultValue
     * @return array
     */
    public function prepareAttributesForSave(array $rowData, $withDefaultValue = true)
    {
        $resultAttrs = [];

        foreach ($this->_getProductAttributes($rowData) as $attrCode => $attrParams) {
            if (!$attrParams['is_static']) {
                if (isset($rowData[$attrCode]) && strlen($rowData[$attrCode])) {
                    $resultAttrs[$attrCode] =
                        ($attrParams['type'] == 'select' || $attrParams['type'] == 'multiselect')
                        ? $attrParams['options'][strtolower($rowData[$attrCode])]
                        : $rowData[$attrCode];
                } elseif ($withDefaultValue && $attrParams['default_value'] !== null) {
                    $resultAttrs[$attrCode] = $attrParams['default_value'];
                }
            }
        }
        return $resultAttrs;
    }

    /**
     * Save product type specific data.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product_Type_Abstract
     */
    public function saveData()
    {
        return $this;
    }
}
