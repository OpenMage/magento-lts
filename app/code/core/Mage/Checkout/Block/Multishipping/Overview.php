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
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout overview information
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_Overview extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Initialize default item renderer for row-level items output
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemRender(
            $this->_getRowItemType('default'),
            'checkout/cart_item_renderer',
            'checkout/multishipping/overview/item.phtml'
        );
    }

    /**
     * Get multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Sales_Block_Items_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(
                $this->__('Review Order - %s', $headBlock->getDefaultTitle())
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getBillingAddress()
    {
        return $this->getCheckout()->getQuote()->getBillingAddress();
    }

    /**
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get object with payment info posted data
     *
     * @return Varien_Object
     */
    public function getPayment()
    {
        if (!$this->hasData('payment')) {
            $payment = new Varien_Object($this->getRequest()->getPost('payment'));
            $this->setData('payment', $payment);
        }
        return $this->_getData('payment');
    }

    /**
     * @return Mage_Sales_Model_Quote_Address[]
     */
    public function getShippingAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    /**
     * @return int
     */
    public function getShippingAddressCount()
    {
        $count = $this->getData('shipping_address_count');
        if (is_null($count)) {
            $count = count($this->getShippingAddresses());
            $this->setData('shipping_address_count', $count);
        }
        return $count;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Rate|false
     */
    public function getShippingAddressRate($address)
    {
        if ($rate = $address->getShippingRateByCode($address->getShippingMethod())) {
            return $rate;
        }
        return false;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getShippingPriceInclTax($address)
    {
        $exclTax = $address->getShippingAmount();
        $taxAmount = $address->getShippingTaxAmount();
        return $this->formatPrice($exclTax + $taxAmount);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getShippingPriceExclTax($address)
    {
        return $this->formatPrice($address->getShippingAmount());
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Item[]
     */
    public function getShippingAddressItems($address)
    {
        return $address->getAllVisibleItems();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Mage_Sales_Model_Quote_Address_Total[]
     */
    public function getShippingAddressTotals($address)
    {
        $totals = $address->getTotals();
        foreach ($totals as $total) {
            if ($total->getCode()=='grand_total') {
                if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING) {
                    $total->setTitle($this->__('Total'));
                } else {
                    $total->setTitle($this->__('Total for this address'));
                }
            }
        }
        return $totals;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->getCheckout()->getQuote()->getGrandTotal();
    }

    /**
     * @return string
     */
    public function getAddressesEditUrl()
    {
        return $this->getUrl('*/*/backtoaddresses');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getEditShippingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editShipping', array('id'=>$address->getCustomerAddressId()));
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getEditBillingAddressUrl($address)
    {
        return $this->getUrl('*/multishipping_address/editBilling', array('id'=>$address->getCustomerAddressId()));
    }

    /**
     * @return string
     */
    public function getEditShippingUrl()
    {
        return $this->getUrl('*/*/backtoshipping');
    }

    /**
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/overviewPost');
    }

    /**
     * @return string
     */
    public function getEditBillingUrl()
    {
        return $this->getUrl('*/*/backtobilling');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/backtobilling');
    }

    /**
     * Retrieve virtual product edit url
     *
     * @return string
     */
    public function getVirtualProductEditUrl()
    {
        return $this->getUrl('*/cart');
    }

    /**
     * Retrieve virtual product collection array
     *
     * @return array
     */
    public function getVirtualItems()
    {
        $items = array();
        foreach ($this->getBillingAddress()->getItemsCollection() as $_item) {
            if ($_item->isDeleted()) {
                continue;
            }
            if ($_item->getProduct()->getIsVirtual() && !$_item->getParentItemId()) {
                $items[] = $_item;
            }
        }
        return $items;
    }

    /**
     * Retrieve quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * @return mixed
     */
    public function getBillinAddressTotals()
    {
        $_address = $this->getQuote()->getBillingAddress();
        return $this->getShippingAddressTotals($_address);
    }


    /**
     * @param Mage_Sales_Model_Order_Total $totals
     * @param null $colspan
     * @return string
     */
    public function renderTotals($totals, $colspan = null)
    {
        if ($colspan === null) {
            $colspan = $this->helper('tax')->displayCartBothPrices() ? 5 : 3;
        }
        $totals = $this->getChild('totals')->setTotals($totals)->renderTotals('', $colspan)
            . $this->getChild('totals')->setTotals($totals)->renderTotals('footer', $colspan);
        return $totals;
    }

    /**
     * Add renderer for row-level item output
     *
     * @param   string $type Product type
     * @param   string $block Block type
     * @param   string $template Block template
     * @return  Mage_Checkout_Block_Multishipping_Overview
     */
    public function addRowItemRender($type, $block, $template)
    {
        $type = $this->_getRowItemType($type);
        parent::addItemRender($this->_getRowItemType($type), $block, $template);
        return $this;
    }

    /**
     * Return row-level item html
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getRowItemHtml(Varien_Object $item)
    {
        $type = $this->_getItemType($item);
        $block = $this->_getRowItemRenderer($type)
            ->setItem($item);
        $this->_prepareItem($block);
        return $block->toHtml();
    }

    /**
     * Retrieve renderer block for row-level item output
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function _getRowItemRenderer($type)
    {
        $type = $this->_getRowItemType($type);
        $type = isset($this->_itemRenders[$type]) ? $type : $this->_getRowItemType('default');
        return parent::getItemRenderer($type);
    }

    /**
     * Wrap row renderers into namespace by adding 'row_' suffix
     *
     * @param string $type Product type
     * @return string
     */
    protected function _getRowItemType($type)
    {
        return 'row_' . $type;
    }
}
