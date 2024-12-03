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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product options api
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Option_Api extends Mage_Catalog_Model_Api_Resource
{
    /**
     * Add custom option to product
     *
     * @param string $productId
     * @param array $data
     * @param int|string|null $store
     * @return bool $isAdded
     */
    public function add($productId, $data, $store = null)
    {
        $product = $this->_getProduct($productId, $store, null);
        if (!(is_array($data['additional_fields']) && count($data['additional_fields']))) {
            $this->_fault('invalid_data');
        }
        if (!$this->_isTypeAllowed($data['type'])) {
            $this->_fault('invalid_type');
        }
        $this->_prepareAdditionalFields(
            $data,
            $product->getOptionInstance()->getGroupByType($data['type'])
        );
        $this->_saveProductCustomOption($product, $data);
        return true;
    }

    /**
     * Update product custom option data
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($optionId, $data, $store = null)
    {
        /** @var Mage_Catalog_Model_Product_Option $option */
        $option = Mage::getModel('catalog/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        $product = $this->_getProduct($option->getProductId(), $store, null);
        $option = $product->getOptionById($optionId);
        if (isset($data['type']) && !$this->_isTypeAllowed($data['type'])) {
            $this->_fault('invalid_type');
        }
        if (isset($data['additional_fields'])) {
            $this->_prepareAdditionalFields(
                $data,
                $option->getGroupByType()
            );
        }
        foreach ($option->getValues() as $valueId => $value) {
            if (isset($data['values'][$valueId])) {
                $data['values'][$valueId] = array_merge($value->getData(), $data['values'][$valueId]);
            }
        }
        $data = array_merge($option->getData(), $data);
        $this->_saveProductCustomOption($product, $data);
        return true;
    }

    /**
     * Prepare custom option data for saving by model. Used for custom option add and update
     *
     * @param array $data
     * @param string $groupType
     */
    protected function _prepareAdditionalFields(&$data, $groupType)
    {
        if (is_array($data['additional_fields'])) {
            if ($groupType != Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                // reset can be used as there should be the only
                // element in 'additional_fields' for options of all types except those from Select group
                $field = reset($data['additional_fields']);
                if (!(is_array($field) && count($field))) {
                    $this->_fault('invalid_data');
                } else {
                    foreach ($field as $key => $value) {
                        $data[$key] = $value;
                    }
                }
            } else {
                // convert Select rows array to appropriate format for saving in the model
                foreach ($data['additional_fields'] as $row) {
                    if (!(is_array($row) && count($row))) {
                        $this->_fault('invalid_data');
                    } else {
                        foreach ($row as $key => $value) {
                            $row[$key] = Mage::helper('catalog')->stripTags($value);
                        }
                        if (!empty($row['value_id'])) {
                            // map 'value_id' to 'option_type_id'
                            $row['option_type_id'] = $row['value_id'];
                            unset($row['value_id']);
                            $data['values'][$row['option_type_id']] = $row;
                        } else {
                            $data['values'][] = $row;
                        }
                    }
                }
            }
        }
        unset($data['additional_fields']);
    }

    /**
     * Save product custom option data. Used for custom option add and update.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $data
     */
    protected function _saveProductCustomOption($product, $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = Mage::helper('catalog')->stripTags($value);
            }
        }

        try {
            if (!$product->getOptionsReadonly()) {
                $product
                    ->getOptionInstance()
                    ->setOptions([$data]);

                $product->setHasOptions(true);

                // an empty request can be set as event parameter
                // because it is not used for options changing in observers
                Mage::dispatchEvent(
                    'catalog_product_prepare_save',
                    ['product' => $product, 'request' => new Mage_Core_Controller_Request_Http()]
                );

                $product->save();
            }
        } catch (Exception $e) {
            $this->_fault('save_option_error', $e->getMessage());
        }
    }

    /**
     * Read list of possible custom option types from module config
     *
     * @return array
     */
    public function types()
    {
        $path = Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type::PRODUCT_OPTIONS_GROUPS_PATH;
        $types = [];
        foreach (Mage::getConfig()->getNode($path)->children() as $group) {
            $groupTypes = Mage::getConfig()->getNode($path . '/' . $group->getName() . '/types')->children();
            /** @var Mage_Core_Model_Config_Element $type */
            foreach ($groupTypes as $type) {
                $labelPath = $path . '/' . $group->getName() . '/types/' . $type->getName() . '/label';
                $types[] = [
                    'label' => (string) Mage::getConfig()->getNode($labelPath),
                    'value' => $type->getName()
                ];
            }
        }
        return $types;
    }

    /**
     * Get full information about custom option in product
     *
     * @param int|string $optionId
     * @param  int|string|null $store
     * @return array
     */
    public function info($optionId, $store = null)
    {
        /** @var Mage_Catalog_Model_Product_Option $option */
        $option = Mage::getModel('catalog/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        $product = $this->_getProduct($option->getProductId(), $store, null);
        $option = $product->getOptionById($optionId);
        $result = [
            'title' => $option->getTitle(),
            'type' => $option->getType(),
            'is_require' => $option->getIsRequire(),
            'sort_order' => $option->getSortOrder(),
            // additional_fields should be two-dimensional array for all option types
            'additional_fields' => [
                [
                    'price' => $option->getPrice(),
                    'price_type' => $option->getPriceType(),
                    'sku' => $option->getSku()
                ]
            ]
        ];
        // Set additional fields to each type group
        switch ($option->getGroupByType()) {
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_TEXT:
                $result['additional_fields'][0]['max_characters'] = $option->getMaxCharacters();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_FILE:
                $result['additional_fields'][0]['file_extension'] = $option->getFileExtension();
                $result['additional_fields'][0]['image_size_x'] = $option->getImageSizeX();
                $result['additional_fields'][0]['image_size_y'] = $option->getImageSizeY();
                break;
            case Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT:
                $result['additional_fields'] = [];
                foreach ($option->getValuesCollection() as $value) {
                    $result['additional_fields'][] = [
                        'value_id' => $value->getId(),
                        'title' => $value->getTitle(),
                        'price' => $value->getPrice(),
                        'price_type' => $value->getPriceType(),
                        'sku' => $value->getSku(),
                        'sort_order' => $value->getSortOrder()
                    ];
                }
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Retrieve list of product custom options
     *
     * @param  string $productId
     * @param  int|string|null $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = [];
        $product = $this->_getProduct($productId, $store, null);
        /** @var Mage_Catalog_Model_Product_Option $option */
        foreach ($product->getProductOptionsCollection() as $option) {
            $result[] = [
                'option_id' => $option->getId(),
                'title' => $option->getTitle(),
                'type' => $option->getType(),
                'is_require' => $option->getIsRequire(),
                'sort_order' => $option->getSortOrder()
            ];
        }
        return $result;
    }

    /**
     * Remove product custom option
     *
     * @param string $optionId
     * @return bool
     */
    public function remove($optionId)
    {
        /** @var Mage_Catalog_Model_Product_Option $option */
        $option = Mage::getModel('catalog/product_option')->load($optionId);
        if (!$option->getId()) {
            $this->_fault('option_not_exists');
        }
        try {
            $option->getValueInstance()->deleteValue($optionId);
            $option->deletePrices($optionId);
            $option->deleteTitles($optionId);
            $option->delete();
        } catch (Exception $e) {
            $this->_fault('delete_option_error');
        }
        return true;
    }

    /**
     * Check is type in allowed set
     *
     * @param string $type
     * @return bool
     */
    protected function _isTypeAllowed($type)
    {
        $allowedTypes = [];
        foreach ($this->types() as $optionType) {
            $allowedTypes[] = $optionType['value'];
        }

        if (!in_array($type, $allowedTypes)) {
            return false;
        }
        return true;
    }
}
