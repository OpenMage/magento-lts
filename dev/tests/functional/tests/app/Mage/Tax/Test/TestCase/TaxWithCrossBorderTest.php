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

namespace Mage\Tax\Test\TestCase;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\CatalogRule\Test\Fixture\CatalogRule;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleIndex;
use Mage\CatalogRule\Test\Page\Adminhtml\CatalogRuleEdit;
use Mage\Customer\Test\Fixture\Customer;
use Mage\SalesRule\Test\Fixture\SalesRule;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;

/**
 * Steps:
 *
 * 1. Create Tax Rules with 3 different rates for different addresses.
 * 2. Create new product.
 * 3. Create Tax configuration.
 * 4. Create two customers that will match two different rates.
 * 5. Perform all assertions.
 *
 * @group Tax_(CS)
 * @ZephyrId MPERF-6815
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxWithCrossBorderTest extends Injectable
{
    /**
     * Fixture SalesRule.
     *
     * @var SalesRule
     */
    protected $salesRule;

    /**
     * Fixture CatalogRule.
     *
     * @var CatalogRule
     */
    protected $catalogRule;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Promo Quote Index page.
     *
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * Promo Quote Edit page.
     *
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * Catalog Rule Index page.
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndex;

    /**
     * Catalog Rule Edit page.
     *
     * @var CatalogRuleEdit
     */
    protected $catalogRuleEdit;

    /**
     * Prepare instance for test.
     *
     * @return void
     */
    public function __prepare()
    {
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Injection data.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param CatalogRuleIndex $catalogRuleIndex
     * @param CatalogRuleEdit $catalogRuleEdit
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        CatalogRuleIndex $catalogRuleIndex,
        CatalogRuleEdit $catalogRuleEdit,
        FixtureFactory $fixtureFactory
    ) {
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
        $this->catalogRuleIndex = $catalogRuleIndex;
        $this->catalogRuleEdit = $catalogRuleEdit;
        $this->fixtureFactory = $fixtureFactory;

        $taxRule = $fixtureFactory->createByCode('taxRule', ['dataset' => 'cross_border_tax_rule']);
        $taxRule->persist();

        return ['customers' => $this->createCustomers()];
    }

    /**
     * Create customers.
     *
     * @return array
     */
    protected function createCustomers()
    {
        $customersData = ['johndoe_unique_TX', 'johndoe_unique'];
        $customers = [];
        foreach ($customersData as $customerData) {
            $customer = $this->fixtureFactory->createByCode('customer', ['dataset' => $customerData]);
            $customer->persist();
            $customers[] = $customer;
        }

        return $customers;
    }

    /**
     * Test product prices with tax.
     *
     * @param CatalogProductSimple $product
     * @param string $config
     * @param string $salesRule
     * @param string $catalogRule
     * @return void
     */
    public function test(CatalogProductSimple $product, $config, $salesRule, $catalogRule)
    {
        // Steps:
        if ($salesRule !== "-") {
            $salesRule = $this->fixtureFactory->createByCode('salesRule', ['dataset' => $salesRule]);
            $salesRule->persist();
            $this->salesRule = $salesRule;
        }
        if ($catalogRule !== "-") {
            $catalogRule = $this->fixtureFactory->createByCode('catalogRule', ['dataset' => $catalogRule]);
            $catalogRule->persist();
            $this->catalogRule = $catalogRule;
        }
        $this->objectManager->create('Mage\Core\Test\TestStep\SetupConfigurationStep', ['configData' => $config])
            ->run();
        $product->persist();
    }

    /**
     * Delete sales rule, catalog rule, all tax rules and setup default tax configuration.
     *
     * @return void
     */
    public function tearDown()
    {
        if (isset($this->salesRule)) {
            $this->promoQuoteIndex->open();
            $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen(['name' => $this->salesRule->getName()]);
            $this->promoQuoteEdit->getFormPageActions()->delete();
            $this->salesRule = null;
        }
        if (isset($this->catalogRule)) {
            $this->catalogRuleIndex->open();
            $this->catalogRuleIndex->getCatalogRuleGrid()->searchAndOpen(['name' => $this->catalogRule->getName()]);
            $this->catalogRuleEdit->getFormPageActions()->delete();
            $this->catalogRule = null;
        }
        $this->objectManager->create('Mage\Tax\Test\TestStep\DeleteAllTaxRulesStep')->run();
    }

    /**
     * Rollback default configuration.
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $objectManager = ObjectManager::getInstance();
        $objectManager->create(
            'Mage\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => 'default_tax_configuration']
        )->run();
        $objectManager->create('\Mage\Tax\Test\TestStep\CreateTaxRuleStep', ['taxRule' => 'default'])->run();
    }
}
