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

namespace Mage\Review\Test\Constraint;

use Magento\Mtf\Constraint\AbstractAssertForm;
use Mage\Review\Test\Fixture\Review;
use Mage\Review\Test\Page\Adminhtml\CatalogProductReviewEdit;
use Mage\Review\Test\Page\Adminhtml\CatalogProductReview;

/**
 * Assert that review data equals passed from fixture on edit page.
 */
class AssertProductReviewForm extends AbstractAssertForm
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = [
        'entity_id'
    ];

    /**
     * Assert that review data equals passed from fixture on edit page.
     *
     * @param CatalogProductReview $reviewIndex
     * @param Review $review
     * @param CatalogProductReviewEdit $reviewEdit
     * @param string $status [optional]
     * @return void
     */
    public function processAssert(
        CatalogProductReview $reviewIndex,
        Review $review,
        CatalogProductReviewEdit $reviewEdit,
        $status = ''
    ) {
        $reviewIndex->open();
        $reviewIndex->getReviewGrid()->searchAndOpen(['title' => $review->getTitle()]);
        $fixtureData = $this->prepareFixtureData($review, $status);
        $formData = $reviewEdit->getReviewForm()->getData();
        $error = $this->verifyData($fixtureData, $formData);

        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Prepare fixture data.
     *
     * @param Review $review
     * @param string $status
     * @return array
     */
    protected function prepareFixtureData(Review $review, $status)
    {
        $reviewData = $review->getData();
        if ($status !== '') {
            $reviewData['status_id'] = $status;
        }

        return $reviewData;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Review data equals passed from fixture on edit page.';
    }
}
