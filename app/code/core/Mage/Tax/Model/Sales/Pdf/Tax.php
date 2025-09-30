<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Sales_Pdf_Tax extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Check if tax amount should be included to grandtotal block
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $fontSize
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $config = Mage::getSingleton('tax/config');
        if ($config->displaySalesTaxWithGrandTotal($store)) {
            return [];
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = [];

        if ($config->displaySalesFullSummary($store)) {
            $totals = $this->getFullTaxInfo();
        }

        return array_merge($totals, parent::getTotalsForDisplay());
    }
}
