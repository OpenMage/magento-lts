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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\Constraint;

use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Checkout\Test\Block\AbstractItem;
use Magento\Mtf\ObjectManager;
use Mage\Checkout\Test\Block\Cart\Totals;

/**
 * Abstract assert for check taxes.
 */
abstract class AbstractAssertTax extends AbstractAssertForm
{
    /**
     * Verify fields for assert.
     *
     * @var array
     */
    protected $verifyFields = [
        'subtotal_excl_tax',
        'subtotal_incl_tax',
        'discount',
        'shipping_excl_tax',
        'shipping_incl_tax',
        'grand_total_excl_tax',
        'grand_total_incl_tax',
        'tax'
    ];

    /**
     * Verifiable fields for cart items.
     *
     * @var array
     */
    protected $cartItemVerifiableFields = [
        'cart_item_price_excl_tax',
        'cart_item_price_incl_tax',
        'cart_item_subtotal_excl_tax',
        'cart_item_subtotal_incl_tax'
    ];

    /**
     * Price types.
     *
     * @var array
     */
    protected $priceTypes = ['order_prices' => 'Order'];

    /**
     * Order view page on backend.
     *
     * @var SalesOrderView
     */
    protected $orderView;

    /**
     * Assert order prices.
     *
     * @param InjectableFixture $product
     * @param array $prices
     * @return void
     */
    protected function assertOrderPrices(InjectableFixture $product, array $prices)
    {
        $error = $this->verifyData($prices, $this->getActualPrices($product, 'order_prices'));
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Get actual prices.
     *
     * @param InjectableFixture $product
     * @param string $pricesType
     * @return array
     */
    protected function getActualPrices(InjectableFixture $product, $pricesType)
    {
        return array_merge(
            $this->{'get' . $this->priceTypes[$pricesType] . 'Prices'}($product),
            $this->{'get' . $this->priceTypes[$pricesType] . 'Totals'}()
        );
    }

    /**
     * Unset category and product page expected prices.
     *
     * @param array $prices
     * @return array
     */
    protected function preparePrices(array $prices)
    {
        $resultTotalPrices = array_intersect_key($prices, array_flip($this->verifyFields));
        $resultItemPrices = array_intersect_key($prices, array_flip($this->cartItemVerifiableFields));

        return array_merge($resultTotalPrices, $resultItemPrices);
    }

    /**
     * Get order product prices.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getOrderPrices(InjectableFixture $product)
    {
        $viewBlock = $this->orderView->getItemsOrderedBlock()->getItemProductBlock($product);
        return $this->getTypePrices($viewBlock);
    }

    /**
     * Get order totals.
     *
     * @return array
     */
    public function getOrderTotals()
    {
        $totalsBlock = $this->orderView->getOrderTotalsBlock();
        return $this->getTypeBlockData($totalsBlock);
    }

    /**
     * Get type prices.
     *
     * @param AbstractItem $block
     * @return array
     */
    protected function getTypePrices(AbstractItem $block)
    {
        $result = [];
        foreach ($this->cartItemVerifiableFields as $field) {
            $result[$field] = $block->getCartItemTypePrice($field);
        }

        return $result;
    }

    /**
     * Get data from block.
     *
     * @param Totals $block
     * @return array
     */
    protected function getTypeBlockData(Totals $block)
    {
        $result = [];
        foreach ($this->verifyFields as $field) {
            $result[$field] = $block->getData($field);
        }

        return $result;
    }

    /**
     * Prepare verify fields for assert.
     *
     * @param array $prices
     * @return array
     */
    protected function prepareVerifyFields(array $prices)
    {
        $prices = $this->preparePrices($prices);
        foreach ($this->verifyFields as $field) {
            if(!isset($prices[$field])) {
                $this->verifyFields = array_diff($this->verifyFields, [$field]);
            }
        }

        return $prices;
    }
}
