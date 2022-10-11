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
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Rule Combine Condition data model
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 * @author     Magento Core Team <core@magentocommerce.com>
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
            $attributes[] = ['value'=>'catalogrule/rule_condition_product|'.$code, 'label'=>$label];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            ['value'=>'catalogrule/rule_condition_combine', 'label'=>Mage::helper('catalogrule')->__('Conditions Combination')],
            ['label'=>Mage::helper('catalogrule')->__('Product Attribute'), 'value'=>$attributes],
        ]);
        return $conditions;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $productCollection
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
