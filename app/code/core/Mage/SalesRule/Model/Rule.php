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
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Rule extends Mage_Rule_Model_Rule
{
    const FREE_SHIPPING_ITEM = 1;
    const FREE_SHIPPING_ADDRESS = 2;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule');
        $this->setIdFieldName('rule_id');
    }

    protected function _beforeSave()
    {
        if($coupon = $this->getCouponCode()) {
            $this->getResource()->addUniqueField(array(
                'field' => 'coupon_code',
                'title' => Mage::helper('salesRule')->__('Coupon with the same code')
            ));
            Mage::app()->cleanCache('salesrule_coupon_'.md5($coupon));
        } else {
            $this->getResource()->resetUniqueField();
        }
        return parent::_beforeSave();
    }


    protected function _beforeDelete()
    {
        if ($coupon = $this->getCouponCode()) {
            Mage::app()->cleanCache('salesrule_coupon_'.$coupon);
        }
        parent::_beforeDelete();
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
    }

    public function toString($format='')
    {
        $str = Mage::helper('salesrule')->__("Name: %s", $this->getName()) ."\n"
             . Mage::helper('salesrule')->__("Start at: %s", $this->getStartAt()) ."\n"
             . Mage::helper('salesrule')->__("Expire at: %s", $this->getExpireAt()) ."\n"
             . Mage::helper('salesrule')->__("Customer registered: %s", $this->getCustomerRegistered()) ."\n"
             . Mage::helper('salesrule')->__("Customer is new buyer: %s", $this->getCustomerNewBuyer()) ."\n"
             . Mage::helper('salesrule')->__("Description: %s", $this->getDescription()) ."\n\n"
             . $this->getConditions()->toStringRecursive() ."\n\n"
             . $this->getActions()->toStringRecursive() ."\n\n";
        return $str;
    }

    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
		if (isset($arr['conditions'])) {
    		$this->getConditions()->loadArray($arr['conditions'][1]);
		}
		if (isset($arr['actions'])) {
    		$this->getActions()->loadArray($arr['actions'][1], 'actions');
		}

    	return $this;
    }

    /**
     * Returns rule as an array for admin interface
     *
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     *
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = parent::toArray($arrAttributes);
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();

        return $out;
    }
    /*
    public function processProduct(Mage_Sales_Model_Product $product)
    {
        $this->validateProduct($product) && $this->updateProduct($product);
        return $this;
    }

    public function validateProduct(Mage_Sales_Model_Product $product)
    {
        if (!$this->getIsCollectionValidated()) {
            $env = $this->getEnv();
            $result = $result && $this->getIsActive()
                && (strtotime($this->getStartAt()) <= $env->getNow())
                && (strtotime($this->getExpireAt()) >= $env->getNow())
                && ($this->getCustomerRegistered()==2 || $this->getCustomerRegistered()==$env->getCustomerRegistered())
                && ($this->getCustomerNewBuyer()==2 || $this->getCustomerNewBuyer()==$env->getCustomerNewBuyer())
                && $this->getConditions()->validateProduct($product);
        } else {
            $result = $this->getConditions()->validateProduct($product);
        }

        return $result;
    }

    public function updateProduct(Mage_Sales_Model_Product $product)
    {
        $this->getActions()->updateProduct($product);
        return $this;
    }
    */
    public function getResourceCollection()
    {
        return Mage::getResourceModel('salesrule/rule_collection');
    }

//    protected function _afterSave()
//    {
//        $this->_getResource()->updateRuleProductData($this);
//        parent::_afterSave();
//    }
//
//    public function validate(Varien_Object $quote)
//    {
//
//        if ($this->getUsesPerCustomer() && $quote->getCustomer()) {
//            $customerUses = $this->_getResource()->getCustomerUses($this, $quote->getCustomerId());
//            if ($customerUses >= $this->getUsesPerCustomer()) {
//                return false;
//            }
//        }
//        return parent::validate($quote);
//    }
}
