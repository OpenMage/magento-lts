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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Checkout\Test\Block;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\ElementInterface;

/**
 * Base product item form on checkout page.
 */
class AbstractItem extends Form
{
    /**
     * Mapping for prices.
     *
     * @var array
     */
    protected $pricesType = [
        'price' => ['selector' => '.product-cart-price .cart-price .price'],
        'subtotal' => ['selector' => '.product-cart-total .cart-price .price'],
        'cart_item_price' => ['selector' => '.product-cart-price .cart-price .price'],
        'cart_item_subtotal' => ['selector' => '.product-cart-total .cart-price .price'],
        'cart_item_price_excl_tax' => ['selector' => '.product-cart-price[data-rwd-tax-label="Excl. Tax"]'],
        'cart_item_price_incl_tax' => ['selector' => '.product-cart-price[data-rwd-tax-label="Incl. Tax"]'],
        'cart_item_subtotal_excl_tax' => [
            'selector' => '//td[@class="product-cart-total"][1]//*[@class="cart-price"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'cart_item_subtotal_incl_tax' => [
            'selector' => '//td[@class="product-cart-total"][2]//*[@class="cart-price"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ]
    ];

    /**
     * Selector for product name.
     *
     * @var string
     */
    protected $productName = '.product-name > a';

    /**
     * Quantity input selector.
     *
     * @var string
     */
    protected $qty = '.input-text.qty';

    /**
     * Selector for options block.
     *
     * @var string
     */
    protected $optionsBlock = 'dl.item-options';

    /**
     * Get product name.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->_rootElement->find($this->productName)->getText();
    }

    /**
     * Get product quantity.
     *
     * @return string
     */
    public function getQty()
    {
        return $this->_rootElement->find($this->qty)->getValue();
    }

    /**
     * Set product quantity.
     *
     * @param int $qty
     * @return void
     */
    public function setQty($qty)
    {
        $this->_rootElement->find($this->qty)->setValue($qty);
    }

    /**
     * Get price type.
     *
     * @param string $priceType
     * @return string
     */
    public function getCartItemTypePrice($priceType)
    {
        $strategy = isset($this->pricesType[$priceType]['strategy'])
            ? $this->pricesType[$priceType]['strategy']
            : Locator::SELECTOR_CSS;
        $selector = $this->prepareSelector($priceType);
        $price = $this->_rootElement->find($selector, $strategy)->getText();

        return $this->escapeCurrency($price);
    }

    /**
     * Get product options from cart.
     *
     * @param ElementInterface $element
     * @return array
     */
    public function getOptions(ElementInterface $element = null)
    {
        $element = ($element == null) ? $this->_rootElement : $element;
        $optionsBlock = $element->find($this->optionsBlock);
        $options = [];

        if ($optionsBlock->isVisible()) {
            $titles = $optionsBlock->getElements('./dt', Locator::SELECTOR_XPATH);
            $values = $optionsBlock->getElements('./dd', Locator::SELECTOR_XPATH);

            foreach ($titles as $key => $title) {
                $value = $values[$key]->getText();
                $options[] = [
                    'title' => str_replace(':', '', $title->getText()),
                    'value' => $this->escapeCurrencyForOption($value),
                ];
            }
        }

        return $options;
    }

    /**
     * Escape currency in option label.
     *
     * @param string $label
     * @return string
     */
    protected function escapeCurrencyForOption($label)
    {
        return preg_replace('/^(\d+) x (\w+) \W([\d\.,]+)$/', '$1 x $2 $3', $label);
    }

    /**
     * Prepare selector for field.
     *
     * @param string $field
     * @return string
     */
    protected function prepareSelector($field)
    {
        return $this->pricesType[$field]['selector'];
    }

    /**
     * Escape currency in price.
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }
}
