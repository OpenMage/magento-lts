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
class Mage_Sales_Model_Order_Invoice_Total_Grand extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * @return $this
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        /**
         * Check order grand total and invoice amounts
         */
        if ($invoice->isLast()) {
            //
        }

        return $this;
    }
}
