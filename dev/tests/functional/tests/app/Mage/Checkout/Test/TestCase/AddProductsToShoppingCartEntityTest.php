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
