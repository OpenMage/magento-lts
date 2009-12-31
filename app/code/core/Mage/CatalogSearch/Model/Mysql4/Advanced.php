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
 * @package     Mage_CatalogSearch
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Advanced Catalog Search resource model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Advanced extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define catalog product table as main table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Prepare response object and dispatch prepare price event
     *
     * Return response object
     *
     * @param Varien_Db_Select $select
     * @return Varien_Object
     */
    protected function _dispatchPreparePriceEvent($select)
    {
        // prepare response object for event
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());

        // prepare event arguments
        $eventArgs = array(
            'select'          => $select,
            'table'           => 'price_index',
            'store_id'        => Mage::app()->getStore()->getId(),
            'response_object' => $response
        );

        /**
         * @deprecated since 1.3.2.2
         */
        Mage::dispatchEvent('catalogindex_prepare_price_select', $eventArgs);

        /**
         * @since 1.4
         */
        Mage::dispatchEvent('catalog_prepare_price_select', $eventArgs);

        return $response;
    }

    /**
     * Add filter by attribute price
     *
     * @param Mage_CatalogSearch_Model_Advanced $object
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     */
    public function addPriceFilter($object, $attribute, $value)
    {
        if (empty($value['from']) && empty($value['to'])) {
            return false;
        }

        $adapter = $this->_getReadAdapter();

        $conditions = array();
        if (strlen($value['from']) > 0) {
            $conditions[] = $adapter->quoteInto('price_index.min_price %s * %s >= ?', $value['from']);
        }
        if (strlen($value['to']) > 0) {
            $conditions[] = $adapter->quoteInto('price_index.min_price %s * %s <= ?', $value['to']);
        }

        if (!$conditions) {
            return false;
        }

        $object->getProductCollection()->addPriceData();
        $select     = $object->getProductCollection()->getSelect();
        $response   = $this->_dispatchPreparePriceEvent($select);
        $additional = join('', $response->getAdditionalCalculations());

        $rate = 1;
        if (!empty($value['currency'])) {
            $rate = Mage::app()->getStore()->getBaseCurrency()->getRate($value['currency']);
        }

        foreach ($conditions as $condition) {
            $select->where(sprintf($condition, $additional, $rate));
        }

        return true;
    }

    /**
     * Add filter by indexable attribute
     *
     * @param Mage_CatalogSearch_Model_Advanced $object
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     */
    public function addIndexableAttributeFilter($object, $attribute, $value)
    {
        if (is_string($value) && strlen($value) == 0) {
            return false;
        }
        if (is_array($value) && (isset($value['from']) || isset($value['to']))) {
            return false;
        }

        if ($attribute->getIndexType() == 'decimal') {
            $table = $this->getTable('catalog/product_index_eav_decimal');
        } else {
            $table = $this->getTable('catalog/product_index_eav');
        }

        $tableAlias = 'ast_' . $attribute->getAttributeCode();
        $storeId    = Mage::app()->getStore()->getId();
        $select     = $object->getProductCollection()->getSelect();

        $select->distinct(true);
        $select->join(
            array($tableAlias => $table),
            "e.entity_id={$tableAlias}.entity_id AND {$tableAlias}.attribute_id={$attribute->getAttributeId()}"
                . " AND {$tableAlias}.store_id={$storeId}",
            array()
        );
        $select->where("{$tableAlias}.`value` IN(?)", $value);

        return true;
    }
}
