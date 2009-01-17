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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
    }

    /**
     * Get array last added items
     *
     * @return array
     */
    public function getRecentItems()
    {
        $items = array();
        if (!$this->getSummaryCount()) {
        	return $items;
        }
        $i = 0;
        $allItems = array_reverse($this->getItems());
        foreach ($allItems as $item) {
        	$items[] = $item;
        	if (++$i==3) break;
        }
        return $items;
    }

    /**
     * Get shopping cart subtotal.
     * It will include tax, if required by config settings.
     *
     * @return decimal
     */
    public function getSubtotal($skipTax = false)
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            $subtotal = $totals['subtotal']->getValue();
            if (!$skipTax) {
                if ((!$this->helper('tax')->displayCartBothPrices()) && $this->helper('tax')->displayCartPriceInclTax()) {
                    $subtotal = $this->_addTax($subtotal);
                }
            }
        }
        return $subtotal;
    }

    /**
     * Get subtotal, including tax.
     * Will return > 0 only if appropriate config settings are enabled.
     *
     * @return decimal
     */
    public function getSubtotalInclTax()
    {
        if (!$this->helper('tax')->displayCartBothPrices()) {
            return 0;
        }
        return $this->_addTax($this->getSubtotal(true));
    }

    private function _addTax($price) {
        $totals = $this->getTotals();
        if (isset($totals['tax'])) {
            $price += $totals['tax']->getValue();
        }
        return $price;
    }

    public function getSummaryCount()
    {
        return Mage::getSingleton('checkout/cart')->getSummaryQty();
    }

    public function getIncExcTax($flag)
    {
        $text = Mage::helper('tax')->getIncExcText($flag);
        return $text ? ' ('.$text.')' : '';
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout();
    }

    public function getCheckoutUrl()
    {
        return $this->helper('checkout/url')->getCheckoutUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if ((bool) Mage::app()->getStore()->getConfig('checkout/sidebar/display')) {
            $html = parent::_toHtml();
        }
        return $html;
    }
}