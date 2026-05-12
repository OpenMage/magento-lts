<?php

declare(strict_types=1);

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
 * @method Mage_SalesRule_Model_Resource_Coupon            _getResource()
 * @method Mage_SalesRule_Model_Resource_Coupon_Collection getCollection()
 * @method string|Zend_Date                                getExpirationDate()
 * @method Mage_SalesRule_Model_Resource_Coupon            getResource()
 * @method Mage_SalesRule_Model_Resource_Coupon_Collection getResourceCollection()
 * @method $this                                           setExpirationDate(string|Zend_Date $value)
 */
class Mage_SalesRule_Model_Coupon extends Mage_Core_Model_Abstract
{
    /**
     * Coupon's owner rule instance
     *
     * @var Mage_SalesRule_Model_Rule
     */
    protected $_rule;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/coupon');
    }

    public function getCode(): string
    {
        return (string) $this->_getData('code');
    }

    public function getIsPrimary(): ?int
    {
        $value = $this->_getData('is_primary');
        return $value !== null ? (int) $value : null;
    }

    public function getRuleId(): int
    {
        return (int) $this->_getData('rule_id');
    }

    public function getTimesUsed(): int
    {
        return (int) $this->_getData('times_used');
    }

    public function getUsageLimit(): ?int
    {
        $value = $this->_getData('usage_limit');
        return $value !== null ? (int) $value : null;
    }

    public function getUsagePerCustomer(): ?int
    {
        $value = $this->_getData('usage_per_customer');
        return $value !== null ? (int) $value : null;
    }

    public function setCode(string $value): static
    {
        return $this->setData('code', $value);
    }

    public function setIsPrimary(?int $value): static
    {
        return $this->setData('is_primary', $value);
    }

    public function setRuleId(int $value): static
    {
        return $this->setData('rule_id', $value);
    }

    public function setTimesUsed(int $value): static
    {
        return $this->setData('times_used', $value);
    }

    public function setUsageLimit(?int $value): static
    {
        return $this->setData('usage_limit', $value);
    }

    public function setUsagePerCustomer(?int $value): static
    {
        return $this->setData('usage_per_customer', $value);
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
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
     * @param  int|Mage_SalesRule_Model_Rule $rule
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadPrimaryByRule($rule)
    {
        $this->getResource()->loadPrimaryByRule($this, $rule);
        return $this;
    }

    /**
     * Load Shopping Cart Price Rule by coupon code
     *
     * @param  string              $couponCode
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByCode($couponCode)
    {
        $this->load($couponCode, 'code');
        return $this;
    }

    public function getType(): int
    {
        return (int) $this->_getData('type');
    }

    public function setType(int $value): static
    {
        return $this->setData('type', $value);
    }
}
