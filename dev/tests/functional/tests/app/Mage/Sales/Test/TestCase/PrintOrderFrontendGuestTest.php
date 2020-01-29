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

namespace Mage\Sales\Test\TestCase;

use Mage\Sales\Test\Fixture\Order;
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create order.
 *
 * Steps:
 * 1. Find the Order on frontend.
 * 2. Navigate to: Orders and Returns.
 * 3. Fill the form with correspondent Order data.
 * 4. Click on the "Continue" button.
 * 5. Click on the "Print Order" button.
 * 6. Perform appropriate assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7532
 */
class PrintOrderFrontendGuestTest extends Scenario
{
    /**
     * Prepare data.
     *
     * @return void
     */
    public function __prepare()
    {
        $this->markTestIncomplete('Bugs:
        MPERF-5053: Some total models are not displayed in a guest view,
        MPERF-7349: Can not find order(created on backend) on "Orders and Returns" page (on frontend)'
        );

        $this->objectManager->create('\Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Run print order on frontend test.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }
}
