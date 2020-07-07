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

namespace Mage\Review\Test\Constraint;

use Mage\Review\Test\Fixture\Review;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that add and view review links are present on product page.
 */
class AssertReviewLinksIsPresentOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that add view review links are present on product page.
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture $product
     * @param Review $review
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductView $catalogProductView,
        InjectableFixture $product,
        Review $review
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        // Verify add review link
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductView->getReviewViewBlock()->getAddReviewLink()->isVisible(),
            'Add review link is not visible on product page.'
        );

        // Verify view review link
        $viewReviewLink = $catalogProductView->getReviewViewBlock()->getViewReviewLink($review);
        \PHPUnit_Framework_Assert::assertTrue(
            $viewReviewLink->isVisible(),
            'View review link is not visible on product page.'
        );
        \PHPUnit_Framework_Assert::assertContains(
            '1',
            $viewReviewLink->getText(),
            'There is more than 1 approved review.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Add and view review links are present on product page.';
    }
}
