<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product options abstract type block
 *
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Block_Product_View_Options_Abstract extends Mage_Core_Block_Template
{
    /**
     * Product object
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Product option object
     *
     * @var Mage_Catalog_Model_Product_Option
     */
    protected $_option;

    /**
     * Set Product object
     *
     * @return $this
     */
    public function setProduct(?Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Retrieve Product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set option
     *
     * @return Mage_Catalog_Block_Product_View_Options_Abstract
     */
    public function setOption(Mage_Catalog_Model_Product_Option $option)
    {
        $this->_option = $option;
        return $this;
    }

    /**
     * Get option
     *
     * @return Mage_Catalog_Model_Product_Option
     */
    public function getOption()
    {
        return $this->_option;
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getFormatedPrice()
    {
        if ($option = $this->getOption()) {
            return $this->_formatPrice([
                'is_percent'    => ($option->getPriceType() === 'percent'),
                'pricing_value' => $option->getPrice($option->getPriceType() === 'percent'),
            ]);
        }

        return '';
    }

    /**
     * Return formatted price
     *
     * @param array $value
     * @param bool $flag
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _formatPrice($value, $flag = true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $taxHelper = Mage::helper('tax');
        $store = $this->getProduct()->getStore();

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');

        $priceStr = $sign;
        $_priceInclTax = $this->getPrice($value['pricing_value'], true);
        $_priceExclTax = $this->getPrice($value['pricing_value']);
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $helper::currencyByStore($_priceInclTax, $store, true, $flag);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $helper::currencyByStore($_priceExclTax, $store, true, $flag);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $helper::currencyByStore($_priceExclTax, $store, true, $flag);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (' . $sign . $helper::currencyByStore($_priceInclTax, $store, true, $flag) . ' ' . $this->__('Incl. Tax') . ')';
            }
        }

        if ($flag) {
            $priceStr = '<span class="price-notice">' . $priceStr . '</span>';
        }

        return $priceStr;
    }

    /**
     * Get price with including/excluding tax
     *
     * @param float $price
     * @param bool $includingTax
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPrice($price, $includingTax = null)
    {
        if (!is_null($includingTax)) {
            $price = Mage::helper('tax')->getPrice($this->getProduct(), $price, true);
        } else {
            $price = Mage::helper('tax')->getPrice($this->getProduct(), $price);
        }

        return $price;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        $store = $this->getProduct()->getStore();
        return $helper::currencyByStore($price, $store, false);
    }
}
