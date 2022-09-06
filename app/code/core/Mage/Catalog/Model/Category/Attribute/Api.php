<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category attribute api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
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
                    'scope'        => $scope
                ];
            }
        }

        return $result;
    }

    /**
     * Retrieve category attribute options
     *
     * @param int|string $attributeId
     * @param string|int $store
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
                        'label' => $optionValue
                    ];
                }
            }
        }

        return $result;
    }
}
