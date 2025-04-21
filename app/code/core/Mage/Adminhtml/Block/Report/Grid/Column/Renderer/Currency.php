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
class Mage_Adminhtml_Block_Report_Grid_Column_Renderer_Currency extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Currency
{
    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        $currencyCode = $this->_getCurrencyCode($row);

        if (!$currencyCode) {
            return $data;
        }

        $data = (float) $data * $this->_getRate($row);
        $data = sprintf('%F', $data);
        return Mage::app()->getLocale()->currency($currencyCode)->toCurrency($data);
    }
}
