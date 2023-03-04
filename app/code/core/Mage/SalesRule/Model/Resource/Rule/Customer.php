<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Rule Customer Model Resource
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Rule_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule_customer', 'rule_customer_id');
    }

    /**
     * Get rule usage record for a customer
     *
     * @param Mage_SalesRule_Model_Rule_Customer $rule
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     */
    public function loadByCustomerRule($rule, $customerId, $ruleId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('rule_id = :rule_id');
        $data = $read->fetchRow($select, [':rule_id' => $ruleId, ':customer_id' => $customerId]);
        if ($data === false) {
            // set empty data, as an existing rule object might be used
            $data = [];
        }
        $rule->setData($data);
        return $this;
    }
}
