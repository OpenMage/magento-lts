<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Format total value based on order currency
     *
     * @param   Varien_Object $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            /** @var Mage_Adminhtml_Helper_Sales $helper */
            $helper = $this->helper('adminhtml/sales');
            return $helper->displayPrices(
                $this->getOrder(),
                $total->getBaseValue(),
                $total->getValue(),
            );
        }
        return $total->getValue();
    }

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        $this->_totals = [];
        $this->_totals['subtotal'] = new Varien_Object([
            'code'      => 'subtotal',
            'value'     => $this->getSource()->getSubtotal(),
            'base_value' => $this->getSource()->getBaseSubtotal(),
            'label'     => $this->helper('sales')->__('Subtotal'),
        ]);

        /**
         * Add shipping
         */
        if (!$this->getSource()->getIsVirtual()
            && ((float) $this->getSource()->getShippingAmount() || $this->getSource()->getShippingDescription())
        ) {
            $this->_totals['shipping'] = new Varien_Object([
                'code'      => 'shipping',
                'value'     => $this->getSource()->getShippingAmount(),
                'base_value' => $this->getSource()->getBaseShippingAmount(),
                'label' => $this->helper('sales')->__('Shipping & Handling'),
            ]);
        }

        /**
         * Add discount
         */
        if ((float) $this->getSource()->getDiscountAmount() != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = $this->helper('sales')->__(
                    'Discount (%s)',
                    $this->getSource()->getDiscountDescription(),
                );
            } else {
                $discountLabel = $this->helper('sales')->__('Discount');
            }
            $this->_totals['discount'] = new Varien_Object([
                'code'      => 'discount',
                'value'     => $this->getSource()->getDiscountAmount(),
                'base_value' => $this->getSource()->getBaseDiscountAmount(),
                'label'     => $discountLabel,
            ]);
        }

        $this->_totals['grand_total'] = new Varien_Object([
            'code'      => 'grand_total',
            'strong'    => true,
            'value'     => $this->getSource()->getGrandTotal(),
            'base_value' => $this->getSource()->getBaseGrandTotal(),
            'label'     => $this->helper('sales')->__('Grand Total'),
            'area'      => 'footer',
        ]);

        return $this;
    }
}
