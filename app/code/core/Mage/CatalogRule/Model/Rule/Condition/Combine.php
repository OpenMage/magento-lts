<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * Catalog Rule Combine Condition data model
 *
 * @package    Mage_CatalogRule
 */
class Mage_CatalogRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('catalogrule/rule_condition_combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = Mage::getModel('catalogrule/rule_condition_product');
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = ['value' => 'catalogrule/rule_condition_product|' . $code, 'label' => $label];
        }

        $conditions = parent::getNewChildSelectOptions();
        return array_merge_recursive($conditions, [
            ['value' => 'catalogrule/rule_condition_combine', 'label' => Mage::helper('catalogrule')->__('Conditions Combination')],
            ['label' => Mage::helper('catalogrule')->__('Product Attribute'), 'value' => $attributes],
        ]);
    }

    /**
     * @param  Mage_Catalog_Model_Resource_Product_Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
