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

namespace Mage\Downloadable\Test\Block\Catalog\Product\View;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Downloadable links blocks on frontend.
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Links extends Block
{
    /**
     * Selector title for for links.
     *
     * @var string
     */
    protected $title = '//div[contains(@id,"product-options-wrapper")]//label[@class="required"]';

    /**
     * Selector for link by label.
     *
     * @var string
     */
    protected $linkByLabel = './/input[@type="checkbox" and (./../span/label[contains(text(),"%s")])]';

    /**
     * Choice link selector.
     *
     * @var string
     */
    protected $choiceLink = './/*[contains(@id,"downloadable-links-list")]//li';

    /**
     * Checkbox selector item links.
     *
     * @var string
     */
    protected $separatelyForChoice = 'input[type="checkbox"]';

    /**
     * Checkbox selector item links.
     *
     * @var string
     */
    protected $linkForChoice = 'span>label:first-child';

    /**
     * Checkbox selector item links.
     *
     * @var string
     */
    protected $sampleLinkForChoice = 'li span>a';

    /**
     * Checkbox selector item links.
     *
     * @var string
     */
    protected $priceForChoice = '.price-notice';

    /**
     * Checkbox selector item links.
     *
     * @var string
     */
    protected $priceAdjustmentsForChoice = '.price-adjustments .price';

    /**
     * Get title for links block.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_rootElement->find($this->title, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Fill links on product view page.
     *
     * @param array $data
     * @return void
     */
    public function fill(array $data)
    {
        foreach ($data as $linkData) {
            $selector = sprintf($this->linkByLabel, $linkData['label']);
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH, 'checkbox')->setValue($linkData['value']);
        }
    }

    /**
     * Return links data on product page view.
     *
     * @return array
     */
    public function getLinks()
    {
        $linksData = [];

        $choiceLinks = $this->_rootElement->getElements($this->choiceLink, Locator::SELECTOR_XPATH);
        foreach ($choiceLinks as $choiceLink) {
            $link = $choiceLink->find($this->linkForChoice);
            $sample = $choiceLink->find($this->sampleLinkForChoice);
            $price = $choiceLink->find($this->priceForChoice);
            $priceAdjustments = $choiceLink->find($this->priceAdjustmentsForChoice);

            $linkData = [
                'links_purchased_separately' => $choiceLink->find($this->separatelyForChoice)->isVisible()
                    ? 'Yes'
                    : 'No',
                'title' => $link->isVisible() ? $link->getText() : null,
                'sample' => $sample->isVisible() ? $sample->getText() : null,
                'price' => $price->isVisible() ? $this->escapePrice($price->getText()) : null,
                'price_adjustments' => $priceAdjustments->isVisible()
                    ? $this->escapePrice($priceAdjustments->getText())
                    : null,
            ];

            $linksData[] = array_filter($linkData);
        }

        return $linksData;
    }

    /**
     * Escape currency for price.
     *
     * @param string $price
     * @return string
     */
    protected function escapePrice($price)
    {
        return preg_replace('/[^0-9\.,]/', '', $price);
    }
}
