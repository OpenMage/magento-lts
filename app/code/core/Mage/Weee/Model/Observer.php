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
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Weee_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Assign custom renderer for product create/edit form weee attribute element
     *
     * @param Varien_Event_Observer $observer
     */
    public function setWeeeRendererInForm(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_edit_prepare_form

        $form = $observer->getEvent()->getForm();
        $product = $observer->getEvent()->getProduct();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            if ($weeeTax = $form->getElement($code)) {
                $weeeTax->setRenderer(
                    Mage::app()->getLayout()->createBlock('weee/renderer_weee_tax')
                );
            }
        }
    }

    /**
     * Exclude WEEE attributes from standard form generation
     *
     * @param Varien_Event_Observer $observer
     */
    public function updateExcludedFieldList(Varien_Event_Observer $observer)
    {
        //adminhtml_catalog_product_form_prepare_excluded_field_list

        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        foreach ($attributes as $code) {
            $list[] = $code;
        }
        $block->setFormExcludedFieldList($list);
    }

    /**
     * Add additional price calculation to select object which is using for select indexed data
     *
     * @param   Varien_Event_Observer $observer
     */
    public function prepareCatalogIndexSelect(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('weee')->isEnabled($observer->getEvent()->getStoreId())) {
            return $this;
        }

        switch(Mage::helper('weee')->getListPriceDisplayType()) {
            case Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL:
            case Mage_Weee_Model_Tax::DISPLAY_EXCL:
                return $this;
        }

        $select = $observer->getEvent()->getSelect();
        $table  = $observer->getEvent()->getTable();
        $storeId= $observer->getEvent()->getStoreId();

        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $response = $observer->getEvent()->getResponseObject();

        $additionalCalculations = $response->getAdditionalCalculations();

        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes();
        if ($attributes && Mage::helper('weee')->isDiscounted()) {
            $discountField = 'IFNULL(_discount_percent.value, 0)';
            $joinConditions = array(
                "_discount_percent.entity_id = {$table}.entity_id",
                "_discount_percent.website_id = '{$websiteId}'",
                "_discount_percent.customer_group_id = '{$customerGroupId}'",
            );
            $select->joinLeft(array('_discount_percent'=>Mage::getSingleton('weee/tax')->getResource()->getTable('weee/discount')), implode(' AND ', $joinConditions), array());
        }
        foreach ($attributes as $attribute) {
            $tableAlias = "weee_{$attribute}_table";

            if (Mage::helper('weee')->isDiscounted()) {
                $additionalCalculations[] = "+(IFNULL({$tableAlias}.value, 0)*(1-({$discountField}/100)))";
            } else {
                $additionalCalculations[] = "+(IFNULL({$tableAlias}.value, 0))";
            }
        }
        $response->setAdditionalCalculations($additionalCalculations);

        $rateRequest = Mage::getSingleton('tax/calculation')->getRateRequest();
        $attributes = array();
        $attributes = Mage::getSingleton('weee/tax')->getWeeeTaxAttributeCodes();
        foreach ($attributes as $attribute) {
            $attributeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', $attribute);

            $tableAlias = "weee_{$attribute}_table";
            $on = array();
            $on[] = "{$tableAlias}.attribute_id = '{$attributeId}'";
            $on[] = "({$tableAlias}.website_id in ('{$websiteId}', 0))";

            $country = $rateRequest->getCountryId();
            $on[] = "({$tableAlias}.country = '{$country}')";

            $region = $rateRequest->getRegionId();
            $on[] = "({$tableAlias}.state in ('{$region}', '*'))";

            $attributeSelect = $this->_getSelect();
            $attributeSelect->from(array($tableAlias=>Mage::getSingleton('weee/tax')->getResource()->getTable('weee/tax')));


            foreach ($on as $one) {
                $attributeSelect->where($one);
            }
            $attributeSelect->limit(1);

            $order = array($tableAlias.'.state DESC', $tableAlias.'.website_id DESC');

            $attributeSelect->order($order);
            $select->joinLeft(array($tableAlias=>$attributeSelect), $table.'.entity_id = '.$tableAlias.'.entity_id', array());
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

    /**
     * Add new attribute type to manage attributes interface
     *
     * @param   Varien_Event_Observer $observer
     */
    public function addWeeeTaxAttributeType(Varien_Event_Observer $observer)
    {
        // adminhtml_product_attribute_types

        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();
        $types[] = array(
            'value' => 'weee',
            'label' => Mage::helper('weee')->__('Fixed Product Tax'),
            'hide_fields' => array(
                'is_unique',
                'is_required',
                'frontend_class',
                'is_configurable',

                '_scope',
                '_default_value',
                '_front_fieldset',
            ),
            'disabled_types' => array(
                'grouped',
            )
        );

        $response->setTypes($types);
    }

    /**
     * Automaticaly assign backend model to weee attributes
     *
     * @param   Varien_Event_Observer $observer
     */
    public function assignBackendModelToAttribute(Varien_Event_Observer $observer)
    {
        $backendModel = 'weee/attribute_backend_weee_tax';
        $object = $observer->getEvent()->getAttribute();
        if ($object->getFrontendInput() == 'weee') {
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = array();
                foreach (Mage_Catalog_Model_Product_Type::getOptions() as $option) {
                    if ($option['value'] == 'grouped') {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }
    }

    /**
     * Add custom element type for attributes form
     *
     * @param   Varien_Event_Observer $observer
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
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateDiscountPercents(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('weee')->isEnabled()) {
            return $this;
        }

        $eventProduct = $observer->getEvent()->getProduct();
        $productCondition = $observer->getEvent()->getProductCondition();
        if ($productCondition) {
            $eventProduct = $productCondition;
        }
        Mage::getModel('weee/tax')->updateProductsDiscountPercent($eventProduct);
        return $this;
    }

    /**
     * Update configurable options of the product view page
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateCofigurableProductOptions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('weee')->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $options = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');
        if (!$_product) {
            return $this;
        }
        if (!Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
            return $this;
        }
        $amount = Mage::helper('weee')->getAmount($_product);
        $origAmount = Mage::helper('weee')->getOriginalAmount($_product);

        $options['oldPlusDisposition'] = $origAmount;
        $options['plusDisposition'] = $amount;

        $response->setAdditionalOptions($options);

        return $this;
    }

    /**
     * Process bundle options selection for prepare view json
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_Weee_Model_Observer
     */
    public function updateBundleProductOptions(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('weee')->isEnabled()) {
            return $this;
        }

        $response = $observer->getEvent()->getResponseObject();
        $selection = $observer->getEvent()->getSelection();
        $options = $response->getAdditionalOptions();

        $_product = Mage::registry('current_product');
        if (!Mage::helper('weee')->typeOfDisplay($_product, array(0, 1, 4))) {
            return $this;
        }
        $typeDynamic = Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return $this;
        }

        $amount = Mage::helper('weee')->getAmount($selection);
        $options['plusDisposition'] = $amount;

        $response->setAdditionalOptions($options);

        return $this;
    }
}
