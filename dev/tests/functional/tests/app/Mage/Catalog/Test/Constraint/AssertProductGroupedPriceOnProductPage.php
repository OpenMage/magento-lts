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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Block\Product\View;
use Mage\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Assert that displayed grouped price on product page equals passed from fixture.
 */
class AssertProductGroupedPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = 'That displayed grouped price on product page is NOT equal to one, passed from fixture.';

    /**
     * Customer group
     *
     * @var string
     */
    protected $customerGroup;

    /**
     * Assert that displayed grouped price on product page equals passed from fixture.
     *
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, InjectableFixture $product, Browser $browser)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView->getViewBlock());
    }

    /**
     * Set $errorMessage for grouped price assert.
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Verify product special price on product view page.
     *
     * @param InjectableFixture $product
     * @param View $productViewBlock
     * @param string $customerGroup [optional]
     * @return void
     */
    public function assertPrice(InjectableFixture $product, View $productViewBlock, $customerGroup = 'NOT LOGGED IN')
    {
        $this->customerGroup = $customerGroup;
        $groupPrice = $this->getGroupedPrice($productViewBlock, $product);
        \PHPUnit_Framework_Assert::assertEquals($groupPrice['fixture'], $groupPrice['onPage'], $this->errorMessage);
    }

    /**
     * Get grouped price with fixture product and product page.
     *
     * @param View $view
     * @param InjectableFixture $product
     * @return array
     */
    protected function getGroupedPrice(View $view, InjectableFixture $product)
    {
        $fields = $product->getData();
        $groupPrice['onPage'] = $view->getPriceBlock()->getSpecialPrice();
        $groupPrice['fixture'] = number_format(
            $fields['group_price'][array_search($this->customerGroup, $fields['group_price'])]['price'],
            2
        );

        return $groupPrice;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that displayed grouped price on product page equals passed from fixture.';
    }
}
