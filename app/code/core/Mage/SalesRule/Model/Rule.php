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
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_SalesRule_Model_Rule extends Mage_Rule_Model_Rule
{
    const FREE_SHIPPING_ITEM = 1;
    const FREE_SHIPPING_ADDRESS = 2;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrule_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    protected $_labels = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Check unique coupon code before saving and clear coupon related cache
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _beforeSave()
    {
        $coupon = $this->getCouponCode();
        if($coupon) {
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

    /**
     * Delete rule coupon code related cache
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _beforeDelete()
    {
        $coupon = $this->getCouponCode();
        if ($coupon) {
            Mage::app()->cleanCache('salesrule_coupon_'.$coupon);
        }
        return parent::_beforeDelete();
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

    /**
     * Initialize rule model data from array
     *
     * @param   array $rule
     * @return  Mage_SalesRule_Model_Rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        if (isset($rule['store_labels'])) {
            $this->setStoreLabels($rule['store_labels']);
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

    public function getResourceCollection()
    {
        return Mage::getResourceModel('salesrule/rule_collection');
    }

    /**
     * Save rula labels after rule save
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _afterSave()
    {
        if ($this->hasStoreLabels()) {
            $this->_getResource()->saveStoreLabels($this->getId(), $this->getStoreLabels());
        }
        return parent::_afterSave();
    }

    /**
     * Get Rule label for specific store
     *
     * @param   store $store
     * @return  string | false
     */
    public function getStoreLabel($store=null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        if ($this->hasStoreLabels()) {
            $labels = $this->_getData('store_labels');
            if (isset($labels[$storeId])) {
                return $labels[$storeId];
            } elseif ($labels[0]) {
                return $labels[0];
            }
            return false;
        } elseif (!isset($this->_labels[$storeId])) {
            $this->_labels[$storeId] = $this->_getResource()->getStoreLabel($this->getId(), $storeId);
        }
        return $this->_labels[$storeId];
    }

    /**
     * Get all existing rule labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }
        return $this->_getData('store_labels');
    }
}
