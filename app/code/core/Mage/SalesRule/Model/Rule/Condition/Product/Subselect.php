<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * Class Mage_SalesRule_Model_Rule_Condition_Product_Subselect
 *
 * @package    Mage_SalesRule
 *
 * @method $this setAttributeOption(array $value)
 * @method $this setOperatorOption(array $value)
 */
class Mage_SalesRule_Model_Rule_Condition_Product_Subselect extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_subselect')
            ->setValue(null);
    }

    /**
     * @param array $arr
     * @param string $key
     * @return $this|Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);
        return $this;
    }

    /**
     * @param string $containerKey
     * @param string $itemKey
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        return '<attribute>' . $this->getAttribute() . '</attribute>'
            . '<operator>' . $this->getOperator() . '</operator>'
            . parent::asXml($containerKey, $itemKey);
    }

    /**
     * @return $this|Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'qty'  => Mage::helper('salesrule')->__('total quantity'),
            'base_row_total'  => Mage::helper('salesrule')->__('total amount'),
        ]);
        return $this;
    }

    /**
     * @return $this|Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * @return $this|Mage_SalesRule_Model_Rule_Condition_Product_Combine
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption([
            '=='  => static::$translate ? Mage::helper('rule')->__('is') : 'is',
            '!='  => static::$translate ? Mage::helper('rule')->__('is not') : 'is not',
            '>='  => static::$translate ? Mage::helper('rule')->__('equals or greater than') : 'equals or greater than',
            '<='  => static::$translate ? Mage::helper('rule')->__('equals or less than') : 'equals or less than',
            '>'   => static::$translate ? Mage::helper('rule')->__('greater than') : 'greater than',
            '<'   => static::$translate ? Mage::helper('rule')->__('less than') : 'less than',
            '()'  => static::$translate ? Mage::helper('rule')->__('is one of') : 'is one of',
            '!()' => static::$translate ? Mage::helper('rule')->__('is not one of') : 'is not one of',
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
        Mage::helper('salesrule')->__('If %s %s %s for a subselection of items in cart matching %s of these conditions:', $this->getAttributeElement()->getHtml(), $this->getOperatorElement()->getHtml(), $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    /**
     * validate
     *
     * @param Varien_Object $object Quote
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $attr = $this->getAttribute();
        $total = 0;
        foreach ($object->getQuote()->getAllVisibleItems() as $item) {
            if (Mage_Rule_Model_Condition_Combine::validate($item)) {
                $total += $item->getData($attr);
            }
        }

        return $this->validateAttribute($total);
    }
}
