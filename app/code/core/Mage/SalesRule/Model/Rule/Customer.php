<?php

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
 * @method Mage_SalesRule_Model_Resource_Rule_Customer _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer_Collection getCollection()
 * @method int getCustomerId()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer_Collection getResourceCollection()
 * @method int getRuleId()
 * @method int getTimesUsed()
 * @method $this setCustomerId(int $value)
 * @method $this setRuleId(int $value)
 * @method $this setTimesUsed(int $value)
 */
class Mage_SalesRule_Model_Rule_Customer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_customer');
    }

    /**
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }
}
