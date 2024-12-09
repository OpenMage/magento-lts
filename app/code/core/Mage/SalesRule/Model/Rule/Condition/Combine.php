<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = ['value' => 'salesrule/rule_condition_address|' . $code, 'label' => $label];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            ['value' => 'salesrule/rule_condition_product_found', 'label' => Mage::helper('salesrule')->__('Product attribute combination')],
            ['value' => 'salesrule/rule_condition_product_subselect', 'label' => Mage::helper('salesrule')->__('Products subselection')],
            ['value' => 'salesrule/rule_condition_combine', 'label' => Mage::helper('salesrule')->__('Conditions combination')],
            ['label' => Mage::helper('salesrule')->__('Cart Attribute'), 'value' => $attributes],
        ]);

        $additional = new Varien_Object();
        Mage::dispatchEvent('salesrule_rule_condition_combine', ['additional' => $additional]);
        if ($additionalConditions = $additional->getConditions()) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
