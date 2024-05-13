<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Rating\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Review\Test\Page\Product\ReviewProductList;
use Mage\Review\Test\Fixture\Review;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that product rating is displayed on product review page(frontend).
 */
class AssertProductRatingInProductPage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that product rating is displayed on product review page(frontend).
     *
     * @param CatalogProductView $catalogProductView
     * @param ReviewProductList $reviewProductList
     * @param BrowserInterface $browser
     * @param CatalogProductSimple $product
     * @param Review $review
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        ReviewProductList $reviewProductList,
        BrowserInterface $browser,
        CatalogProductSimple $product,
        Review $review
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->openCustomInformationTab('Reviews');
        $catalogProductView->getReviewsBlock()->clickAddReviewLink();
        $reviewForm = $reviewProductList->getReviewFormBlock();
        $ratings = $review->getDataFieldConfig('ratings')['source']->getRatings();
        foreach($ratings as $rating){
            \PHPUnit_Framework_Assert::assertTrue(
                $reviewForm->isVisibleRating($rating),
                'Product rating "' . $rating->getRatingCode() . '" is not displayed on review product page.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product rating is displayed on review product page.';
    }
}
