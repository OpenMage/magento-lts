<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart abstract block
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
abstract class Mage_Checkout_Block_Cart_Abstract extends Mage_Core_Block_Template
{
    protected $_customer = null;
    protected $_checkout = null;
    protected $_quote    = null;

    protected $_totals;
    protected $_itemRenders = [];

    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/cart/item/default.phtml');
    }

    /**
     * Add renderer for item product type
     *
     * @param   string $productType
     * @param   string $blockType
     * @param   string $template
     * @return  Mage_Checkout_Block_Cart_Abstract
     */
    public function addItemRender($productType, $blockType, $template)
    {
        $this->_itemRenders[$productType] = [
            'block' => $blockType,
            'template' => $template,
            'blockInstance' => null,
        ];
        return $this;
    }

    /**
     * Get renderer information by product type code
     *
     * @deprecated please use getItemRendererInfo() method instead
     * @see getItemRendererInfo()
     * @param   string $type
     * @return  array
     */
    public function getItemRender($type)
    {
        return $this->getItemRendererInfo($type);
    }

    /**
     * Get renderer information by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRendererInfo($type)
    {
        return $this->_itemRenders[$type] ?? $this->_itemRenders['default'];
    }

    /**
     * Get renderer block instance by product type code
     *
     * @param   string $type
     * @return  array
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }
        if (is_null($this->_itemRenders[$type]['blockInstance'])) {
            $this->_itemRenders[$type]['blockInstance'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                    ->setTemplate($this->_itemRenders[$type]['template'])
                    ->setRenderedBlock($this);
        }

        return $this->_itemRenders[$type]['blockInstance'];
    }

    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->_customer === null) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        if ($this->_checkout === null) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }

    /**
     * Get active quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if ($this->_quote === null) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Get all cart items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Get item row html
     *
     * @return  string
     */
    public function getItemHtml(Mage_Sales_Model_Quote_Item $item)
    {
        /** @var Mage_Checkout_Block_Cart_Item_Renderer $renderer */
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item);
        return $renderer->toHtml();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->getTotalsCache();
    }

    /**
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Check if can apply msrp to totals
     *
     * @return bool
     */
    public function canApplyMsrp()
    {
        if (!$this->getQuote()->hasCanApplyMsrp() && Mage::helper('catalog')->isMsrpEnabled()) {
            $this->getQuote()->collectTotals();
        }
        return $this->getQuote()->getCanApplyMsrp();
    }
}
