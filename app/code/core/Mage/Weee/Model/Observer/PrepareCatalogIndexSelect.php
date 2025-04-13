<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart operation observer
 *
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Wee_Model_Observer_PrepareCatalogIndexSelect extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Add additional price calculation to select object which is using for select indexed data
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        $storeId = (int) $observer->getEvent()->getDataByKey('store_id');
        if (!Mage::helper('weee')->isEnabled($storeId)) {
            return;
        }

        switch (Mage::helper('weee')->getListPriceDisplayType()) {
            case Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL:
            case Mage_Weee_Model_Tax::DISPLAY_EXCL:
                return;
        }

        /** @var Varien_Db_Select $select */
        $select = $observer->getEvent()->getDataByKey('select');
        /** @var string $table */
        $table = $observer->getEvent()->getDataByKey('table');

        $websiteId = (int) Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = (int) Mage::getSingleton('customer/session')->getCustomerGroupId();

        /** @var Varien_Object $response */
        $response = $observer->getEvent()->getDataByKey('response_object');
        $additionalCalculations = $response->getDataByKey('additional_calculations');

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes();

        if ($attributes && Mage::helper('weee')->isDiscounted()) {
            $joinConditions = [
                "discount_percent.entity_id = {$table}.entity_id",
                $select->getAdapter()->quoteInto('discount_percent.website_id = ?', $websiteId),
                $select->getAdapter()->quoteInto('discount_percent.customer_group_id = ?', $customerGroupId),
            ];
            $tableWeeDiscount = Mage::getSingleton('weee/tax')->getResource()->getTable('weee/discount');
            $select->joinLeft(
                ['discount_percent' => $tableWeeDiscount],
                implode(' AND ', $joinConditions),
                [],
            );
        }
        $checkDiscountField = $select->getAdapter()->getCheckSql(
            'discount_percent.value IS NULL',
            '0',
            'discount_percent.value',
        );
        foreach ($attributes as $attribute) {
            $fieldAlias = sprintf('weee_%s_table.value', $attribute);
            $checkAdditionalCalculation = $select->getAdapter()->getCheckSql("{$fieldAlias} IS NULL", '0', $fieldAlias);
            if (Mage::helper('weee')->isDiscounted()) {
                $additionalCalculations[] = sprintf('+(%s*(1-(%s/100)))', $checkAdditionalCalculation, $checkDiscountField);
            } else {
                $additionalCalculations[] = "+($checkAdditionalCalculation)";
            }
        }
        $response->setData('additional_calculations', $additionalCalculations);

        /** @var Varien_Object $rateRequest */
        $rateRequest = Mage::getSingleton('tax/calculation')->getRateRequest();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeTaxAttributeCodes();
        foreach ($attributes as $attribute) {
            $attributeId = (int) Mage::getSingleton('eav/entity_attribute')
                ->getIdByCode(Mage_Catalog_Model_Product::ENTITY, $attribute);
            $tableAlias = sprintf('weee_%s_table', $attribute);
            $quotedTableAlias = $select->getAdapter()->quoteTableAs($tableAlias, null);
            $attributeSelect = $this->_getSelect();
            $attributeSelect
                ->from([$tableAlias => Mage::getSingleton('weee/tax')->getResource()->getTable('weee/tax')])
                ->where("{$quotedTableAlias}.attribute_id = ?", $attributeId)
                ->where("{$quotedTableAlias}.website_id IN(?)", [$websiteId, 0])
                ->where("{$quotedTableAlias}.country = ?", $rateRequest->getDataByKey('country_id'))
                ->where("{$quotedTableAlias}.state IN(?)", [$rateRequest->getDataByKey('region_id'), '*']);

            $order = [
                sprintf('%s.state %s', $tableAlias, Zend_Db_Select::SQL_DESC),
                sprintf('%s.website_id %s', $tableAlias, Zend_Db_Select::SQL_DESC),
            ];
            $attributeSelect->order($order);

            $joinCondition = sprintf('%s.entity_id = %s.entity_id', $table, $quotedTableAlias);
            $select->joinLeft(
                [$tableAlias => $attributeSelect],
                $joinCondition,
                [],
            );
        }
    }

    /**
     * Get empty select object
     *
     * @return Varien_Db_Select
     */
    protected function _getSelect()
    {
        return Mage::getSingleton('weee/tax')->getResource()->getReadConnection()->select();
    }
}
