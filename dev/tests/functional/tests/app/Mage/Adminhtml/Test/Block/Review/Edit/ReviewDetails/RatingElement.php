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

namespace Mage\Adminhtml\Test\Block\Review\Edit\ReviewDetails;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;

/**
 * Rating typified element.
 */
class RatingElement extends SimpleElement
{
    /**
     * Selector for label of checked rating.
     *
     * @var string
     */
    protected $checkedRating = 'input:checked';

    /**
     * Selector for rating title.
     *
     * @var string
     */
    protected $ratingTitle = '//*[@id="product-review-table"]/tbody/tr/td[1]';

    /**
     * Get ratings list.
     *
     * @return array
     */
    public function getValue()
    {
        $result = [];
        $ratings = $this->getElements($this->checkedRating, Locator::SELECTOR_CSS, 'checkbox');
        $titles = $this->getElements($this->ratingTitle, Locator::SELECTOR_XPATH);
        foreach ($ratings as $key => $rating) {
            $ratingTitle = $titles[$key]->getText();
            $result[] = [
                'title' => $ratingTitle,
                'rating' => $this->prepareFormatValue($rating->getAttribute('id'), $ratingTitle)
            ];
        }

        return $result;
    }

    /**
     * Prepare format for rating value.
     *
     * @param string $value
     * @param string $replace
     * @return string
     */
    protected function prepareFormatValue($value, $replace)
    {
        return str_replace($replace . '_', '', $value);
    }
}
