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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Review\Test\Block\Product\View;

use Magento\Mtf\Client\ElementInterface;
use Magento\Mtf\Block\Block;
use Mage\Review\Test\Fixture\Review as ReviewFixture;
use Magento\Mtf\Client\Locator;

/**
 * Product review block on the product page.
 */
class Review extends Block
{
    /**
     * Selector for add review link.
     *
     * @var string
     */
    protected $addReviewLink = 'a[href$="#review-form"]';

    /**
     * Review items selector.
     *
     * @var string
     */
    protected $itemsSelector = 'dl dt';

    /**
     * Item row review selector.
     *
     * @var string
     */
    protected $itemRowSelector = './/dl/dt[a[contains(text(),"%s")]]/following-sibling::dd[1]';

    /**
     * Item review author.
     *
     * @var string
     */
    protected $itemAuthor = '/span';

    /**
     * Ratings selector.
     *
     * @var string
     */
    protected $itemRatings = '//*[@class="ratings-table"]//tr';

    /**
     * Selector for rating value.
     *
     * @var string
     */
    protected $ratingValue = '.rating';

    /**
     * Click add review link.
     *
     * @return void
     */
    public function clickAddReviewLink()
    {
        $this->getAddReviewLink()->click();
    }

    /**
     * Is visible review items.
     *
     * @return bool
     */
    public function isVisibleReviewItems()
    {
        return $this->_rootElement->find($this->itemsSelector)->isVisible();
    }

    /**
     * Get add review link.
     *
     * @return ElementInterface
     */
    public function getAddReviewLink()
    {
        return $this->_rootElement->find($this->addReviewLink);
    }

    /**
     * Get all reviews.
     *
     * @return array
     */
    public function getItems()
    {
        $items = [];
        if (!$this->_rootElement->find($this->itemsSelector)->isVisible()) {
            return [];
        }
        $reviewsTitles = $this->_rootElement->getElements($this->itemsSelector);
        foreach ($reviewsTitles as $title) {
            $reviewTitle = $this->getReviewTitle($title);;
            $items[] = [
                'title' => $reviewTitle,
                'detail' => $this->getReviewText($reviewTitle),
                'nickname' => $this->getReviewAuthor($reviewTitle),
                'ratings' => $this->getReviewRatings($reviewTitle)
            ];
        }

        return $items;
    }

    /**
     * Get review ratings.
     *
     * @param string $reviewTitle
     * @return array|null
     */
    protected function getReviewRatings($reviewTitle)
    {
        $ratings = [];
        $ratingsSelector = $this->getRatingsSelector($reviewTitle);
        if (!$this->_rootElement->find($ratingsSelector, Locator::SELECTOR_XPATH)->isVisible()) {
            return null;
        }
        $ratingsElements = $this->_rootElement->getElements($ratingsSelector, Locator::SELECTOR_XPATH);
        foreach ($ratingsElements as $itemRating) {
            $ratings[] = [
                'title' => strtolower($itemRating->getText()),
                'rating' => $this->getRatingValue($itemRating)
            ];
        }

        return $ratings;
    }

    /**
     * Get ratings selector.
     *
     * @param string $reviewTitle
     * @return string
     */
    protected function getRatingsSelector($reviewTitle)
    {
        return $this->getReviewSelector($reviewTitle, 'Ratings');
    }

    /**
     * Get rating value.
     *
     * @param ElementInterface $itemRating
     * @return string
     */
    protected function getRatingValue(ElementInterface $itemRating)
    {
        $ratingValue = $itemRating->find($this->ratingValue)->getAttribute('style');
        preg_match('`(\d+)%`', $ratingValue, $matches);
        return isset($matches[1]) ? $matches[1] / 20 : null;
    }

    /**
     * Get review's title.
     *
     * @param ElementInterface $titleElement
     * @return string
     */
    protected function getReviewTitle(ElementInterface $titleElement)
    {
        return strtolower($titleElement->getText());
    }

    /**
     * Get review's author.
     *
     * @param string $reviewTitle
     * @return string
     */
    protected function getReviewAuthor($reviewTitle)
    {
        $reviewAuthor = $this->_rootElement->find($this->getReviewAuthorSelector($reviewTitle), Locator::SELECTOR_XPATH)
            ->getText();

        return strtolower(trim(str_replace('REVIEW BY', '', explode('/', $reviewAuthor)[0])));
    }

    /**
     * Get review's text.
     *
     * @param string $reviewTitle
     * @return string
     */
    protected function getReviewText($reviewTitle)
    {
        $reviewText = $this->_rootElement->find($this->getReviewTextSelector($reviewTitle), Locator::SELECTOR_XPATH)
            ->getText();
        return explode("\n", $reviewText)[0];
    }

    /**
     * Get review text selector.
     *
     * @param string $reviewTitle
     * @return string
     */
    protected function getReviewTextSelector($reviewTitle)
    {
        return $this->getReviewSelector($reviewTitle, 'Text');
    }

    /**
     * Get review author selector.
     *
     * @param string $reviewTitle
     * @return string
     */
    protected function getReviewAuthorSelector($reviewTitle)
    {
        return $this->getReviewSelector($reviewTitle, 'Author');
    }

    /**
     * Get review entity selector.
     *
     * @param string $reviewTitle
     * @param string $type
     * @return string
     */
    protected function getReviewSelector($reviewTitle, $type)
    {
        $property = 'item' . $type;
        $specifySelector = property_exists($this, $property) ? $this->$property : '';
        return sprintf($this->itemRowSelector . $specifySelector, $reviewTitle);
    }
}
