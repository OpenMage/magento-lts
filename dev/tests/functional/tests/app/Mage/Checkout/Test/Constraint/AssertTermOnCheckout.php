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

use Mage\Customer\Test\Fixture\Address;
use Mage\Checkout\Test\Page\CheckoutOnepage;
use Mage\Checkout\Test\Page\CheckoutOnepageSuccess;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\ObjectManager;
use Mage\Checkout\Test\Fixture\CheckoutAgreement;

/**
 * Check that Terms and Conditions is present on the last checkout step - Order Review.
 */
class AssertTermOnCheckout extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Notification message.
     */
    const NOTIFICATION_MESSAGE = 'Please agree to all the terms and conditions before placing the order.';

    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Check that checkbox is present on the last checkout step - Order Review.
     * Check that after Place order without click on checkbox "Terms and Conditions" order was not successfully placed.
     * Check that after clicking on "Terms and Conditions" checkbox and "Place Order" button success place order message
     * appears.
     *
     * @param ObjectManager $objectManager
     * @param Address $billingAddress
     * @param CheckoutOnepage $checkoutOnepage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param AssertOrderSuccessPlacedMessage $assertOrderSuccessPlacedMessage
     * @param CheckoutAgreement $checkoutAgreement
     * @param string $checkoutMethod
     * @param string $products
     * @param array $shipping
     * @param array $payment
     * @return void
     */
    public function processAssert(
        ObjectManager $objectManager,
        Address $billingAddress,
        CheckoutOnepage $checkoutOnepage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        AssertOrderSuccessPlacedMessage $assertOrderSuccessPlacedMessage,
        CheckoutAgreement $checkoutAgreement,
        $checkoutMethod,
        $products,
        array $shipping,
        array $payment
    ) {
        $this->objectManager = $objectManager;
        $products = $this->createProducts($products);
        $this->addToCart($products);
        $this->startCheckout();
        $this->processCheckout($checkoutMethod, $billingAddress, $shipping, $payment);
        $alertText = $checkoutOnepage->getReviewBlock()->clickContinue();

        \PHPUnit_Framework_Assert::assertEquals(
            self::NOTIFICATION_MESSAGE,
            $alertText,
            'Notification required message of Terms and Conditions is absent.'
        );

        $checkoutOnepage->getReviewBlock()->setAgreement($checkoutAgreement, 'Yes');
        $checkoutOnepage->getReviewBlock()->clickContinue();
        $assertOrderSuccessPlacedMessage->processAssert($checkoutOnepageSuccess);
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        return $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        )->run()['products'];
    }

    /**
     * Add products to cart.
     *
     * @param array $products
     * @return void
     */
    protected function addToCart(array $products)
    {
        $this->objectManager->create(
            'Mage\Checkout\Test\TestStep\AddProductsToTheCartStep',
            ['products' => $products]
        )->run();
    }

    /**
     * Start checkout.
     *
     * @return void
     */
    protected function startCheckout()
    {
        $this->objectManager->create('Mage\Checkout\Test\TestStep\ProceedToCheckoutStep')->run();
    }

    /**
     * Process checkout.
     *
     * @param string $checkoutMethod
     * @param Address $billingAddress
     * @param array $shipping
     * @param array $payment
     * @return void
     */
    protected function processCheckout($checkoutMethod, Address $billingAddress, array $shipping, array $payment)
    {
        $this->objectManager->create(
            'Mage\Checkout\Test\TestStep\SelectCheckoutMethodStep',
            ['checkoutMethod' => $checkoutMethod]
        )->run();
        $this->objectManager->create(
            'Mage\Checkout\Test\TestStep\FillBillingInformationStep',
            ['billingAddress' => $billingAddress]
        )->run();
        $this->objectManager->create(
            'Mage\Checkout\Test\TestStep\FillShippingMethodStep',
            ['shipping' => $shipping]
        )->run();
        $this->objectManager->create(
            'Mage\Checkout\Test\TestStep\SelectPaymentMethodStep',
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
        return 'Order was placed with checkout agreement successfully.';
    }
}
