<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Rule Product Condition data model
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Validate product attribute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();
        if ($attrCode == 'category_ids') {
            return $this->validateAttribute($object->getCategoryIds());
        }
        if ($attrCode == 'attribute_set_id') {
            return $this->validateAttribute($object->getData($attrCode));
        }

        $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null;
        $object->setData($attrCode, $this->_getAttributeValue($object));
        $result = $this->_validateProduct($object);
        $this->_restoreOldAttrValue($object, $oldAttrValue);

        return (bool)$result;
    }

    /**
     * Validate product
     *
     * @param Varien_Object $object
     * @return bool
     */
    protected function _validateProduct($object)
    {
        return Mage_Rule_Model_Condition_Abstract::validate($object);
    }

    /**
     * Restore old attribute value
     *
     * @param Varien_Object $object
     * @param mixed $oldAttrValue
     */
    protected function _restoreOldAttrValue($object, $oldAttrValue)
    {
        $attrCode = $this->getAttribute();
        if (is_null($oldAttrValue)) {
            $object->unsetData($attrCode);
        } else {
            $object->setData($attrCode, $oldAttrValue);
        }
    }

    /**
     * Get attribute value
     *
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _getAttributeValue($object)
    {
        $attrCode = $this->getAttribute();
        $storeId = $object->getStoreId();
        $defaultStoreId = Mage_Core_Model_App::ADMIN_STORE_ID;
        $productValues  = $this->_entityAttributeValues[$object->getId()] ?? [];
        $defaultValue = $productValues[$defaultStoreId] ?? $object->getData($attrCode);
        $value = $productValues[$storeId] ?? $defaultValue;

        $value = $this->_prepareDatetimeValue($value, $object);
        $value = $this->_prepareMultiselectValue($value, $object);

        return $value;
    }

    /**
     * Prepare datetime attribute value
     *
     * @param mixed $value
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _prepareDatetimeValue($value, $object)
    {
        $attribute = $object->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getBackendType() === 'datetime') {
            if (!$value) {
                return null;
            }
            $value = strtotime($value);
        }
        return $value;
    }

    /**
     * Prepare multiselect attribute value
     *
     * @param mixed $value
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _prepareMultiselectValue($value, $object)
    {
        $attribute = $object->getResource()->getAttribute($this->getAttribute());
        if ($attribute && $attribute->getFrontendInput() == 'multiselect') {
            $value = strlen($value) ? explode(',', $value) : [];
        }
        return $value;
    }
}
