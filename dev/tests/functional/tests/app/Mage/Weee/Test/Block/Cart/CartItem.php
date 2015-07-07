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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Weee\Test\Block\Cart;

/**
 * Product item fpt block on cart page.
 */
class CartItem extends \Mage\Checkout\Test\Block\Cart\CartItem
{
    /**
     * Selector for price.
     *
     * @var string
     */
    protected $price = '.cart-tax-total';

    /**
     * Selector for fpt.
     *
     * @var string
     */
    protected $fpt = '.cart-tax-info .weee span';

    /**
     * Mapping for prices.
     *
     * @var array
     */
    protected $pricesType = [
        'price' => [
            'selector' => '.product-cart-price .cart-price .price'
        ],
        'subtotal' => [
            'selector' => '.product-cart-total .cart-price .price'
        ],
        'cart_item_price' => [
            'selector' => '.product-cart-price .cart-price .price'
        ],
        'cart_item_subtotal' => [
            'selector' => '.product-cart-total .cart-price .price'
        ],
        'price_fpt' => [
            'selector' => '.product-cart-price .weee .price'
        ],
        'price_fpt_total' => [
            'selector' => '.product-cart-price .cart-tax-total .weee .price'
        ],
        'subtotal_fpt' => [
            'selector' => '.product-cart-total .weee .price'
        ],
        'subtotal_fpt_total' => [
            'selector' => '.product-cart-total .cart-tax-total .weee .price'
        ],
        'cart_item_subtotal_incl_tax' => [
            'selector' => '.product-cart-total .cart-tax-total .price'
        ],
        'cart_item_price_incl_tax' => [
            'selector' => '.product-cart-price[data-rwd-tax-label="Incl. Tax"] .cart-tax-total .price'
        ]
    ];

    /**
     * Open fpt blocks.
     *
     * @return void
     */
    public function openFpt()
    {
        $fptBlocks = $this->_rootElement->getElements($this->fpt);
        $fptPricesBlocks = $this->_rootElement->getElements($this->price);
        foreach ($fptBlocks as $key => $fptBlock) {
            if (!$fptBlock->isVisible()) {
                $fptPricesBlocks[$key]->click();
            }
        }
    }
}
