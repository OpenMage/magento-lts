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

namespace Mage\Paypal\Test\TestCase;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create product according to dataSet.
 * 2. Apply configuration for test.
 * 3. Create tax rule for us customers.
 *
 * Steps:
 * 1. Go to frontend as non registered customer.
 * 2. Add product to Shopping Cart from product page.
 * 3. Proceed OnePageCheckout as a Guest till 'Payment Information step'.
 * 4. Select Payment Method and click "Continue".
 * 5. Login to PayPal using test buyer credentials.
 * 6. Click "Pay Now" button.
 * 7. Perform asserts.
 *
 * @group One_Page_Checkout_(CS), PayPal_(CS)
 * @ZephyrId MPERF-7055
 */
class CreateOrderWithPayPalStandardTest extends Scenario
{
    /* tags */
    const TEST_TYPE = '3rd_party_test';
    /* end tags */

    /**
     * Prepare environment for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        // Delete existing tax rules.
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();

        // Create US tax rule
        $taxRule = $fixtureFactory->createByCode('taxRule', ['dataSet' => 'us_tax_rule']);
        $taxRule->persist();
    }

    /**
     * Create order with PayPal standard test.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Disable enabled config after test.
     *
     * @return void
     */
    public function tearDown()
    {
        // Rollback configuration.
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
    }

    /**
     * Delete all tax rules after test.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        ObjectManager::getInstance()->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }
}
