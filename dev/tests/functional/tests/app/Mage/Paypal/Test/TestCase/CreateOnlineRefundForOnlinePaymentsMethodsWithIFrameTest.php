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
 * 3. Process checkout.
 * 4. Go to Sales > Orders.
 * 5. Select created order in the grid and open it.
 * 6. Click 'Ship' button.
 * 7. Fill data according to dataset.
 * 8. Click 'Submit Ship' button.
 * 9. Click 'Invoice' button.
 * 10. Fill data according to dataset.
 * 11. Click 'Submit Invoice' button.
 * 12. Click 'Credit memo' button.
 * 13. Fill data according to dataset.
 * 14. Click 'Submit credit memo' button.
 * 15. Perform asserts.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7277
 */
class CreateOnlineRefundForOnlinePaymentsMethodsWithIFrameTest extends Scenario
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
     * Create online refund with online payments methods with i-frame.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Disable included config, delete all tax rules and logout after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $this->customerAccountLogout->open();
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
    }
}
