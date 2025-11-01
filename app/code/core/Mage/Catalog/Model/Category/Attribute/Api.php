<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category attribute api
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Attribute_Api extends Mage_Catalog_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'category_store_id';
    }

    /**
     * Retrieve category attributes
     *
     * @return array
     */
    public function items()
    {
        $attributes = Mage::getModel('catalog/category')->getAttributes();
        $result = [];

        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if ($this->_isAllowedAttribute($attribute)) {
                if (!$attribute->getId() || $attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = [
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope,
                ];
            }
        }

        return $result;
    }

    /**
     * Retrieve category attribute options
     *
     * @param int|string $attributeId
     * @param int|string $store
     * @return array
     */
    public function options($attributeId, $store = null)
    {
        $attribute = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store))
            ->getResource()
            ->getAttribute($attributeId);

        if (!$attribute) {
            $this->_fault('not_exists');
        }

        $result = [];
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions(false) as $optionId => $optionValue) {
                if (is_array($optionValue)) {
                    $result[] = $optionValue;
                } else {
                    $result[] = [
                        'value' => $optionId,
                        'label' => $optionValue,
                    ];
                }
            }
        }

        return $result;
    }
}
