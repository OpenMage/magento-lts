<?php

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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Weee_Model_Observer
 *
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function setWeeeRendererInForm(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form
        /** @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            $weeeTax = $form->getElement($code);
            if ($weeeTax) {
                $weeeTax->setRenderer(
                    Mage::app()->getLayout()->createBlock('weee/renderer_weee_tax')
                );
            }
        }

        return $this;
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        /** @var Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes $block */
        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        $list = array_merge($list, array_values($attributes));

        $block->setFormExcludedFieldList($list);

        return $this;
    }

    /**
     * Add additional price calculation to select object which is using for select indexed data
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function prepareCatalogIndexSelect(Varien_Event_Observer $observer)
    {
        $storeId = (int)$observer->getEvent()->getStoreId();
        if (!Mage::helper('weee')->isEnabled($storeId)) {
            return $this;
        }

        switch (Mage::helper('weee')->getListPriceDisplayType()) {
            case Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL:
            case Mage_Weee_Model_Tax::DISPLAY_EXCL:
                return $this;
        }

        /** @var Varien_Db_Select $select */
        $select = $observer->getEvent()->getSelect();
        $table = $observer->getEvent()->getTable();

        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = (int)Mage::getSingleton('customer/session')->getCustomerGroupId();

        /** @var Varien_Object $response */
        $response = $observer->getEvent()->getResponseObject();
        $additionalCalculations = $response->getAdditionalCalculations();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes();

        if ($attributes && Mage::helper('weee')->isDiscounted()) {
            $joinConditions = [
                "discount_percent.entity_id = {$table}.entity_id",
                $select->getAdapter()->quoteInto('discount_percent.website_id = ?', $websiteId),
                $select->getAdapter()->quoteInto('discount_percent.customer_group_id = ?', $customerGroupId)
            ];
            $tableWeeDiscount = Mage::getSingleton('weee/tax')->getResource()->getTable('weee/discount');
            $select->joinLeft(
                ['discount_percent' => $tableWeeDiscount],
                implode(' AND ', $joinConditions),
                []
            );
        }
        $checkDiscountField = $select->getAdapter()->getCheckSql(
            'discount_percent.value IS NULL',
            '0',
            'discount_percent.value'
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
        $response->setAdditionalCalculations($additionalCalculations);

        /** @var Varien_Object $rateRequest */
        $rateRequest = Mage::getSingleton('tax/calculation')->getRateRequest();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeTaxAttributeCodes();
        foreach ($attributes as $attribute) {
            $attributeId = (int)Mage::getSingleton('eav/entity_attribute')
                ->getIdByCode(Mage_Catalog_Model_Product::ENTITY, $attribute);
            $tableAlias = sprintf('weee_%s_table', $attribute);
            $quotedTableAlias = $select->getAdapter()->quoteTableAs($tableAlias, null);
            $attributeSelect = $this->_getSelect();
            $attributeSelect
                ->from([$tableAlias => Mage::getSingleton('weee/tax')->getResource()->getTable('weee/tax')])
                ->where("{$quotedTableAlias}.attribute_id = ?", $attributeId)
                ->where("{$quotedTableAlias}.website_id IN(?)", [$websiteId, 0])
                ->where("{$quotedTableAlias}.country = ?", $rateRequest->getCountryId())
                ->where("{$quotedTableAlias}.state IN(?)", [$rateRequest->getRegionId(), '*']);

            $order = [
                sprintf('%s.state %s', $tableAlias, Varien_Db_Select::SQL_DESC),
                sprintf('%s.website_id %s', $tableAlias, Varien_Db_Select::SQL_DESC)
            ];
            $attributeSelect->order($order);

            $joinCondition = sprintf('%s.entity_id = %s.entity_id', $table, $quotedTableAlias);
            $select->joinLeft(
                [$tableAlias => $attributeSelect],
                $joinCondition,
                []
            );
        }
        return $this;
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

    /**
     * Add new attribute type to manage attributes interface
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function addWeeeTaxAttributeType(Varien_Event_Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = [
            'value' => 'weee',
            'label' => Mage::helper('weee')->__('Fixed Product Tax'),
            'hide_fields' => [
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',

                '_scope',
                '_default_value',
                '_front_fieldset',
            ],
            'disabled_types' => [
                Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
            ]
        ];

        $response->setTypes($types);

        return $this;
    }

    /**
     * Automatically assign backend model to weee attributes
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function assignBackendModelToAttribute(Varien_Event_Observer $observer)
    {
        $backendModel = Mage_Weee_Model_Attribute_Backend_Weee_Tax::getBackendModelName();
        /** @var Mage_Eav_Model_Entity_Attribute_Abstract $object */
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = [];
                foreach (Mage_Catalog_Model_Product_Type::getOptions() as $option) {
                    if ($option['value'] == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }

    /**
     * Add custom element type for attributes form
     *
     * @return Mage_Weee_Model_Observer
     */
    public function updateElementTypes(Varien_Event_Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types['weee'] = Mage::getConfig()->getBlockClassName('weee/element_weee_tax');
        $response->setTypes($types);
        return $this;
    }

    /**
     * Update WEEE amounts discount percents
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function updateDiscountPercents(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('weee')->isEnabled()) {
            return $this;
        }

        $productCondition = $observer->getEvent()->getProductCondition();
        if ($productCondition) {
            $eventProduct = $productCondition;
        } else {
            $eventProduct = $observer->getEvent()->getProduct();
        }
        Mage::getModel('weee/tax')->updateProductsDiscountPercent($eventProduct);

        return $this;
    }

    /**
     * Update configurable options of the product view page
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function updateCofigurableProductOptions(Varien_Event_Observer $observer)
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');
        if (!$weeeHelper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $options = $response->getAdditionalOptions();

        $eventProduct = $observer->getEvent()->getProduct();

        $_product = $eventProduct ? $eventProduct : Mage::registry('current_product');

        if (!$_product) {
            return $this;
        }

        $amount = $weeeHelper->getAmountForDisplay($_product);
        $origAmount = $weeeHelper->getOriginalAmount($_product);
        $attributes = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes = $amountInclTaxes - $amount;

        if ($weeeHelper->typeOfDisplay($_product, 3)) {
            // don't show weee as part of the product
            $options['oldPlusDisposition'] = 0;
            $options['plusDisposition'] = 0;
            $options['plusDispositionTax'] = 0;
        } else {
            $options['oldPlusDisposition'] = $origAmount;
            $options['plusDisposition'] = $amount;
            $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        }

        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, [0, 1, 4])) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Process bundle options selection for prepare view json
     *
     * @return  Mage_Weee_Model_Observer
     */
    public function updateBundleProductOptions(Varien_Event_Observer $observer)
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');
        if (!$weeeHelper->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $selection = $observer->getEvent()->getSelection();
        $options = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');

        $typeDynamic = Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return $this;
        }

        $amount = $weeeHelper->getAmountForDisplay($selection);
        $attributes = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes = $amountInclTaxes - $amount;

        if ($weeeHelper->typeOfDisplay($_product, 3)) {
            // don't show weee as part of the product
            $options['plusDisposition'] = 0;
            $options['plusDispositionTax'] = 0;
        } else {
            $options['plusDisposition'] = $amount;
            $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        }

        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, [0, 1, 4])) {
            $options['exclDisposition'] = true;
        }

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Notify weee helper about the admin session quote store when creating order
     * in the backend
     *
     * @return $this
     */
    public function setSessionQuoteStore(Varien_Event_Observer $observer)
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');

        /** @var Mage_Adminhtml_Model_Session_Quote $sessionQuote */
        $sessionQuote = $observer->getEvent()->getSessionQuote();
        if ($sessionQuote) {
            $weeeHelper->setStore($sessionQuote->getStore());
        }

        return $this;
    }
}
