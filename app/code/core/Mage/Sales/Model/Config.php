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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Sales_Model_Config
{
    const XML_PATH_ORDER_STATES = 'global/sales/order/states';

    /**
     * @param string $type
     * @return bool
     */
    public function getQuoteRuleConditionInstance($type)
    {
        return Mage::getConfig()->getNodeClassInstance("global/sales/quote/rule/conditions/$type");
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getQuoteRuleActionInstance($type)
    {
        return Mage::getConfig()->getNodeClassInstance("global/sales/quote/rule/actions/$type");
    }

    /**
     * Retrieve order statuses for state
     *
     * @param string $state
     * @return array
     */
    public function getOrderStatusesForState($state)
    {
        $states = Mage::getConfig()->getNode(self::XML_PATH_ORDER_STATES);
        if (!isset($states->$state) || !isset($states->$state->statuses)) {
            return [];
        }

        $statuses = [];

        foreach ($states->$state->statuses->children() as $status => $node) {
            $statuses[] = $status;
        }
        return $statuses;
    }
}
