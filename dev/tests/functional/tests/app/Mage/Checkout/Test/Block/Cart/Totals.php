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

namespace Mage\Checkout\Test\Block\Cart;

use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Cart totals block.
 */
class Totals extends Block
{
    /**
     * Verifiable fields.
     *
     * @var array
     */
    protected $fieldType = [
        'grand_total' => [
            'selector' => '//tr[normalize-space(td)="Grand Total"]//span',
            'is_required'
        ],
        'subtotal' => [
            'selector' => '//tr[normalize-space(td)="Subtotal"]//span',
            'is_required'
        ],
        'subtotal_excl_tax' => [
            'selector' => '//tr[normalize-space(td)="Subtotal (Excl. Tax)"]//span',
            'is_required'
        ],
        'subtotal_incl_tax' => [
            'selector' => '//tr[normalize-space(td)="Subtotal (Incl. Tax)"]//span',
            'is_required'
        ],
        'discount' => [
            'selector' => '//tr[td[contains(text(),"Discount")]]//span'
        ],
        'shipping_excl_tax' => [
            'selector' => '//tr[.//*[contains(.,"Shipping") and contains(.,"Excl. Tax")]]//span'
        ],
        'shipping_incl_tax' => [
            'selector' => '//tr[.//*[contains(.,"Shipping") and contains(.,"Incl. Tax")]]//span'
        ],
        'tax' => [
            'selector' => '//tr[normalize-space(td)="Tax"]//span',
            'is_required'
        ],
        'grand_total_excl_tax' => [
            'selector' => '//tr[.//*[contains(.,"Grand Total") and contains(.,"Excl. Tax")]]//span',
            'is_required'
        ],
        'grand_total_incl_tax' => [
            'selector' => '//tr[.//*[contains(.,"Grand Total") and contains(.,"Incl. Tax")]]//span',
            'is_required'
        ],
        'shipping_price' => [
            'selector' => '//td[contains(.,"Shipping &")]/following-sibling::td/span[contains(@class,"price")]',
            'is_required'
        ]
    ];

    /**
     * Shipping price block selector.
     *
     * @var string
     */
    protected $shippingPriceBlockSelector = '//td[contains(.,"Shipping & Handling")]';

    /**
     * Get data from block.
     *
     * @param string $type
     * @return null|string
     */
    public function getData($type)
    {
        $selector = $this->prepareSelector($type);
        $element = $this->_rootElement->find($selector, Locator::SELECTOR_XPATH);
        return isset($this->fieldType[$type]['is_required'])
            ? $this->escapeCurrency($element->getText())
            : ($element->isVisible() ? $this->escapeCurrency($element->getText()) : null);
    }

    /**
     * Prepare selector.
     *
     * @param string $type
     * @return string
     */
    protected function prepareSelector($type)
    {
        return $this->fieldType[$type]['selector'];
    }

    /**
     * Method that escapes currency symbols.
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }

    /**
     * Check that shipping price block is visible.
     *
     * @return bool
     */
    public function isVisibleShippingPriceBlock()
    {
        return  $this->_rootElement->find($this->shippingPriceBlockSelector, Locator::SELECTOR_XPATH)->isVisible();
    }
}
