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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Block\Product\Compare;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Client\Locator;

/**
 * List Compare products on compare page.
 */
class ListCompare extends Block
{
    /**
     * Selector by product info.
     *
     * @var string
     */
    protected $productInfo = '//tr[contains(@class, "product-shop-row")][1]/td[%d]';

    /**
     * Selector by product block.
     *
     * @var string
     */
    protected $productBlock = '//tr[contains(., "%s")]/td[%d]';

    /**
     * Selector by name product.
     *
     * @var string
     */
    protected $nameSelector = './/*[contains(@class, "product-name")]/a';

    /**
     * Selector for search product via name.
     *
     * @var string
     */
    protected $productName = '[normalize-space(text()) = "%s"]';

    /**
     * Selector by price product.
     *
     * @var string
     */
    protected $priceSelector = './/div[contains(@class,"price-box")]';

    /**
     * Selector by special price from.
     *
     * @var string
     */
    protected $priceFrom = './/div[contains(@class,"price-box")]/p[contains(@class,"price-from")]';

    /**
     * Selector by special price from.
     *
     * @var string
     */
    protected $priceFromString = './/div[contains(@class,"price-box")]//*[contains(., "From")]';

    /**
     * Selector by special price from.
     *
     * @var string
     */
    protected $priceStartingAtString = './/div[contains(@class,"price-box")]//*[contains(., "Starting at")]';

    /**
     * Selector by special price to.
     *
     * @var string
     */
    protected $priceTo = './/div[contains(@class,"price-box")]/p[contains(@class,"price-to")]';

    /**
     * Global attribute selector.
     *
     * @var string
     */
    protected $attribute = '//th/span[text()="%s"]';

    /**
     * Get product info.
     *
     * @param int $index
     * @param string $attributeKey
     * @param string $currency
     * @return string
     */
    public function getProductInfo($index, $attributeKey, $currency = ' $')
    {
        $infoBlock = $this->getCompareProductInfo($index);
        if ($attributeKey == 'price') {
            return $this->getPrice($infoBlock, $currency);
        } else {
            return strtolower($infoBlock->find($this->nameSelector, Locator::SELECTOR_XPATH)->getText());
        }
    }

    /**
     * Get product price.
     *
     * @param SimpleElement $infoBlock
     * @param string $currency
     * @return string|array
     */
    protected function getPrice(SimpleElement $infoBlock, $currency)
    {
        if ($infoBlock->find($this->priceFrom, Locator::SELECTOR_XPATH)->isVisible()) {
            return [
                'price_from' => $this->getPriceFromPage($infoBlock, $currency, $this->priceFrom),
                'price_to' => $this->getPriceFromPage($infoBlock, $currency, $this->priceTo)
            ];
        } elseif ($infoBlock->find($this->priceFromString, Locator::SELECTOR_XPATH)->isVisible()) {
            return ['price_from' => $this->getPriceFromPage($infoBlock, $currency, $this->priceSelector)];
        } elseif ($infoBlock->find($this->priceStartingAtString, Locator::SELECTOR_XPATH)->isVisible()) {
            return ['price_starting' => $this->getPriceFromPage($infoBlock, $currency, $this->priceSelector)];
        } else {
            return $this->getPriceFromPage($infoBlock, $currency, $this->priceSelector);
        }
    }

    /**
     * Get price from page.
     *
     * @param SimpleElement $infoBlock
     * @param string $currency
     * @param string $selector
     * @return string
     */
    protected function getPriceFromPage(SimpleElement $infoBlock, $currency, $selector)
    {
        return $this->preparePrice(
            $infoBlock->find($selector, Locator::SELECTOR_XPATH)->getText(),
            $currency
        );
    }

    /**
     * Prepare price.
     *
     * @param string $price
     * @param string $currency
     * @return string
     * @throws \Exception
     */
    protected function preparePrice($price, $currency)
    {
        preg_match('/.(\d+.*)/', $price, $prices);
        if (!empty($prices[1])) {
            return trim($prices[1], $currency);
        } else {
            throw new \Exception("Price is absent on price block! \n" . $price);
        }
    }

    /**
     * Get product meta data (sku, description and short description).
     *
     * @param int $index
     * @param string $attributeKey
     * @return string
     */
    public function getProductMetaData($index, $attributeKey)
    {
        return strtolower(
            $this->_rootElement->find(
                sprintf($this->productBlock, $attributeKey, $index),
                Locator::SELECTOR_XPATH
            )->getText()
        );
    }

    /**
     * Get item compare product info.
     *
     * @param int $index
     * @return SimpleElement
     */
    protected function getCompareProductInfo($index)
    {
        return $this->_rootElement->find(sprintf($this->productInfo, $index), Locator::SELECTOR_XPATH);
    }

    /**
     * Check is visible attribute.
     *
     * @param string $attributeName
     * @return bool
     */
    public function isAttributeVisible($attributeName)
    {
        $selector = sprintf($this->attribute, $attributeName);
        return $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
