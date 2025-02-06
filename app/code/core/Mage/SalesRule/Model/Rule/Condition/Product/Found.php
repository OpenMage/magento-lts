<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/**
 * Class Mage_SalesRule_Model_Rule_Condition_Product_Found
 *
 * @category   Mage
 * @package    Mage_SalesRule
 *
 * @method setValueOption(array $array)
 */
class Mage_SalesRule_Model_Rule_Condition_Product_Found extends Mage_SalesRule_Model_Rule_Condition_Product_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('salesrule/rule_condition_product_found');
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        $this->setValueOption([
            1 => static::$translate ? Mage::helper('salesrule')->__('FOUND') : 'FOUND',
            0 => static::$translate ? Mage::helper('salesrule')->__('NOT FOUND') : 'NOT FOUND',
        ]);
        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . Mage::helper('salesrule')->__('If an item is %s in the cart with %s of these conditions true:', $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
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
        $all = $this->getAggregator() === 'all';
        $true = (bool) $this->getValue();
        $found = false;
        foreach ($object->getAllItems() as $item) {
            $found = $all;
            foreach ($this->getConditions() as $cond) {
                $validated = $cond->validate($item);
                if (($all && !$validated) || (!$all && $validated)) {
                    $found = $validated;
                    break;
                }
            }
            if (($found && $true) || (!$true && $found)) {
                break;
            }
        }
        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } elseif (!$found && !$true) { // not found and we're making sure it doesn't exist
            return true;
        }
        return false;
    }
}
