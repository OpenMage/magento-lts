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

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Catalog\Test\Block\Product\View;

/**
 * Assert that displayed special price on product page equals passed from fixture.
 */
class AssertProductSpecialPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
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
    protected $errorMessage = 'Assert that displayed special price on product page NOT equals to passed from fixture.';

    /**
     * Assert that displayed special price on product page equals passed from fixture.
     *
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, Browser $browser, InjectableFixture $product)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        //Process assertions
        $this->assertPrice($product, $catalogProductView->getViewBlock());
    }

    /**
     * Set $errorMessage for special price assert.
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
     * @return void
     */
    public function assertPrice(InjectableFixture $product, View $productViewBlock)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($product->getSpecialPrice(), 2),
            $productViewBlock->getPriceBlock()->getSpecialPrice(),
            $this->errorMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Assert that displayed special price on product page equals passed from fixture.";
    }
}
