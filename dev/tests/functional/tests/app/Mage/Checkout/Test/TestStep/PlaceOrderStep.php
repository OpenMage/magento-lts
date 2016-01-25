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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestStep;

use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Sales\Test\Fixture\Order;

/**
 * Place order in one page checkout.
 */
class PlaceOrderStep implements TestStepInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Onepage checkout page.
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * One page checkout success page.
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * Positive case flag.
     *
     * @var bool
     */
    protected $positiveCase;

    /**
     * Fixture of customer.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Array fixtures of products.
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param bool $positiveCase [optional]
     * @param Customer|null $customer
     * @param array $products [optional]
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        $positiveCase = true,
        Customer $customer = null,
        array $products = []
    ) {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->positiveCase = $positiveCase;
        $this->fixtureFactory = $fixtureFactory;
        $this->customer = $customer;
        $this->products = $products;
    }

    /**
     * Place order after checking order totals on review step.
     *
     * @return mixed
     */
    public function run()
    {
        $orderId = null;
        if ($this->positiveCase) {
            $this->checkoutOnepage->getReviewBlock()->clickContinue();
            $orderId = $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId();
        }

        return ['orderId' => $orderId, 'order' => $this->createOrderFixture($orderId)];
    }

    /**
     * Create order fixture.
     *
     * @param string $id
     * @return Order
     */
    protected function createOrderFixture($id)
    {
        $data = [
            'id' => $id,
            'customer_id' => ['customer' => $this->customer],
            'entity_id' => ['products' => $this->products]
        ];

        return $this->fixtureFactory->createByCode('order', ['data' => $data]);
    }
}
