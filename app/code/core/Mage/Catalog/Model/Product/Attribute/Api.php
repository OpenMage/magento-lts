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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product attribute api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Api extends Mage_Catalog_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
        $this->_ignoredAttributeCodes[] = 'type_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
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
        $result = array();

        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            if ( (!$attribute->getId() || $attribute->isInSet($setId))
                && $this->_isAllowedAttribute($attribute)) {

                if (!$attribute->getId() || $attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope
                );
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
            ->getAttribute($attributeId)
            ->setStoreId($storeId);

        /* @var $attribute Mage_Catalog_Model_Entity_Attribute */
        if (!$attribute) {
            $this->_fault('not_exists');
        }
        $options = array();
        if ($attribute->usesSource()) {
            foreach ($attribute->getSource()->getAllOptions() as $optionId=>$optionValue) {
                if (is_array($optionValue)) {
                    $options[] = $optionValue;
                } else {
                    $options[] = array(
                        'value' => $optionId,
                        'label' => $optionValue
                    );
                }
            }
        }
        return $options;
    }
} // Class Mage_Catalog_Model_Product_Attribute_Api End
