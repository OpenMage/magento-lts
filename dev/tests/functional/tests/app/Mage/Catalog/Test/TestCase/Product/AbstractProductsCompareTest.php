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

namespace Mage\Catalog\Test\TestCase\Product;

use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\TestCase\Injectable;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Customer\Test\Fixture\Customer;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Customer\Test\Page\CustomerAccountLogin;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Page\Product\CatalogProductCompare;
use Mage\Catalog\Test\Constraint\AssertProductCompareSuccessAddMessage;

/**
 * Abstract class for compare products class.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractProductsCompareTest extends Injectable
{
    /**
     * Array products.
     *
     * @var array
     */
    protected $products;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product compare page.
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Catalog product page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer login page.
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Fixture customer.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Assert Product Compare success add message.
     *
     * @var AssertProductCompareSuccessAddMessage
     */
    protected $assertProductCompareSuccessAddMessage;

    /**
     * Prepare data.
     *
     * @param FixtureFactory $fixtureFactory
     * @param Customer $customer
     * @param Browser $browser
     * @param AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
     * @return void
     */
    public function __prepare(
        FixtureFactory $fixtureFactory,
        Customer $customer,
        Browser $browser,
        AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
    ) {
        $this->fixtureFactory = $fixtureFactory;
        $this->browser = $browser;
        $this->assertProductCompareSuccessAddMessage = $assertProductCompareSuccessAddMessage;
        $customer->persist();
        $this->customer = $customer;
    }

    /**
     * Injection data.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
    }

    /**
     * Login customer.
     *
     * @return void
     */
    protected function loginCustomer()
    {
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $this->customer]
        )->run();
    }

    /**
     * Create products.
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        $products = $this->objectManager->create(
            'Mage\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $products]
        )->run();

        return $products['products'];
    }

    /**
     * Add product to compare list.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function addProduct(InjectableFixture $product)
    {
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->clickAddToCompare();
    }
}
