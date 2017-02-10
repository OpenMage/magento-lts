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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Fixture\Cart;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that grand total is equal to expected.
 */
class AssertGrandTotalInShoppingCart extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that grand total is equal to expected.
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @param Customer|null $customer
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $cart, Customer $customer = null)
    {
        if ($customer !== null) {
            $this->login($customer);
        }
        $checkoutCart->open();
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($cart->getGrandTotal(), 2),
            $checkoutCart->getTotalsBlock()->getData('grand_total')
        );
    }

    /**
     * Login customer in frontend.
     *
     * @param Customer $customer
     * @return void
     */
    protected function login(Customer $customer)
    {
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Grand total price in the shopping cart equals to expected grand total price from data set.';
    }
}
