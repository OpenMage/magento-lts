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
 * 1. Create product.
 *
 * Steps:
 * 1. Go to Frontend.
 * 2. Add product to the cart.
 * 3. Click the 'Proceed to Checkout' button.
 * 4. Select checkout method according to dataSet.
 * 5. Fill billing information and select the 'Ship to this address' option.
 * 6. Select shipping method according to dataSet.
 * 7. Select payment method according to dataSet.
 * 8. Place order.
 * 9. Perform assertions.
 *
 * @group One_Page_Checkout_(CS)
 * @ZephyrId MPERF-7006
 */
class OnePageCheckoutWithinOnlineShippingMethods extends Scenario
{
    /**
     * Prepare product for test.
     *
     * @return array
     */
    public function __prepare()
    {
        return ['products' => $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => 'catalogProductSimple::order_default']
        )->run()['products']];
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
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();

        $this->objectManager->create(
            'Mage\CurrencySymbol\Test\TestStep\ApplyCurrencyInConfigStep',
            ['currencySymbols' => 'usd']
        )->run();
    }
}
