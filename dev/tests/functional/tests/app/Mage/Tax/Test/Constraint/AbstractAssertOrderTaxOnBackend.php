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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\Constraint;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderInvoiceNew;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreditMemoNew;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\ObjectManager;
use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Mtf\System\Event\EventManagerInterface;
use Mage\Sales\Test\Page\OrderView;

/**
 * Checks that prices displayed excluding tax in order are correct on backend.
 */
abstract class AbstractAssertOrderTaxOnBackend extends AbstractAssertTaxCalculationAfterCheckout
{
    /**
     * Price types.
     *
     * @var array
     */
    protected $priceTypes = [
        'order_prices' => 'Order',
        'invoice_prices' => 'InvoiceNew',
        'credit_memo_prices' => 'CreditMemoNew'
    ];

    /**
     * Order view page.
     *
     * @var SalesOrderInvoiceNew
     */
    protected $orderInvoiceNew;

    /**
     * Order view page.
     *
     * @var SalesOrderCreditMemoNew
     */
    protected $orderCreditMemoNew;

    /**
     * Order index page.
     *
     * @var SalesOrderIndex
     */
    protected $salesOrder;

    /**
     * Implementation for get invoice creation page total prices function.
     *
     * @return array
     */
    abstract protected function getInvoiceNewTotals();

    /**
     * Implementation for get credit memo creation page total prices function.
     *
     * @return array
     */
    abstract protected function getCreditMemoNewTotals();

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param EventManagerInterface $eventManager
     * @param \Mage\Sales\Test\Page\Adminhtml\SalesOrderView $salesOrderView
     * @param SalesOrderIndex $orderIndex
     * @param SalesOrderInvoiceNew $orderInvoiceNew
     * @param SalesOrderCreditMemoNew $orderCreditMemoNew
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param OrderView $orderView
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        SalesOrderView $salesOrderView,
        SalesOrderIndex $orderIndex,
        SalesOrderInvoiceNew $orderInvoiceNew,
        SalesOrderCreditMemoNew $orderCreditMemoNew,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        OrderView $orderView
    ) {
        parent::__construct($objectManager, $eventManager, $checkoutOnepage, $checkoutOnepageSuccess, $orderView);
        $this->orderView = $salesOrderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderCreditMemoNew = $orderCreditMemoNew;
        $this->orderIndex = $orderIndex;
    }

    /**
     * Assert that specified prices are actual on order, invoice and refund pages.
     *
     * @param InjectableFixture $product
     * @param array $prices
     * @param array $arguments [optional]
     * @return void
     */
    public function processAssert(InjectableFixture $product, array $prices, array $arguments = null)
    {
        $prices = $this->prepareVerifyFields($prices);

        $this->orderIndex->open()->getSalesOrderGrid()->openFirstRow();
        $this->assertOrderPrices($product, $prices);

        $this->assertInvoicePrices($product, $prices);

        //Check prices after invoice on order page
        $this->orderInvoiceNew->getFormBlock()->submit();
        $this->assertOrderPrices($product, $prices);

        $this->assertCreditMemoPrices($product, $prices);

        //Check prices after refund on order page
        $this->orderCreditMemoNew->getFormBlock()->submit();
        $this->assertOrderPrices($product, $prices);
    }

    /**
     * Assert credit memo prices.
     *
     * @param InjectableFixture $product
     * @param array $prices
     * @return void
     */
    protected function assertCreditMemoPrices(InjectableFixture $product, array $prices)
    {
        $this->orderView->getPageActions()->refund();
        $error = $this->verifyData(
            $this->preparePricesCreditMemo($prices),
            $this->getActualPrices($product, 'credit_memo_prices')
        );
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Assert invoice prices.
     *
     * @param InjectableFixture $product
     * @param array $prices
     * @return void
     */
    protected function assertInvoicePrices(InjectableFixture $product, array $prices)
    {
        $this->orderView->getPageActions()->invoice();
        $error = $this->verifyData($prices, $this->getActualPrices($product, 'invoice_prices'));
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Prepare prices for credit memo.
     *
     * @param array $prices
     * @return array
     */
    protected function preparePricesCreditMemo(array $prices)
    {
        unset($prices['shipping_excl_tax']);
        unset($prices['shipping_incl_tax']);
        return $prices;
    }

    /**
     * Get invoice new product prices.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getInvoiceNewPrices(InjectableFixture $product)
    {
        $productBlock = $this->orderInvoiceNew->getFormBlock()->getItemsBlock()->getItemProductBlock($product);
        return $this->getTypePrices($productBlock);
    }

    /**
     * Get Credit Memo new product prices.
     *
     * @param InjectableFixture $product
     * @return array
     */
    public function getCreditMemoNewPrices(InjectableFixture $product)
    {
        $productBlock = $this->orderCreditMemoNew->getFormBlock()->getItemsBlock()->getItemProductBlock($product);
        return $this->getTypePrices($productBlock);
    }
}
