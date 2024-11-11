<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Sales_Pdf_Shipping extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Get array of arrays with totals information for display in PDF
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
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        $amountInclTax = $this->getSource()->getShippingInclTax();
        if (!$amountInclTax) {
            $amountInclTax = $this->getAmount() + $this->getSource()->getShippingTaxAmount();
        }
        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        if ($config->displaySalesShippingBoth($store)) {
            $totals = [
                [
                    'amount'    => $this->getAmountPrefix() . $amount,
                    'label'     => Mage::helper('tax')->__('Shipping (Excl. Tax)') . ':',
                    'font_size' => $fontSize
                ],
                [
                    'amount'    => $this->getAmountPrefix() . $amountInclTax,
                    'label'     => Mage::helper('tax')->__('Shipping (Incl. Tax)') . ':',
                    'font_size' => $fontSize
                ],
            ];
        } elseif ($config->displaySalesShippingInclTax($store)) {
            $totals = [[
                'amount'    => $this->getAmountPrefix() . $amountInclTax,
                'label'     => Mage::helper('sales')->__($this->getTitle()) . ':',
                'font_size' => $fontSize
            ]];
        } else {
            $totals = [[
                'amount'    => $this->getAmountPrefix() . $amount,
                'label'     => Mage::helper('sales')->__($this->getTitle()) . ':',
                'font_size' => $fontSize
            ]];
        }

        return $totals;
    }
}
