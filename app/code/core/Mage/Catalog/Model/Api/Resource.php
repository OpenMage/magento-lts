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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog api resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api_Resource extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Default ignored attribute codes
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = ['entity_id', 'attribute_set_id', 'entity_type_id'];

    /**
     * Default ignored attribute types
     *
     * @var array
     */
    protected $_ignoredAttributeTypes = [];

    /**
     * Field name in session for saving store id
     * @var string
     */
    protected $_storeIdSessionField   = 'store_id';

    /**
     * Name of resource model in ACL list
     * @var string
     */
    protected $_resourceAttributeAclName = 'catalog/category/attributes/field_';

    /**
     * Check is attribute allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return bool
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        if (Mage::getSingleton('api/server')->getApiName() == 'rest') {
            if (!$this->_checkAttributeAcl($attribute)) {
                return false;
            }
        }

        if (is_array($attributes)
            && !(in_array($attribute->getAttributeCode(), $attributes)
                  || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes);
    }

    /**
     * Retrieves store id from store code, if no store id specified,
     * it use seted session or admin store
     *
     * @param string|int $store
     * @return int
     */
    protected function _getStoreId($store = null)
    {
        if (is_null($store)) {
            $store = ($this->_getSession()->hasData($this->_storeIdSessionField)
                        ? $this->_getSession()->getData($this->_storeIdSessionField) : 0);
        }

        try {
            $storeId = Mage::app()->getStore($store)->getId();
        } catch (Mage_Core_Model_Store_Exception $e) {
            $this->_fault('store_not_exists');
        }

        return $storeId;
    }

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  int|string $store
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId, $store = null, $identifierType = null)
    {
        $product = Mage::helper('catalog/product')->getProduct($productId, $this->_getStoreId($store), $identifierType);
        if (is_null($product->getId())) {
            $this->_fault('product_not_exists');
        }
        return $product;
    }

    /**
     * Set current store for catalog.
     *
     * @param string|int $store
     * @return int
     */
    public function currentStore($store = null)
    {
        if (!is_null($store)) {
            try {
                $storeId = Mage::app()->getStore($store)->getId();
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_fault('store_not_exists');
            }

            $this->_getSession()->setData($this->_storeIdSessionField, $storeId);
        }

        return $this->_getStoreId();
    }
}
