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

namespace Mage\Catalog\Test\Block\Product;

use Mage\Catalog\Test\Block\Msrp\Popup;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * This class is used to access the price related information from the storefront.
 */
class Price extends Block
{
    /**
     * Mapping for different type of price.
     *
     * @var array
     */
    protected $mapTypePrices = [
        'price' => [
            'selector' => '.regular-price .price'
        ],
        'old_price' => [
            'selector' => '.old-price .price'
        ],
        'special_price' => [
            'selector' => '.special-price .price'
        ],
        'price_from' => [
            'selector' => 'p.price-from .price',
        ],
        'price_to' => [
            'selector' => 'p.price-to .price',
        ],
        'minimal_price' => [
            'selector' => 'p.minimal-price .price',
        ],
        'final_price' => [
            'selector' => '.price',
        ],
        'category_price_excl_tax' => [
            'selector' => '.price-excluding-tax span.price'
        ],
        'category_price_incl_tax' => [
            'selector' => '.price-including-tax span.price'
        ],
        'product_view_price_incl_tax' => [
            'selector' => '.price-including-tax span.price'
        ],
        'product_view_price_excl_tax' => [
            'selector' => '.price-excluding-tax span.price'
        ]
    ];

    /**
     * 'Add to Cart' button.
     *
     * @var string
     */
    protected $addToCart = '.action.tocart';

    /**
     * 'Click for price' link selector.
     *
     * @var string
     */
    protected $clickForPrice = '.map-link';

    /**
     * MAP popup selector.
     *
     * @var string
     */
    protected $map = './ancestor::body//*[@id="map-popup"]';

    /**
     * This method returns the price represented by the block.
     *
     * @param string $currency
     * @return string
     */
    public function getPrice($currency = '$')
    {
        return $this->getTypePrice('price', $currency);
    }

    /**
     * This method returns the regular price represented by the block.
     *
     * @return string
     */
    public function getRegularPrice()
    {
        // either return the old price (implies special price display or a regular price
        $priceElement = $this->getTypePriceElement('old_price');
        if (!$priceElement->isVisible()) {
            $priceElement = $this->getTypePriceElement('price')->isVisible()
                ? $this->getTypePriceElement('price')
                : $this->getTypePriceElement('minimal_price');
        }
        // return the actual value of the price
        $price = preg_replace('#[^\d\.\s]+#umis', '', $priceElement->getText());
        return number_format(trim($price), 2);
    }

    /**
     * This method returns the special price represented by the block.
     *
     * @param string $currency
     * @return string
     */
    public function getSpecialPrice($currency = '$')
    {
        return $this->getTypePrice('special_price', $currency);
    }

    /**
     * Get final price.
     *
     * @param string $currency
     * @return string
     */
    public function getFinalPrice($currency = '$')
    {
        return $this->getTypePrice('final_price', $currency);
    }

    /**
     * This method returns if the regular price is visible.
     *
     * @return bool
     */
    public function isRegularPriceVisible()
    {
        return $this->getTypePriceElement('price')->isVisible();
    }

    /**
     * Get specify type price.
     *
     * @param string $type
     * @param string $currency [optional]
     * @return string|null
     */
    protected function getTypePrice($type, $currency = '$')
    {
        $typePriceElement = $this->getTypePriceElement($type);
        return $typePriceElement->isVisible() ? $this->escape($typePriceElement->getText(), $currency) : null;
    }

    /**
     * Get specify type price element.
     *
     * @param string $type
     * @return Element
     */
    public function getTypePriceElement($type)
    {
        $mapTypePrice = $this->mapTypePrices[$type];
        return $this->_rootElement->find(
            $mapTypePrice['selector'],
            isset($mapTypePrice['strategy']) ? $mapTypePrice['strategy'] : Locator::SELECTOR_CSS
        );
    }

    /**
     * Escape currency and separator for price.
     *
     * @param string $price
     * @param string $currency
     * @return string
     */
    protected function escape($price, $currency = '$')
    {
        return str_replace([',', $currency], '', $price);
    }

    /**
     * Click on 'Click for price' link.
     *
     * @return void
     */
    public function clickForPrice()
    {
        $this->_rootElement->find($this->clickForPrice)->click();
    }

    /**
     * Get MAP popup block.
     *
     * @return Popup
     */
    public function getMapBlock()
    {
        return $this->blockFactory->create(
            'Mage\Catalog\Test\Block\Msrp\Popup',
            [
                'element' => $this->_rootElement->find($this->map, Locator::SELECTOR_XPATH)
            ]
        );
    }

    /**
     * Get Result price.
     *
     * @return string
     */
    public function getResultPrice()
    {
        return ($this->getTypePriceElement('price')->isVisible())
            ? $this->getTypePrice('price')
            : $this->getTypePrice('special_price');
    }
}
