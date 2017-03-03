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

namespace Mage\Paypal\Test\TestCase;

use Magento\Mtf\TestCase\Scenario;
use Mage\Customer\Test\Page\CustomerAccountLogout;

/**
 * Preconditions:
 * 1. Create product.
 * 2. Apply configuration for test.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add products to the cart.
 * 3. Click the 'Proceed to Checkout' button.
 * 4. Select checkout method according to dataset.
 * 5. Fill billing information and select the 'Ship to this address' option.
 * 6. Select shipping method according to dataset.
 * 7. Select payment method according to dataset.
 * 8. Place order.
 * 9. Go to Sales > Orders.
 * 10. Select created order in the grid and open it.
 * 11. Click 'Invoice' button.
 * 12. Fill data according to dataset.
 * 13. Click 'Submit Invoice' button.
 * 14. Perform assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7231
 */
class CreateOnlineInvoiceForOnlinePaymentsMethodsWithIFrameTest extends Scenario
{
    /* tags */
    const TEST_TYPE = '3rd_party_test';
    /* end tags */

    /**
     * Customer logout page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Prepare environment for test.
     *
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __prepare(CustomerAccountLogout $customerAccountLogout)
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Create online invoice with online payments methods with i-frame.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Disable included config after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
    }
}
