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
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Adjustments extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Mage_Sales_Model_Order_Creditmemo
     */
    protected $_source;

    /**
     * Initialize creditmemo adjustment totals
     *
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_source  = $parent->getSource();
        $total = new Varien_Object([
            'code'      => 'agjustments',
            'block_name' => $this->getNameInLayout(),
        ]);
        $parent->removeTotal('shipping');
        $parent->removeTotal('adjustment_positive');
        $parent->removeTotal('adjustment_negative');
        $parent->addTotal($total);
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Get credit memo shipping amount depend on configuration settings
     * @return float
     */
    public function getShippingAmount()
    {
        $config = Mage::getSingleton('tax/config');
        $source = $this->getSource();
        if ($config->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $shipping = $source->getBaseShippingInclTax();
        } else {
            $shipping = $source->getBaseShippingAmount();
        }

        return Mage::app()->getStore()->roundPrice($shipping) * 1;
    }

    /**
     * Get label for shipping total based on configuration settings
     * @return string
     */
    public function getShippingLabel()
    {
        $config = Mage::getSingleton('tax/config');
        $source = $this->getSource();
        if ($config->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $label = $this->helper('sales')->__('Refund Shipping (Incl. Tax)');
        } elseif ($config->displaySalesShippingBoth($source->getOrder()->getStoreId())) {
            $label = $this->helper('sales')->__('Refund Shipping (Excl. Tax)');
        } else {
            $label = $this->helper('sales')->__('Refund Shipping');
        }

        return $label;
    }
}
