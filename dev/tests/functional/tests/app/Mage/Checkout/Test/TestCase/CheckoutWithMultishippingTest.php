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

namespace Mage\Checkout\Test\TestCase;

use Mage\Customer\Test\Page\CustomerAccountLogout;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Setup configuration.
 * 2. Create customer with two and more addresses.
 * 3. Create products.
 *
 * Steps:
 * 1. Login to frontend.
 * 2. Add product to cart.
 * 3. Start checkout with multishipping.
 * 4. Process checkout with multishipping.
 * 5. Perform asserts.
 *
 * @group Multi_Address_Checkout_(CS)
 * @ZephyrId MPERF-7418
 */
class CheckoutWithMultishippingTest extends Scenario
{
    /**
     * Customer logout page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Prepare data for test.
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(CustomerAccountLogout $customerAccountLogout)
    {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->objectManager->create('\Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Runs checkout with multishipping test.
     *
     * @param Customer $customer
     * @return array
     */
    public function test(Customer $customer)
    {
        $this->executeScenario();
        return ['billingAddress' => $this->getBillingAddress($customer)];
    }

    /**
     * Get billing address for asserts.
     *
     * @param Customer $customer
     * @return null|Address
     */
    protected function getBillingAddress(Customer $customer)
    {
        return $customer->hasData('address')
            ? $customer->getDataFieldConfig('address')['source']->getAddresses()[0]
            : null;
    }

    /**
     * Logout after variation.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
