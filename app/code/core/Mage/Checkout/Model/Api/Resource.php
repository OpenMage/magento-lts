<?php
/**
 * OpenMage
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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout api resource
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Api_Resource extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Attributes map array per entity type
     *
     * @var array
     */
    protected $_attributesMap = [
        'global' => [],
    ];

    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = [
        'global'    =>  ['entity_id', 'attribute_set_id', 'entity_type_id']
    ];

    /**
     * Field name in session for saving store id
     * @var string
     */
    protected $_storeIdSessionField   = 'store_id';

    /**
     * Check if quote already exist with provided quoteId for creating
     *
     * @param int $quoteId
     * @return bool
     */
    protected function _isQuoteExist($quoteId)
    {
        if (empty($quoteId)) {
            return false;
        }

        try {
            $quote = $this->_getQuote($quoteId);
        } catch (Mage_Api_Exception $e) {
            return false;
        }

        if (!is_null($quote->getId())) {
            $this->_fault('quote_already_exist');
        }

        return false;
    }

    /**
     * Retrieves store id from store code, if no store id specified,
     * it use set session or admin store
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
     * Retrieves quote by quote identifier and store code or by quote identifier
     *
     * @param int $quoteId
     * @param string|int $store
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote($quoteId, $store = null)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel("sales/quote");

        if (!(is_string($store) || is_int($store))) {
            $quote->loadByIdWithoutStore($quoteId);
        } else {
            $storeId = $this->_getStoreId($store);

            $quote->setStoreId($storeId)
                ->load($quoteId);
        }
        if (is_null($quote->getId())) {
            $this->_fault('quote_not_exists');
        }

        return $quote;
    }

    /**
     * Get store identifier by quote identifier
     *
     * @param int $quoteId
     * @return int
     */
    protected function _getStoreIdFromQuote($quoteId)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getModel('sales/quote')
                ->loadByIdWithoutStore($quoteId);

        return $quote->getStoreId();
    }

    /**
     * Update attributes for entity
     *
     * @param array $data
     * @param Mage_Core_Model_Abstract $object
     * @param string $type
     * @param array|null $attributes
     * @return $this
     */
    protected function _updateAttributes($data, $object, $type, array $attributes = null)
    {
        foreach ($data as $attribute => $value) {
            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $object->setData($attribute, $value);
            }
        }

        return $this;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $type
     * @param array|null $attributes
     * @return array
     */
    protected function _getAttributes($object, $type, array $attributes = null)
    {
        $result = [];

        if (!is_object($object)) {
            return $result;
        }

        foreach ($object->getData() as $attribute => $value) {
            if (is_object($value)) {
                continue;
            }

            if ($this->_isAllowedAttribute($attribute, $type, $attributes)) {
                $result[$attribute] = $value;
            }
        }

        foreach ($this->_attributesMap['global'] as $alias => $attributeCode) {
            $result[$alias] = $object->getData($attributeCode);
        }

        if (isset($this->_attributesMap[$type])) {
            foreach ($this->_attributesMap[$type] as $alias => $attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }

    /**
     * Check is attribute allowed to usage
     *
     * @param string $attributeCode
     * @param string $type
     * @param array|null $attributes
     * @return bool
     */
    protected function _isAllowedAttribute($attributeCode, $type, array $attributes = null)
    {
        if (!empty($attributes)
            && !(in_array($attributeCode, $attributes))
        ) {
            return false;
        }

        if (in_array($attributeCode, $this->_ignoredAttributeCodes['global'])) {
            return false;
        }

        if (isset($this->_ignoredAttributeCodes[$type])
            && in_array($attributeCode, $this->_ignoredAttributeCodes[$type])
        ) {
            return false;
        }

        return true;
    }
}
