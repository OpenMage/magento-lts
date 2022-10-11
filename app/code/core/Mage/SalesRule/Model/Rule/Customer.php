<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Rule Customer Model
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_SalesRule_Model_Resource_Rule_Customer _getResource()
 * @method Mage_SalesRule_Model_Resource_Rule_Customer getResource()
 * @method int getRuleId()
 * @method Mage_SalesRule_Model_Rule_Customer setRuleId(int $value)
 * @method int getCustomerId()
 * @method Mage_SalesRule_Model_Rule_Customer setCustomerId(int $value)
 * @method int getTimesUsed()
 * @method Mage_SalesRule_Model_Rule_Customer setTimesUsed(int $value)
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
