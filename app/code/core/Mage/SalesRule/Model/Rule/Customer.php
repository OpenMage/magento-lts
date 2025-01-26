<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Rule Customer Model
 *
 * @category   Mage
 * @package    Mage_SalesRule
 *
 * @method Mage_SalesRule_Model_Resource_Rule_Customer _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer getResource()
 * @method int getRuleId()
 * @method $this setRuleId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method int getTimesUsed()
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
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        $this->_getResource()->loadByCustomerRule($this, $customerId, $ruleId);
        return $this;
    }
}
