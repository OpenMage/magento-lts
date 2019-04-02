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

namespace Mage\Review\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Review\Test\Fixture\Review;
use Mage\Adminhtml\Test\Page\Adminhtml\Cache;
use Mage\Review\Test\Page\Adminhtml\CatalogProductReviewEdit;
use Mage\Review\Test\Page\Adminhtml\CatalogProductReview;

/**
 * Assert that product review can do approved.
 */
class AssertSetApprovedProductReview extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product review can do approved.
     *
     * @param CatalogProductReview $reviewIndex
     * @param Review $review
     * @param CatalogProductReviewEdit $reviewEdit
     * @param AssertReviewSuccessSaveMessage $assertReviewSuccessSaveMessage
     * @param Cache $cachePage
     * @return void
     */
    public function processAssert(
        CatalogProductReview $reviewIndex,
        Review $review,
        CatalogProductReviewEdit $reviewEdit,
        AssertReviewSuccessSaveMessage $assertReviewSuccessSaveMessage,
        Cache $cachePage
    ) {
        $reviewIndex->open()->getReviewGrid()->searchAndOpen(['title' => $review->getTitle()]);
        $reviewEdit->getReviewForm()->setApproveReview();
        $reviewEdit->getFormPageActions()->save();
        $assertReviewSuccessSaveMessage->processAssert($reviewIndex);
        $cachePage->open()->getPageActions()->flushCacheStorage();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Review status is change to approve.';
    }
}
