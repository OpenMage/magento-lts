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
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create product.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add product to the cart.
 * 3. Click the 'Proceed to Checkout' button.
 * 4. Select checkout method according to dataset.
 * 5. Fill billing information and select the 'Ship to this address' option.
 * 6. Select shipping method.
 * 7. Select payment method (use reward points and store credit if available).
 * 8. Place order.
 * 9. Perform assertions.
 *
 * @group One_Page_Checkout_(CS)
 * @ZephyrId MPERF-6811
 */
class OnePageCheckoutTest extends Scenario
{
    /**
     * Customer logout page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Preparing pages for tearDown.
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
     * Runs one page checkout test.
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
        $this->customerAccountLogout->open();
        if (isset($this->currentVariation['arguments']['configData'])) {
            $this->objectManager->create(
                'Mage\Core\Test\TestStep\SetupConfigurationStep',
                ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
            )->run();
        }
    }
}
