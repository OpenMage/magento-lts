<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Cost extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * Collect total cost of refunded items
     *
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $baseRefundTotalCost = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->getOrderItem()->getHasChildren()) {
                $baseRefundTotalCost += $item->getBaseCost() * $item->getQty();
            }
        }
        $creditmemo->setBaseCost($baseRefundTotalCost);
        return $this;
    }
}
