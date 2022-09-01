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
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Tax_Model_Sales_Pdf_Subtotal
 */
class Mage_Tax_Model_Sales_Pdf_Subtotal extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getOrder()->getStore();
        $helper= Mage::helper('tax');
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getSource()->getSubtotalInclTax()) {
            $amountInclTax = $this->getSource()->getSubtotalInclTax();
        } else {
            $amountInclTax = $this->getAmount()
                +$this->getSource()->getTaxAmount()
                -$this->getSource()->getShippingTaxAmount();
        }

        $amountInclTax = $this->getOrder()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        if ($helper->displaySalesSubtotalBoth($store)) {
            $totals = [
                [
                    'amount'    => $this->getAmountPrefix().$amount,
                    'label'     => Mage::helper('tax')->__('Subtotal (Excl. Tax)') . ':',
                    'font_size' => $fontSize
                ],
                [
                    'amount'    => $this->getAmountPrefix().$amountInclTax,
                    'label'     => Mage::helper('tax')->__('Subtotal (Incl. Tax)') . ':',
                    'font_size' => $fontSize
                ],
            ];
        } elseif ($helper->displaySalesSubtotalInclTax($store)) {
            $totals = [[
                'amount'    => $this->getAmountPrefix().$amountInclTax,
                'label'     => Mage::helper('sales')->__($this->getTitle()) . ':',
                'font_size' => $fontSize
            ]];
        } else {
            $totals = [[
                'amount'    => $this->getAmountPrefix().$amount,
                'label'     => Mage::helper('sales')->__($this->getTitle()) . ':',
                'font_size' => $fontSize
            ]];
        }

        return $totals;
    }
}
