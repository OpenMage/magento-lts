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

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that product duplicate is not displayed on front-end.
 */
class AssertProductDuplicateIsNotDisplayingOnFrontend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that product duplicate is not displayed on front-end.
     *
     * @param InjectableFixture $product
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    public function processAssert(InjectableFixture $product, Browser $browser, CatalogProductView $catalogProductView)
    {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '-1' . '.html');
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogProductView->getViewBlock()->isVisible(),
            'Duplicate Product is displayed on frontend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'The product does not appear on the frontend.';
    }
}
