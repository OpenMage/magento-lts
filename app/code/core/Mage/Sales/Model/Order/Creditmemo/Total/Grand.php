<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Grand extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    /**
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $grandTotal     = $creditmemo->getGrandTotal();
        $baseGrandTotal = $creditmemo->getBaseGrandTotal();

        $grandTotal += $creditmemo->getAdjustmentPositive();
        $baseGrandTotal += $creditmemo->getBaseAdjustmentPositive();

        $grandTotal -= $creditmemo->getAdjustmentNegative();
        $baseGrandTotal -= $creditmemo->getBaseAdjustmentNegative();

        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);

        $creditmemo->setAdjustment($creditmemo->getAdjustmentPositive() - $creditmemo->getAdjustmentNegative());
        $creditmemo->setBaseAdjustment($creditmemo->getBaseAdjustmentPositive() - $creditmemo->getBaseAdjustmentNegative());

        return $this;
    }
}
