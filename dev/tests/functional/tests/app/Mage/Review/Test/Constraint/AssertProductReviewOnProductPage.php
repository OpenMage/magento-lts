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

namespace Mage\Review\Test\Constraint;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Review\Test\Fixture\Review;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Constraint\AbstractAssertForm;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Assert that product review available on product page.
 */
class AssertProductReviewOnProductPage extends AbstractAssertForm
{
    /**
     * Assert that product review available on product page.
     *
     * @param CatalogProductView $catalogProductView
     * @param Review $review
     * @param FixtureInterface $product
     * @param BrowserInterface $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        Review $review,
        FixtureInterface $product,
        BrowserInterface $browser
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $reviewBlock = $catalogProductView->getReviewsBlock();
        $catalogProductView->getViewBlock()->openCustomInformationTab('Reviews');
        $formReview = $reviewBlock->getItems()[0];
        $fixtureReview = $this->prepareReview($review);
        $errors = $this->verifyData($fixtureReview, $formReview);

        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Prepare fixture review data.
     *
     * @param Review $review
     * @return array
     */
    protected function prepareReview(Review $review)
    {
        return [
            'title' => $review->getTitle(),
            'detail' => $review->getDetail(),
            'nickname' => $review->getNickname(),
            'ratings' => $review->getRatings()
        ];
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product review is displayed correct.';
    }
}
