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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CurrencySymbol\Test\TestCase;

use Mage\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencyIndex;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Mage\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;

/**
 * Test Creation for EditCurrencySymbolEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Apply custom currency in Config.
 * 2. Navigate to Stores->Manage Currency->Rates and click to Import button.
 * 3. Create simple product.
 *
 * Steps:
 * 1. Login to backend.
 * 2. Navigate to Stores->Manage Currency->Symbols.
 * 3. Make changes according to dataset.
 * 4. Click 'Save Currency Symbols' button.
 * 5. Perform all asserts.
 *
 * @group Currency_(PS)
 * @ZephyrId MPERF-6679
 */
class EditCurrencySymbolEntityTest extends Injectable
{
    /**
     * System Currency Symbol grid page.
     *
     * @var SystemCurrencySymbolIndex
     */
    protected $currencySymbolIndex;

    /**
     * System currency rates page.
     *
     * @var SystemCurrencyIndex
     */
    protected $currencyRatesIndex;

    /**
     * Fixture Factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Injection pages.
     *
     * @param SystemCurrencySymbolIndex $currencySymbolIndex
     * @param SystemCurrencyIndex $currencyRatesIndex
     * @return void
     */
    public function __inject(
        SystemCurrencySymbolIndex $currencySymbolIndex,
        SystemCurrencyIndex $currencyRatesIndex
    ) {
        $this->currencySymbolIndex = $currencySymbolIndex;
        $this->currencyRatesIndex = $currencyRatesIndex;
    }

    /**
     * Edit Currency Symbol Entity test.
     *
     * @param CurrencySymbolEntity $currencySymbol
     * @return CatalogProductSimple[]
     */
    public function test(CurrencySymbolEntity $currencySymbol)
    {
        // Preconditions:
        $this->applyCurrencyInConfig();
        $this->importCurrencyRates();
        $product = $this->createSimpleProductWithCategory();

        // Steps:
        $this->currencySymbolIndex->open();
        $this->currencySymbolIndex->getCurrencySymbolForm()->fill($currencySymbol);
        $this->currencySymbolIndex->getPageActions()->save();

        return ['product' => $product];
    }

    /**
     * Apply custom currency in Config.
     *
     * @return void
     */
    protected function applyCurrencyInConfig()
    {
        $config = $this->fixtureFactory->createByCode(
            'configData',
            ['dataSet' => 'config_currency_symbols_usd_and_uah']
        );
        $config->persist();
    }

    /**
     * Create simple product with category.
     *
     * @return CatalogProductSimple
     */
    protected function createSimpleProductWithCategory()
    {
        /**@var CatalogProductSimple $catalogProductSimple */
        $product = $this->fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $product->persist();
        return $product;
    }

    /**
     * Import currency rates for applied currencies.
     *
     * @return void
     */
    protected function importCurrencyRates()
    {
        $this->currencyRatesIndex->open();
        $this->currencyRatesIndex->getGridPageActions()->clickImportButton();
        $this->currencyRatesIndex->getGridPageActions()->saveCurrentRate();
    }

    /**
     * Disabling currency which has been added.
     *
     * @return void
     */
    public function tearDown()
    {
        $config = $this->fixtureFactory->createByCode('configData', ['dataSet' => 'config_currency_symbols_usd']);
        /** @var InjectableFixture $config */
        $config->persist();
    }
}
