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

namespace Mage\SalesRule\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Checkout\Test\Page\CheckoutCart;
use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Customer\Test\Page\CustomerAccountLogin;
use Mage\Customer\Test\Page\CustomerAccountLogout;
use Mage\SalesRule\Test\Fixture\SalesRule;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Abstract class for implementing assert applying.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCartPriceRuleApplying extends AbstractConstraint
{
    /**
     * Page CheckoutCart.
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Page CmsIndex.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Page CustomerAccountLogin.
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Page CustomerAccountLogout.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Page CatalogCategoryView.
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Page CatalogProductView.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer from precondition.
     *
     * @var Customer
     */
    protected $customer;

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * First product from precondition.
     *
     * @var CatalogProductSimple
     */
    protected $productForSalesRule1;

    /**
     * Second product from precondition.
     *
     * @var CatalogProductSimple
     */
    protected $productForSalesRule2;

    /**
     * Implementation assert.
     *
     * @return void
     */
    abstract protected function assert();

    /**
     * 1. Navigate to frontend
     * 2. If "Log Out" link is visible and "isLoggedIn" empty
     *    - makes logout
     * 3. If "isLoggedIn" not empty
     *    - login as customer
     * 4. Clear shopping cart
     * 5. Add test product(s) to shopping cart with specify quantity
     * 6. If "salesRule/data/coupon_code" not empty:
     *    - fill "Enter your code" input in Dіscount Codes
     *    - click "Apply Coupon" button
     * 7. If "address/data/country_id" not empty:
     *    On Estimate Shipping and Tax:
     *    - fill Country, State/Province, Zip/Postal Code
     *    - click 'Get a Quote' button
     *    - select 'Flat Rate' shipping
     *    - click 'Update Total' button
     * 8. Implementation assert
     *
     * @param CheckoutCart $checkoutCart
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param Customer $customer
     * @param SalesRule $salesRule
     * @param Address $address
     * @param Browser $browser
     * @param array $productQuantity
     * @param CatalogProductSimple $productForSalesRule1
     * @param CatalogProductSimple $productForSalesRule2
     * @param array $shipping [optional]
     * @param int|null $isLoggedIn
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        Customer $customer,
        SalesRule $salesRule,
        Address $address,
        Browser $browser,
        array $productQuantity,
        CatalogProductSimple $productForSalesRule1,
        CatalogProductSimple $productForSalesRule2,
        array $shipping = [],
        $isLoggedIn = null
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->customer = $customer;
        $this->browser = $browser;
        $this->productForSalesRule1 = $productForSalesRule1;
        $this->productForSalesRule2 = $productForSalesRule2;
        $isLoggedIn ? $this->login() : $this->customerAccountLogout->open();
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->addProductsToCart($productQuantity);
        if ($address->hasData('country_id')) {
            $this->checkoutCart->getShippingBlock()->fillEstimateShippingAndTax($address);
            if (!empty($shipping)) {
                $this->checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
            }
        }
        if ($salesRule->getCouponCode()) {
            $this->checkoutCart->getDiscountCodesBlock()->applyCouponCode($salesRule->getCouponCode());
        }
        $this->assert();
    }

    /**
     * Log in customer.
     *
     * @return void
     */
    protected function login()
    {
        $this->objectManager->create(
            'Mage\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $this->customer]
        )->run();
    }

    /**
     * Add products to cart.
     *
     * @param array $productQuantity
     * @return void
     */
    protected function addProductsToCart(array $productQuantity)
    {
        foreach ($productQuantity as $product => $quantity) {
            if ($quantity > 0) {
                $this->browser->open($_ENV['app_frontend_url'] . $this->$product->getUrlKey() . '.html');
                $this->catalogProductView->getViewBlock()->setQty($quantity);
                $this->catalogProductView->getViewBlock()->clickAddToCart();
            }
        }
    }

    /**
     * Get totals from total block in shopping cart.
     *
     * @return array
     */
    protected function getTotals()
    {
        $totals = [];
        $totals['subtotal'] = $this->checkoutCart->getTotalsBlock()->getData('subtotal');
        $totals['grandTotal'] = $this->checkoutCart->getTotalsBlock()->getData('grand_total');
        if ($this->checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()) {
            $shippingPrice = $this->checkoutCart->getTotalsBlock()->getData('shipping_price');
            $totals['grandTotal'] = number_format(($totals['grandTotal'] - $shippingPrice), 2);
        }

        return $totals;
    }
}
