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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Minicart
{
    const XML_PATH_CHECKOUT_SIDEBAR_COUNT                  = 'checkout/sidebar/count';
    const XML_PATH_CHECKOUT_MINICART_VISIBLE_ITEMS_COUNT   = 'checkout/cart/minicart_visible_items';

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
    }

    /**
     * Retrieve count of display recently added items
     *
     * @return int
     */
    public function getItemCount()
    {
        $count = $this->getData('item_count');
        if (is_null($count)) {
            $count = Mage::getStoreConfig(self::XML_PATH_CHECKOUT_SIDEBAR_COUNT);
            $this->setData('item_count', $count);
        }
        return $count;
    }

    /**
     * Get array of last added items
     *
     * @param null $count
     * @return array
     */
    public function getRecentItems($count = null)
    {
        if (!$this->getSummaryCount()) {
            return array();
        }
        if ($count === null) {
            $count = $this->getItemCount();
        }
        return array_slice(array_reverse($this->getItems()), 0, $count);
    }

    /**
     * Get shopping cart subtotal.
     *
     * It will include tax, if required by config settings.
     *
     * @param   bool $skipTax flag for getting price with tax or not. Ignored in case when we display just subtotal incl.tax
     * @return  decimal
     */
    public function getSubtotal($skipTax = true)
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        $config = Mage::getSingleton('tax/config');
        if (isset($totals['subtotal'])) {
            if ($config->displayCartSubtotalBoth()) {
                if ($skipTax) {
                    $subtotal = $totals['subtotal']->getValueExclTax();
                } else {
                    $subtotal = $totals['subtotal']->getValueInclTax();
                }
            } elseif($config->displayCartSubtotalInclTax()) {
                $subtotal = $totals['subtotal']->getValueInclTax();
            } else {
                $subtotal = $totals['subtotal']->getValue();
                if (!$skipTax && isset($totals['tax'])) {
                    $subtotal+= $totals['tax']->getValue();
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
        if (!Mage::getSingleton('tax/config')->displayCartSubtotalBoth()) {
            return 0;
        }
        return $this->getSubtotal(false);
    }

    /**
     * Add tax to amount
     *
     * @param float $price
     * @param bool $exclShippingTax
     * @return float
     */
    private function _addTax($price, $exclShippingTax=true) {
        $totals = $this->getTotals();
        if (isset($totals['tax'])) {
            if ($exclShippingTax) {
                $price += $totals['tax']->getValue()-$this->_getShippingTaxAmount();
            } else {
                $price += $totals['tax']->getValue();
            }
        }
        return $price;
    }

    /**
     * Get shipping tax amount
     *
     * @return float
     */
    protected function _getShippingTaxAmount()
    {
        $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
        return $quote->getShippingAddress()->getShippingTaxAmount();
    }

    /**
     * Get incl/excl tax label
     *
     * @param bool $flag
     * @return string
     */
    public function getIncExcTax($flag)
    {
        $text = Mage::helper('tax')->getIncExcText($flag);
        return $text ? ' ('.$text.')' : '';
    }

    /**
     * Check if one page checkout is available
     *
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout() && !$this->getQuote()->getHasError();
    }

    /**
     * Get one page checkout page url
     *
     * @return bool
     */
    public function getCheckoutUrl()
    {
        return $this->helper('checkout/url')->getCheckoutUrl();
    }

    /**
     * Define if Shopping Cart Sidebar enabled
     *
     * @return bool
     */
    public function getIsNeedToDisplaySideBar()
    {
        return (bool) Mage::app()->getStore()->getConfig('checkout/sidebar/display');
    }

    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomQuote()) {
            return $this->getCustomQuote()->getAllVisibleItems();
        }

        return parent::getItems();
    }

    /*
     * Return totals from custom quote if needed
     *
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
            $this->_totals = $quote->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Get cache key informative items
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = parent::getCacheKeyInfo();
        $cacheKeyInfo['item_renders'] = $this->_serializeRenders();
        return $cacheKeyInfo;
    }

    /**
     * Serialize renders
     *
     * @return string
     */
    protected function _serializeRenders()
    {
        $result = array();
        foreach ($this->_itemRenders as $type => $renderer) {
            $result[] = implode('|', array($type, $renderer['block'], $renderer['template']));
        }
        return implode('|', $result);
    }

    /**
     * Deserialize renders from string
     *
     * @param string $renders
     * @return Mage_Checkout_Block_Cart_Sidebar
     */
    public function deserializeRenders($renders)
    {
        if (!is_string($renders)) {
            return $this;
        }

        $renders = explode('|', $renders);
        while (!empty($renders)) {
            $template = array_pop($renders);
            $block = array_pop($renders);
            $type = array_pop($renders);
            if (!$template || !$block || !$type) {
                continue;
            }
            $this->addItemRender($type, $block, $template);
        }

        return $this;
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        $quoteTags = $this->getQuote()->getCacheIdTags();

        $items = array();
        /** @var $item Mage_Sales_Model_Quote_Item */
        foreach ($this->getItems() as $item) {
            $items[] = $item->getProduct();
       }

        return array_merge(
            parent::getCacheTags(),
            (!$quoteTags)? array() : $quoteTags,
            $this->getItemsTags($items)
        );
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        $transport = new Varien_Object();
        $transport->setHtml($html);
        Mage::dispatchEvent(
            'checkout_block_cart_sidebar_aftertohtml',
            array(
                'block' => $this,
                'transport' => $transport,
            )
        );
        return $transport->getHtml();
    }
}
