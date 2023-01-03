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

namespace Mage\Checkout\Test\TestCase;

use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Scenario;

/**
 * Preconditions:
 * 1. Enable "Terms and Conditions".
 *
 * Steps:
 * 1. Open on backend Sales > Terms and conditions.
 * 2. Click "Add New Condition".
 * 3. Fill data from dataset.
 * 4. Click "Save Condition".
 * 5. Perform all assertions.
 *
 * @group Terms_and_Conditions_(CS)
 * @ZephyrId MPERF-7583
 */
class CreateTermEntityTest extends Scenario
{
    /**
     * Create Term Entity test.
     *
     * @return void
     */
    public function test()
    {
        $this->executeScenario();
    }

    /**
     * Clear data after test variation.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Checkout\Test\TestStep\DeleteAllTermsEntityStep')->run();
    }

    /**
     * Clear data after test suite.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        ObjectManager::getInstance()->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'checkout_term_condition', 'rollback' => true]
        )->run();
    }
}
