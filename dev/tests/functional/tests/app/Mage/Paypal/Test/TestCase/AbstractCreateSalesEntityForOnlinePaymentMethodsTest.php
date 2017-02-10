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

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Scenario;

/**
 * Abstract class for create sales entity for online payment methods.
 */
abstract class AbstractCreateSalesEntityForOnlinePaymentMethodsTest extends Scenario
{
    /**
     * Prepare magento customer for test and delete all tax rules.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $magentoCustomer = $fixtureFactory->create('Mage\Customer\Test\Fixture\Customer', ['dataset' => 'default']);
        $magentoCustomer->persist();

        return ['customer' => $magentoCustomer];
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
     * Disable including config and delete all tax rules after test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
        $this->objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->currentVariation['arguments']['configData'], 'rollback' => true]
        )->run();
    }
}
