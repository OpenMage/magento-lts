<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\TestCase;

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create products.
 * 2. Create a customer.
 * 3. Create order.
 *
 * Steps:
 * 1. Go to backend.
 * 2. Open Sales > Orders.
 * 3. Open the created order.
 * 4. Do 'Reorder' for placed order.
 * 5. Perform all assertions.
 *
 * @group Order_Management_(CS)
 * @ZephyrId MPERF-7655
 */
class ReorderOrderEntityTest extends Scenario
{
    /**
     * Delete all tax rules.
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create('\Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Reorder created order.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }
}
