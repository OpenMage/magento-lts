<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer currency
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;
    /**
     * Currency objects cache
     */
    protected static $_currencies = [];

    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $currencyCode = $this->_getCurrencyCode($row);

            if (!$currencyCode) {
                return $data;
            }

            $data = (float) $data * $this->_getRate($row);
            $data = sprintf('%F', $data);
            return Mage::app()->getLocale()->currency($currencyCode)->toCurrency($data);
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code for the row, false on error
     *
     * @param Varien_Object $row
     * @return string|bool
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }
        return false;
    }

    /**
     * Returns rate for the row, 1 by default
     *
     * @param Varien_Object $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return (float) $rate;
        }
        if (($rateField = $this->getColumn()->getRateField()) && ($rate = $row->getData($rateField))) {
            return (float) $rate;
        }
        return 1;
    }
}
