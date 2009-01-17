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
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Validator extends Mage_Core_Model_Abstract
{
    protected $_rules;

	protected function _construct()
	{
        parent::_construct();
		$this->_init('salesrule/validator');
	}

	public function init($websiteId, $customerGroupId, $couponCode)
	{
	    $this->setWebsiteId($websiteId)
	       ->setCustomerGroupId($customerGroupId)
	       ->setCouponCode($couponCode);

	    $this->_rules = Mage::getResourceModel('salesrule/rule_collection')
	        ->setValidationFilter($websiteId, $customerGroupId, $couponCode)
	        ->load();

	    return $this;
	}

	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		$item->setFreeShipping(false);
		$item->setDiscountAmount(0);
		$item->setBaseDiscountAmount(0);
		$item->setDiscountPercent(0);

		$quote = $item->getQuote();

		$address = $item->getAddress();
		if (!$address) {
			$address = $item->getQuote()->getShippingAddress();
		}

		$customerId = $quote->getCustomerId();
        $ruleCustomer = Mage::getModel('salesrule/rule_customer');
		$appliedRuleIds = array();

		foreach ($this->_rules as $rule) {
            /**
             * already tried to validate and failed
             */
			if ($rule->getIsValid()===false) {
			    continue;
			}

			if ($rule->getIsValid()!==true) {
    			/**
    			 * too many times used in general
    			 */
    			if ($rule->getUsesPerCoupon() && ($rule->getTimesUsed() >= $rule->getUsesPerCoupon())) {
                    $rule->setIsValid(false);
                    continue;
                }
                /**
                 * too many times used for this customer
                 */
                $ruleId = $rule->getId();
                if ($ruleId && $rule->getUsesPerCustomer()) {
                    $ruleCustomer->loadByCustomerRule($customerId, $ruleId);
                    if ($ruleCustomer->getId()) {
                        if ($ruleCustomer->getTimesUsed() >= $rule->getUsesPerCustomer()) {
                            continue;
                        }
                    }
                }
                $rule->afterLoad();
                /**
                 * quote does not meet rule's conditions
                 */
    			if (!$rule->validate($address)) {
    			    $rule->setIsValid(false);
    				continue;
    			}
                /**
                 * passed all validations, remember to be valid
                 */
    			$rule->setIsValid(true);
			}

			/**
			 * although the rule is valid, this item is not marked for action
			 */
			if (!$rule->getActions()->validate($item)) {
			    continue;
			}
            $qty = $item->getQty();
            if ($item->getParentItem()) {
                $qty*= $item->getParentItem()->getQty();
            }
			$qty = $rule->getDiscountQty() ? min($qty, $rule->getDiscountQty()) : $qty;
			$rulePercent = $rule->getDiscountAmount();
            $discountAmount = 0;
            $baseDiscountAmount = 0;
			switch ($rule->getSimpleAction()) {
				case 'to_percent':
				    $rulePercent = max(0, 100-$rule->getDiscountAmount());
				    //no break;

				case 'by_percent':
				    if ($step = $rule->getDiscountStep()) {
				        $qty = floor($qty/$step)*$step;
				    }
					$discountAmount    = ($qty*$item->getCalculationPrice() - $item->getDiscountAmount()) * $rulePercent/100;
					$baseDiscountAmount= ($qty*$item->getBaseCalculationPrice() - $item->getBaseDiscountAmount()) * $rulePercent/100;

					if (!$rule->getDiscountQty() || $rule->getDiscountQty()>$qty) {
						$discountPercent = min(100, $item->getDiscountPercent()+$rulePercent);
						$item->setDiscountPercent($discountPercent);
					}
					break;

				case 'to_fixed':
				    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
                    $discountAmount    = $qty*($item->getCalculationPrice()-$quoteAmount);
                    $baseDiscountAmount= $qty*($item->getBaseCalculationPrice()-$rule->getDiscountAmount());
				    break;

				case 'by_fixed':
				    if ($step = $rule->getDiscountStep()) {
				        $qty = floor($qty/$step)*$step;
				    }
				    $quoteAmount = $quote->getStore()->convertPrice($rule->getDiscountAmount());
					$discountAmount    = $qty*$quoteAmount;
					$baseDiscountAmount= $qty*$rule->getDiscountAmount();
					break;

		        case 'cart_fixed':
		            $cartRules = $address->getCartFixedRules();
		            if (!isset($cartRules[$rule->getId()])) {
		                $cartRules[$rule->getId()] = $rule->getDiscountAmount();
		            }
		            if ($cartRules[$rule->getId()] > 0) {
    				    $quoteAmount = $quote->getStore()->convertPrice($cartRules[$rule->getId()]);
    				    $discountAmount = min($item->getRowTotal(), $quoteAmount);
    				    $baseDiscountAmount = min($item->getBaseRowTotal(), $cartRules[$rule->getId()]);
    				    $cartRules[$rule->getId()] -= $baseDiscountAmount;
		            }
				    $address->setCartFixedRules($cartRules);
				    break;

		        case 'buy_x_get_y':
		            $x = $rule->getDiscountStep();
		            $y = $rule->getDiscountAmount();
		            if (!$x || $y>=$x) {
		                break;
		            }
		            $buy = 0; $free = 0;
		            while ($buy+$free<$qty) {
		                $buy += $x;
		                if ($buy+$free>=$qty) {
		                    break;
		                }
		                $free += min($y, $qty-$buy-$free);
		                if ($buy+$free>=$qty) {
		                    break;
		                }
		            }
					$discountAmount    = $free*$item->getCalculationPrice();
					$baseDiscountAmount= $free*$item->getBaseCalculationPrice();
		            break;
			}

            $result = new Varien_Object(array(
                'discount_amount'      => $discountAmount, 
                'base_discount_amount' => $baseDiscountAmount,
            ));
            Mage::dispatchEvent('salesrule_validator_process', array(
                'rule'    => $rule,
                'item'    => $item,
                'address' => $address,
                'quote'   => $quote,
                'qty'     => $qty,
                'result'  => $result,
            ));
            
            $discountAmount = $result->getDiscountAmount();
            $baseDiscountAmount = $result->getBaseDiscountAmount();

            $discountAmount     = $quote->getStore()->roundPrice($discountAmount);
            $baseDiscountAmount = $quote->getStore()->roundPrice($baseDiscountAmount);
            $discountAmount     = min($item->getDiscountAmount()+$discountAmount, $item->getRowTotal());
            $baseDiscountAmount = min($item->getBaseDiscountAmount()+$baseDiscountAmount, $item->getBaseRowTotal());

            $item->setDiscountAmount($discountAmount);
            $item->setBaseDiscountAmount($baseDiscountAmount);

			switch ($rule->getSimpleFreeShipping()) {
				case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM:
					$item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
					break;

				case Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS:
					$address->setFreeShipping(true);
					break;
			}

			$appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

			if ($rule->getCouponCode() && ($rule->getCouponCode() == $this->getCouponCode())) {
                $address->setCouponCode($this->getCouponCode());
			}

			if ($rule->getStopRulesProcessing()) {
				break;
			}
		}
		$item->setAppliedRuleIds(join(',',$appliedRuleIds));
		$address->setAppliedRuleIds($this->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
		$quote->setAppliedRuleIds($this->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));
		return $this;
	}

	public function mergeIds($a1, $a2, $asString=true)
	{
	    if (!is_array($a1)) {
	        $a1 = empty($a1) ? array() : explode(',', $a1);
	    }
	    if (!is_array($a2)) {
	        $a2 = empty($a2) ? array() : explode(',', $a2);
	    }
	    $a = array_unique(array_merge($a1, $a2));
	    if ($asString) {
	       $a = implode(',', $a);
	    }
	    return $a;
	}
}
