<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\TestCase;

use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. All type products is created.
 *
 * Steps:
 * 1. Navigate to frontend.
 * 2. Open test product page.
 * 3. Add to cart test product.
 * 4. Perform all asserts.
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MPERF-7210
 */
class AddProductsToShoppingCartEntityTest extends Scenario
{
    /**
     * Prepare environment for test.
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Run test add products to shopping cart.
     *
     * @return array
     */
    public function test()
    {
        $this->executeScenario();
    }
}
