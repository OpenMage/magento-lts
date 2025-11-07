<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Coupon Model
 *
 * @package    Mage_SalesRule
 *
 * @method Mage_SalesRule_Model_Resource_Coupon _getResource()
 * @method string getCode()
 * @method Mage_SalesRule_Model_Resource_Coupon_Collection getCollection()
 * @method Zend_Date getExpirationDate()
 * @method int getIsPrimary()
 * @method Mage_SalesRule_Model_Resource_Coupon getResource()
 * @method Mage_SalesRule_Model_Resource_Coupon_Collection getResourceCollection()
 * @method int getRuleId()
 * @method int getTimesUsed()
 * @method int getType()
 * @method int getUsageLimit()
 * @method int getUsagePerCustomer()
 * @method $this setCode(string $value)
 * @method $this setExpirationDate(Zend_Date $value)
 * @method $this setIsPrimary(int $value)
 * @method $this setRuleId(int $value)
 * @method $this setTimesUsed(int $value)
 * @method $this setType(int $value)
 * @method $this setUsageLimit(int $value)
 * @method $this setUsagePerCustomer(int $value)
 */
class Mage_SalesRule_Model_Coupon extends Mage_Core_Model_Abstract
{
    /**
     * Coupon's owner rule instance
     *
     * @var Mage_SalesRule_Model_Rule
     */
    protected $_rule;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/coupon');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getRuleId() && $this->_rule instanceof Mage_SalesRule_Model_Rule) {
            $this->setRuleId($this->_rule->getId());
        }

        return parent::_beforeSave();
    }

    /**
     * Set rule instance
     *
     * @return $this
     */
    public function setRule(Mage_SalesRule_Model_Rule $rule)
    {
        $this->_rule = $rule;
        return $this;
    }

    /**
     * Load primary coupon for specified rule
     *
     * @param int|Mage_SalesRule_Model_Rule $rule
     * @return $this
     */
    public function loadPrimaryByRule($rule)
    {
        $this->getResource()->loadPrimaryByRule($this, $rule);
        return $this;
    }

    /**
     * Load Shopping Cart Price Rule by coupon code
     *
     * @param string $couponCode
     * @return $this
     */
    public function loadByCode($couponCode)
    {
        $this->load($couponCode, 'code');
        return $this;
    }
}
