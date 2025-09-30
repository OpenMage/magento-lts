<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sales Order items name column renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Items_Column_Name_Grouped extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getItem()->getOrderItem()) {
            $item = $this->getItem()->getOrderItem();
        } else {
            $item = $this->getItem();
        }
        if ($productType = $item->getRealProductType()) {
            return $this->getRenderedBlock()->getColumnHtml($this->getItem(), $productType);
        }
        return parent::_toHtml();
    }
}
