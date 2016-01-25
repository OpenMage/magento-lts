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

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Fixture\CheckoutAgreement;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\ObjectManager;
use Mage\Checkout\Test\Page\CheckoutMultishippingOverview;
use Magento\Mtf\TestStep\TestStepFactory;

/**
 * Check that Terms and Conditions is present on the last checkout step - Order Review.
 */
class AssertTermRequireMessageOnMultishippingCheckout extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Expected notification message.
     */
    const NOTIFICATION_MESSAGE = 'Please agree to all Terms and Conditions before placing the orders.';

    /**
     * Test step factory.
     *
     * @var TestStepFactory
     */
    protected $stepFactory;

    /**
     * Check that clicking "Place order" without setting checkbox for agreement will result in error message displayed
     * under condition.
     *
     * @param CheckoutMultishippingOverview $checkoutMultishippingOverview
     * @param TestStepFactory $stepFactory
     * @param Customer $customer
     * @param CheckoutAgreement $checkoutAgreement
     * @param string $products
     * @param array $payment
     * @param array $shippingData
     * @param array $fillItemsData
     * @param string $newAddresses
     * @return void
     */
    public function processAssert(
        CheckoutMultishippingOverview $checkoutMultishippingOverview,
        TestStepFactory $stepFactory,
        Customer $customer,
        CheckoutAgreement $checkoutAgreement,
        $products,
        array $payment,
        array $shippingData,
        array $fillItemsData,
        $newAddresses
    ) {
        $this->stepFactory = $stepFactory;
        $customer->persist();
        $products = $this->createProducts($products);
        $this->login($customer);
        $this->addToCart($products);
        $this->startCheckout();
        $this->processCheckoutWithMultishipping(
            $customer,
            $products,
            $fillItemsData,
            $shippingData,
            $payment,
            $newAddresses
        );

        $alertText = $checkoutMultishippingOverview->getOverviewBlock()->clickContinue();
        \PHPUnit_Framework_Assert::assertEquals(
            self::NOTIFICATION_MESSAGE,
            $alertText,
            'Notification required message of Terms and Conditions is absent.'
        );

        $checkoutMultishippingOverview->getOverviewBlock()->setAgreement($checkoutAgreement, 'Yes');
        $checkoutMultishippingOverview->getOverviewBlock()->clickContinue();
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        return $this->stepFactory->create('\Mage\Catalog\Test\TestStep\CreateProductsStep', ['products' => $products])
            ->run()['products'];
    }

    /**
     * Login customer on frontend.
     *
     * @param Customer $customer
     * @return void
     */
    protected function login(Customer $customer)
    {
        $this->stepFactory
            ->create('\Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep', ['customer' => $customer])
            ->run();

    }

    /**
     * Add products to cart.
     *
     * @param array $products
     * @return void
     */
    protected function addToCart(array $products)
    {
        $this->stepFactory->create(
            '\Mage\Checkout\Test\TestStep\AddProductsToTheCartStep',
            ['products' => $products]
        )->run();
    }

    /**
     * Start checkout with multishipping.
     *
     * @return void
     */
    protected function startCheckout()
    {
        $this->stepFactory->create('\Mage\Checkout\Test\TestStep\ProceedToCheckoutWithMultishippingStep')->run();
    }

    /**
     * Process checkout with multishipping.
     *
     * @param Customer $customer
     * @param array $products
     * @param array $fillItemsData
     * @param array $shippingData
     * @param array $payment
     * @param string $newAddresses
     * @return void
     */
    protected function processCheckoutWithMultishipping(
        Customer $customer,
        array $products,
        array $fillItemsData,
        array $shippingData,
        array $payment,
        $newAddresses
    ) {
        $newAddresses = $this->stepFactory->create(
            '\Mage\Customer\Test\TestStep\CreateNewAddressesFixturesStep',
            ['newAddresses' => $newAddresses]
        )->run()['newAddresses'];

        $addresses = $this->stepFactory->create(
            '\Mage\Checkout\Test\TestStep\EnterNewAddressesStep',
            ['newAddresses' => $newAddresses, 'customer' => $customer]
        )->run()['addresses'];

        $addresses = $this->stepFactory->create(
            '\Mage\Checkout\Test\TestStep\SelectAddressesStep',
            [
                'products' => $products,
                'customer' => $customer,
                'fillItemsData' => $fillItemsData,
                'addresses' => $addresses
            ]
        )->run()['addresses'];

        $this->stepFactory->create(
            '\Mage\Checkout\Test\TestStep\FillShippingMethodWithMultishippingStep',
            ['shippingData' => $shippingData, 'addresses' => $addresses]
        )->run();

        $this->stepFactory->create(
            '\Mage\Checkout\Test\TestStep\SelectPaymentMethodWithMultishippingStep',
            ['payment' => $payment]
        )->run();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Validation error message for terms and conditions checkbox is present on multishipping checkout.';
    }
}
