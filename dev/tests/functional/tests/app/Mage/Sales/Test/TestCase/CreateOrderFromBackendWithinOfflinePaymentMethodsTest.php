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

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create customer according to dataset.
 * 2. Create products.
 *
 * Steps:
 * 1. Open Backend.
 * 2. Open Sales -> Orders.
 * 3. Click Create New Order.
 * 4. Select Customer created in preconditions.
 * 5. Add Products according to dataset.
 * 6. Fill data according dataset.
 * 7. Click Update Product qty.
 * 8. Fill data according dataset.
 * 9. Click Get Shipping Method and rates.
 * 10. Fill data according dataset.
 * 11. Submit Order.
 * 12. Perform all assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7098
 */
class CreateOrderFromBackendWithinOfflinePaymentMethodsTest extends Scenario
{
    /**
     * Prepare environment for test.
     *
     * @return void
     */
    public function __prepare()
    {
        // Delete existing tax rules.
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Run Create order from backend within offline payment methods test.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }
}
