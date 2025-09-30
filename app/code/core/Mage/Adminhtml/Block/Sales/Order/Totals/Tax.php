<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml order tax totals block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Totals_Tax extends Mage_Tax_Block_Sales_Order_Tax
{
    /**
     * Get full information about taxes applied to order
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        /** @var Mage_Sales_Model_Order $source */
        $source = $this->getOrder();

        $taxClassAmount = [];
        if ($source instanceof Mage_Sales_Model_Order) {
            $taxClassAmount = $this->_getTaxHelper()->getCalculatedTaxes($source);
        }

        return $taxClassAmount;
    }

    /**
     * Return Mage_Tax_Helper_Data instance
     *
     * @return Mage_Tax_Helper_Data
     */
    protected function _getTaxHelper()
    {
        return Mage::helper('tax');
    }

    /**
     * Display tax amount
     *
     * @param float $amount
     * @param float $baseAmount
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return Mage::helper('adminhtml/sales')->displayPrices(
            $this->getSource(),
            $baseAmount,
            $amount,
            false,
            '<br />',
        );
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore();
    }
}
