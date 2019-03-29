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
