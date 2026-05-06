<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Rule Customer Model
 *
 * @package    Mage_SalesRule
 *
 * @method Mage_SalesRule_Model_Resource_Rule_Customer            _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer_Collection getCollection()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer            getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer_Collection getResourceCollection()
 */
class Mage_SalesRule_Model_Rule_Customer extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_customer');
    }

    /**
     * @param  int                 $customerId
     * @param  int                 $ruleId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }

    public function getCustomerId(): int
    {
        return (int) $this->_getData('customer_id');
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function getRuleId(): int
    {
        return (int) $this->_getData('rule_id');
    }

    public function setRuleId(int $value): static
    {
        return $this->setData('rule_id', $value);
    }

    public function getTimesUsed(): int
    {
        return (int) $this->_getData('times_used');
    }

    public function setTimesUsed(int $value): static
    {
        return $this->setData('times_used', $value);
    }
}
