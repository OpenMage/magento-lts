<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product attribute api
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * Product entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        $this->_ignoredAttributeCodes[] = 'type_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
    }

    /**
     * Retrieve attributes from specified attribute set
     *
     * @param int $setId
     * @return array
     */
    public function items($setId)
    {
        $attributes = Mage::getModel('catalog/product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);
        $result = [];

        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if ((!$attribute->getId() || $attribute->isInSet($setId))
                    && $this->_isAllowedAttribute($attribute)
            ) {
                if (!$attribute->getId() || $attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = [
                    'attribute_id' => $attribute->getId(),
                    'code' => $attribute->getAttributeCode(),
                    'type' => $attribute->getFrontendInput(),
                    'required' => $attribute->getIsRequired(),
                    'scope' => $scope
                ];
            }
        }

        return $result;
    }

    /**
     * Retrieve attribute options
     *
     * @param int $attributeId
     * @param string|int $store
     * @return array
     */
    public function options($attributeId, $store = null)
    {
        $storeId = $this->_getStoreId($store);
        $attribute = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->getResource()
                ->getAttribute($attributeId);

        /** @var Mage_Catalog_Model_Entity_Attribute $attribute */
        if (!$attribute) {
            $this->_fault('not_exists');
        }
        $options = [];
        if ($attribute->usesSource()) {
            $attribute->setStoreId($storeId);
            foreach ($attribute->getSource()->getAllOptions() as $optionId => $optionValue) {
                if (is_array($optionValue)) {
                    $options[] = $optionValue;
                } else {
                    $options[] = [
                        'value' => $optionId,
                        'label' => $optionValue
                    ];
                }
            }
        }

        return $options;
    }

    /**
     * Retrieve list of possible attribute types
     *
     * @return array
     */
    public function types()
    {
        return Mage::getModel('catalog/product_attribute_source_inputtype')->toOptionArray();
    }

    /**
     * Create new product attribute
     *
     * @param array $data input data
     * @return int
     */
    public function create($data)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $model */
        $model = Mage::getModel('catalog/resource_eav_attribute');
        /** @var Mage_Catalog_Helper_Product $helper */
        $helper = Mage::helper('catalog/product');

        if (empty($data['attribute_code']) || (isset($data['frontend_label']) && !is_array($data['frontend_label']))) {
            $this->_fault('invalid_parameters');
        }

        //validate attribute_code
        if (!preg_match('/^[a-z][a-z_0-9]{0,254}$/', $data['attribute_code'])) {
            $this->_fault('invalid_code');
        }

        //validate frontend_input
        $allowedTypes = [];
        foreach ($this->types() as $type) {
            $allowedTypes[] = $type['value'];
        }
        if (!in_array($data['frontend_input'], $allowedTypes)) {
            $this->_fault('invalid_frontend_input');
        }

        $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
        $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
        if (!$model->getBackendType() && (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0)) {
            $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
        }

        $this->_prepareDataForSave($data);

        $model->addData($data);
        $model->setEntityTypeId($this->_entityTypeId);
        $model->setIsUserDefined(1);

        try {
            $model->save();
            // clear translation cache because attribute labels are stored in translation
            Mage::app()->cleanCache([Mage_Core_Model_Translate::CACHE_TAG]);
        } catch (Exception $e) {
            $this->_fault('unable_to_save', $e->getMessage());
        }

        return (int) $model->getId();
    }

    /**
     * Update product attribute
     *
     * @param string|int $attribute attribute code or ID
     * @param array $data
     * @return bool
     */
    public function update($attribute, $data)
    {
        $model = $this->_getAttribute($attribute);

        if ($model->getEntityTypeId() != $this->_entityTypeId) {
            $this->_fault('can_not_edit');
        }

        $data['attribute_code'] = $model->getAttributeCode();
        $data['is_user_defined'] = $model->getIsUserDefined();
        $data['frontend_input'] = $model->getFrontendInput();

        $this->_prepareDataForSave($data);

        $model->addData($data);
        try {
            $model->save();
            // clear translation cache because attribute labels are stored in translation
            Mage::app()->cleanCache([Mage_Core_Model_Translate::CACHE_TAG]);
        } catch (Exception $e) {
            $this->_fault('unable_to_save', $e->getMessage());
        }
        return true;
    }

    /**
     * Remove attribute
     *
     * @param int|string $attribute attribute ID or code
     * @return true|void
     */
    public function remove($attribute)
    {
        $model = $this->_getAttribute($attribute);

        if ($model->getEntityTypeId() != $this->_entityTypeId) {
            $this->_fault('can_not_delete');
        }

        if (!$model->getIsUserDefined()) {
            $this->_fault('can_not_delete');
        }

        try {
            $model->delete();
            return true;
        } catch (Exception $e) {
            $this->_fault('can_not_delete', $e->getMessage());
        }
    }

    /**
     * Get full information about attribute with list of options
     *
     * @param int|string $attribute attribute ID or code
     * @return array
     */
    public function info($attribute)
    {
        $model = $this->_getAttribute($attribute);

        if ($model->isScopeGlobal()) {
            $scope = 'global';
        } elseif ($model->isScopeWebsite()) {
            $scope = 'website';
        } else {
            $scope = 'store';
        }

        $frontendLabels = [
            [
                'store_id' => 0,
                'label' => $model->getFrontendLabel()
            ]
        ];
        foreach ($model->getStoreLabels() as $storeId => $label) {
            $frontendLabels[] = [
                'store_id' => $storeId,
                'label' => $label
            ];
        }

        $result = [
            'attribute_id' => $model->getId(),
            'attribute_code' => $model->getAttributeCode(),
            'frontend_input' => $model->getFrontendInput(),
            'default_value' => $model->getDefaultValue(),
            'is_unique' => $model->getIsUnique(),
            'is_required' => $model->getIsRequired(),
            'apply_to' => $model->getApplyTo(),
            'is_configurable' => $model->getIsConfigurable(),
            'is_searchable' => $model->getIsSearchable(),
            'is_visible_in_advanced_search' => $model->getIsVisibleInAdvancedSearch(),
            'is_comparable' => $model->getIsComparable(),
            'is_used_for_promo_rules' => $model->getIsUsedForPromoRules(),
            'is_visible_on_front' => $model->getIsVisibleOnFront(),
            'used_in_product_listing' => $model->getUsedInProductListing(),
            'frontend_label' => $frontendLabels
        ];
        if ($model->getFrontendInput() != 'price') {
            $result['scope'] = $scope;
        }

        // set additional fields to different types
        switch ($model->getFrontendInput()) {
            case 'text':
                $result['additional_fields'] = [
                    'frontend_class' => $model->getFrontendClass(),
                    'is_html_allowed_on_front' => $model->getIsHtmlAllowedOnFront(),
                    'used_for_sort_by' => $model->getUsedForSortBy()
                ];
                break;
            case 'textarea':
                $result['additional_fields'] = [
                    'is_wysiwyg_enabled' => $model->getIsWysiwygEnabled(),
                    'is_html_allowed_on_front' => $model->getIsHtmlAllowedOnFront(),
                ];
                break;
            case 'date':
            case 'boolean':
                $result['additional_fields'] = [
                    'used_for_sort_by' => $model->getUsedForSortBy()
                ];
                break;
            case 'multiselect':
                $result['additional_fields'] = [
                    'is_filterable' => $model->getIsFilterable(),
                    'is_filterable_in_search' => $model->getIsFilterableInSearch(),
                    'position' => $model->getPosition()
                ];
                break;
            case 'select':
            case 'price':
                $result['additional_fields'] = [
                    'is_filterable' => $model->getIsFilterable(),
                    'is_filterable_in_search' => $model->getIsFilterableInSearch(),
                    'position' => $model->getPosition(),
                    'used_for_sort_by' => $model->getUsedForSortBy()
                ];
                break;
            default:
                $result['additional_fields'] = [];
                break;
        }

        // set options
        $options = $this->options($model->getId());
        // remove empty first element
        if ($model->getFrontendInput() != 'boolean') {
            array_shift($options);
        }

        if (count($options) > 0) {
            $result['options'] = $options;
        }

        return $result;
    }

    /**
     * Add option to select or multiselect attribute
     *
     * @param  int|string $attribute attribute ID or code
     * @param  array $data
     * @return bool
     */
    public function addOption($attribute, $data)
    {
        $model = $this->_getAttribute($attribute);

        if (!$model->usesSource()) {
            $this->_fault('invalid_frontend_input');
        }

        /** @var Mage_Catalog_Helper_Data $helperCatalog */
        $helperCatalog = Mage::helper('catalog');

        $optionLabels = [];
        foreach ($data['label'] as $label) {
            $storeId = $label['store_id'];
            $labelText = $helperCatalog->stripTags($label['value']);
            if (is_array($storeId)) {
                foreach ($storeId as $multiStoreId) {
                    $optionLabels[$multiStoreId] = $labelText;
                }
            } else {
                $optionLabels[$storeId] = $labelText;
            }
        }
        // data in the following format is accepted by the model
        // it simulates parameters of the request made to
        // Mage_Adminhtml_Catalog_Product_AttributeController::saveAction()
        $modelData = [
            'option' => [
                'value' => [
                    'option_1' => $optionLabels
                ],
                'order' => [
                    'option_1' => (int) $data['order']
                ]
            ]
        ];
        if ($data['is_default']) {
            $modelData['default'][] = 'option_1';
        }

        $model->addData($modelData);
        try {
            $model->save();
        } catch (Exception $e) {
            $this->_fault('unable_to_add_option', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove option from select or multiselect attribute
     *
     * @param  int|string $attribute attribute ID or code
     * @param  int $optionId option to remove ID
     * @return bool
     */
    public function removeOption($attribute, $optionId)
    {
        $model = $this->_getAttribute($attribute);

        if (!$model->usesSource()) {
            $this->_fault('invalid_frontend_input');
        }

        // data in the following format is accepted by the model
        // it simulates parameters of the request made to
        // Mage_Adminhtml_Catalog_Product_AttributeController::saveAction()
        $modelData = [
            'option' => [
                'value' => [
                    $optionId => []
                ],
                'delete' => [
                    $optionId => '1'
                ]
            ]
        ];
        $model->addData($modelData);
        try {
            $model->save();
        } catch (Exception $e) {
            $this->_fault('unable_to_remove_option', $e->getMessage());
        }

        return true;
    }

    /**
     * Prepare request input data for saving
     *
     * @param array $data input data
     */
    protected function _prepareDataForSave(&$data)
    {
        /** @var Mage_Catalog_Helper_Data $helperCatalog */
        $helperCatalog = Mage::helper('catalog');

        if ($data['scope'] == 'global') {
            $data['is_global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        } elseif ($data['scope'] == 'website') {
            $data['is_global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE;
        } else {
            $data['is_global'] = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
        }
        if (!isset($data['is_configurable'])) {
            $data['is_configurable'] = 0;
        }
        if (!isset($data['is_filterable'])) {
            $data['is_filterable'] = 0;
        }
        if (!isset($data['is_filterable_in_search'])) {
            $data['is_filterable_in_search'] = 0;
        }
        if (!isset($data['apply_to'])) {
            $data['apply_to'] = [];
        }
        // set frontend labels array with store_id as keys
        if (isset($data['frontend_label']) && is_array($data['frontend_label'])) {
            $labels = [];
            foreach ($data['frontend_label'] as $label) {
                $storeId = $label['store_id'];
                $labelText = $helperCatalog->stripTags($label['label']);
                $labels[$storeId] = $labelText;
            }
            $data['frontend_label'] = $labels;
        }
        // set additional fields
        if (isset($data['additional_fields']) && is_array($data['additional_fields'])) {
            $data = array_merge($data, $data['additional_fields']);
            unset($data['additional_fields']);
        }
        //default value
        if (!empty($data['default_value'])) {
            $data['default_value'] = $helperCatalog->stripTags($data['default_value']);
        }
    }

    /**
     * Load model by attribute ID or code
     *
     * @param int|string $attribute
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attribute)
    {
        $model = Mage::getResourceModel('catalog/eav_attribute')
            ->setEntityTypeId($this->_entityTypeId);

        if (is_numeric($attribute)) {
            $model->load((int) $attribute);
        } else {
            $model->load($attribute, 'attribute_code');
        }

        if (!$model->getId()) {
            $this->_fault('not_exists');
        }

        return $model;
    }
}
