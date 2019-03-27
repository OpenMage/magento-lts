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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\TestCase;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Create simple product.
 * 2. Create Tax Rule with tax data Retail Customer UK-Full Tax.
 * 3. Configure and enable VAT functionality.
 * 4. Create Customer with data of UK Customer and VAT Number for UK.
 *
 * Steps:
 * 1. Go to frontend as Logged In Customer.
 * 2. Add simple product to Shopping Cart from product page.
 * 3. Go to Shopping Cart.
 * 4. Process OnePageCheckout as a Logged In Customer.
 * 5. Process asserts.
 *
 * @group Tax_(CS)
 * @ZephyrId MPERF-7646
 */
class AutomaticTaxApplyingBasedOnVatIdTest extends Scenario
{
    /**
     * Run Automatic tax applying based on Vat Id test.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Delete all tax rules after test and rollback configuration.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $objectManager = ObjectManager::getInstance();
        $objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'enable_vat_rollback']
        )->run();
    }
}
