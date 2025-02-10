<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Config
{
    public const XML_PATH_ORDER_STATES = 'global/sales/order/states';

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
