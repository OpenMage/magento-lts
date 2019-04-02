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

namespace Mage\Review\Test\TestStep;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\Review\Test\Constraint\AssertProductReviewIsAbsentOnProductPage;
use Mage\Review\Test\Fixture\Review;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Review\Test\Page\Product\ReviewProductList;

/**
 * Add frontend review.
 */
class AddFrontendReviewStep implements TestStepInterface
{
    /**
     * Frontend product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Review product list page.
     *
     * @var ReviewProductList
     */
    protected $reviewProductList;

    /**
     * Fixture review.
     *
     * @var Review
     */
    protected $review;

    /**
     * Browser interface.
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * Check first review.
     *
     * @var bool
     */
    protected $isFirstReview;

    /**
     * Assert product review is absent on product page.
     *
     * @var AssertProductReviewIsAbsentOnProductPage
     */
    protected $assertProductReviewIsAbsentOnProductPage;

    /**
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param ReviewProductList $reviewProductList
     * @param Review $review
     * @param BrowserInterface $browser
     * @param AssertProductReviewIsAbsentOnProductPage $assertProductReviewIsAbsentOnProductPage
     * @param bool $isFirstReview
     */
    public function __construct(
        CatalogProductView $catalogProductView,
        ReviewProductList $reviewProductList,
        Review $review,
        BrowserInterface $browser,
        AssertProductReviewIsAbsentOnProductPage $assertProductReviewIsAbsentOnProductPage,
        $isFirstReview = false
    ) {
        $this->catalogProductView = $catalogProductView;
        $this->reviewProductList = $reviewProductList;
        $this->review = $review;
        $this->browser = $browser;
        $this->assertProductReviewIsAbsentOnProductPage = $assertProductReviewIsAbsentOnProductPage;
        $this->isFirstReview = $isFirstReview;
    }

    /**
     * Add review to product via frontend.
     *
     * @return array
     */
    public function run()
    {
        $product = $this->review->getDataFieldConfig('entity_id')['source']->getEntity();
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        if ($this->isFirstReview) {
            $this->assertProductReviewIsAbsentOnProductPage->processAssert();
        }
        $this->catalogProductView->getViewBlock()->openCustomInformationTab('Reviews');
        $this->catalogProductView->getReviewsBlock()->clickAddReviewLink();
        $reviewForm = $this->reviewProductList->getReviewFormBlock();
        $reviewForm->fill($this->review);
        $reviewForm->submit();

        return ['product' => $product, 'review' => $this->review];
    }
}
