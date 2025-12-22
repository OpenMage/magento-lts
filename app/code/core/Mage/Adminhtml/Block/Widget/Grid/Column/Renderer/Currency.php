<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer currency
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     */
    protected static $_currencies = [];

    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($data = (string) $row->getData($this->getColumn()->getIndex())) {
            $currencyCode = $this->_getCurrencyCode($row);

            if (!$currencyCode) {
                return $data;
            }

            $data = (float) $data * $this->_getRate($row);
            $sign = (bool) (int) $this->getColumn()->getShowNumberSign() && ($data > 0) ? '+' : '';
            $data = sprintf('%F', $data);
            $data = Mage::app()->getLocale()->currency($currencyCode)->toCurrency($data);
            return $sign . $data;
        }

        return $this->getColumn()->getDefault();
    }

    /**
     * Returns currency code, false on error
     *
     * @param  Varien_Object $row
     * @return false|string
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
     * Get rate for current row, 1 by default
     *
     * @param  Varien_Object $row
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
