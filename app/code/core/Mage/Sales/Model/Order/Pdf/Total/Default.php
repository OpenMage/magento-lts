<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Total PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Total_Default extends Varien_Object
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
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix().$amount;
        }
        $title = $this->_getSalesHelper()->__($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }

    /**
     * @return Mage_Sales_Helper_Data
     */
    protected function _getSalesHelper()
    {
        return Mage::helper('sales');
    }

    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getFullTaxInfo()
    {
        $fontSize       = $this->getFontSize() ? $this->getFontSize() : 7;
        $taxClassAmount = $this->_getCalculatedTaxes();
        $shippingTax    = $this->_getShippingTax();
        $taxClassAmount = array_merge($taxClassAmount, $shippingTax);

        if (!empty($taxClassAmount)) {
            foreach ($taxClassAmount as &$tax) {
                $percent          = $tax['percent'] ? ' (' . $tax['percent']. '%)' : '';
                $tax['amount']    = $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($tax['tax_amount']);
                $tax['label']     = $this->_getTaxHelper()->__($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            $fullInfo = $this->_getFullRateInfo();
            $tax_info = array();

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent']. '%)' : '';

                        $tax_info[] = array(
                            'amount'    => $this->getAmountPrefix() . $this->getOrder()->formatPriceTxt($_amount),
                            'label'     => $this->_getTaxHelper()->__($rate['title']) . $percent . ':',
                            'font_size' => $fontSize
                        );
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }

    /**
     * Get full rate info
     *
     * @return array
     */
    protected function _getFullRateInfo()
    {
        $rates = Mage::getModel('tax/sales_order_tax')->getCollection()->loadByOrder($this->getOrder())->toArray();
        $fullInfo = Mage::getSingleton('tax/calculation')->reproduceProcess($rates['items']);
        return $fullInfo;
    }

    /**
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Get shipping tax
     *
     * @return array
     */
    protected function _getShippingTax()
    {
        return $this->_getTaxHelper()->getShippingTax($this->getOrder());
    }

    /**
     * Get calculated taxes
     *
     * @return array
     */
    protected function _getCalculatedTaxes()
    {
        return $this->_getTaxHelper()->getCalculatedTaxes($this->getOrder());
    }

    /**
     * Check if we can display total information in PDF
     *
     * @return bool
     */
    public function canDisplay()
    {
        $amount = $this->getAmount();
        return $this->getDisplayZero() || ($amount != 0);
    }

    /**
     * Get Total amount from source
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getDataUsingMethod($this->getSourceField());
    }

    /**
     * Get title description from source
     *
     * @return mixed
     */
    public function getTitleDescription()
    {
        return $this->getSource()->getDataUsingMethod($this->getTitleSourceField());
    }
}
