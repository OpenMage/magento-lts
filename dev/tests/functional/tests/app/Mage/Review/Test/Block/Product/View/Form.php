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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Review\Test\Block\Product\View;

use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Rating\Test\Fixture\Rating;
use Mage\Review\Test\Fixture\Review as ReviewFixture;

/**
 * Product review form on the review product page.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Selector for submit button.
     *
     * @var string
     */
    protected $submit = 'button[type="submit"]';

    /**
     * Item rating selector.
     *
     * @var string
     */
    protected $rating = '//*[@id="product-review-table"]/tbody/tr[th[text()="%s"]]';

    /**
     * Selector for label of rating vote.
     *
     * @var string
     */
    protected $ratingVoteLabel = '#%s_%d';

    /**
     * Click on submit button.
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->submit)->click();
    }

    /**
     * Fill the root form.
     *
     * @param FixtureInterface $review
     * @param SimpleElement|null $element
     * @return $this
     */
    public function fill(FixtureInterface $review, SimpleElement $element = null)
    {
        if ($review->hasData('ratings')) {
            $this->fillRatings($review);
        }
        return parent::fill($review, $element);
    }

    /**
     * Fill ratings on the review form.
     *
     * @param ReviewFixture $review
     * @return void
     */
    protected function fillRatings(ReviewFixture $review)
    {
        $ratingsData = $review->getRatings();
        $ratingsFixture = $review->getDataFieldConfig('ratings')['source']->getRatings();
        foreach ($ratingsData as $key => $value) {
            $this->setRating($ratingsFixture[$key], $value['rating']);
        }
    }

    /**
     * Set rating vote by rating code.
     *
     * @param Rating $rating
     * @param int $ratingVote
     * @return void
     */
    protected function setRating(Rating $rating, $ratingVote)
    {
        $ratingValueSelector = sprintf($this->ratingVoteLabel, $rating->getRatingCode(), $ratingVote);
        $this->getRating($rating)->find($ratingValueSelector, Locator::SELECTOR_CSS, 'checkbox')->setValue('Yes');
    }

    /**
     * Check rating element is visible.
     *
     * @param Rating $rating
     * @return bool
     */
    public function isVisibleRating(Rating $rating)
    {
        return $this->getRating($rating)->isVisible();
    }

    /**
     * Get single product rating.
     *
     * @param Rating $rating
     * @return SimpleElement
     */
    protected function getRating(Rating $rating)
    {
        return $this->_rootElement->find(sprintf($this->rating, $rating->getRatingCode()), Locator::SELECTOR_XPATH);
    }
}
