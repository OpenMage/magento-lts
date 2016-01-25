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

namespace Mage\Adminhtml\Test\Block\Sales\Order\AbstractForm;

use Mage\Checkout\Test\Block\AbstractItem;
use Magento\Mtf\Client\Locator;

/**
 * Item product form on items block.
 */
abstract class Product extends AbstractItem
{
    /**
     * Items tax type.
     *
     * @var array
     */
    protected $pricesType = [
        'cart_item_price_excl_tax' => [
            'selector' => '//span[@class="price-excl-tax"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Price'
        ],
        'cart_item_price_incl_tax' => [
            'selector' => '//span[@class="price-incl-tax"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Price'
        ],
        'cart_item_subtotal_excl_tax' => [
            'selector' => '//span[@class="price-excl-tax"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Subtotal'
        ],
        'cart_item_subtotal_incl_tax' => [
            'selector' => '//span[@class="price-incl-tax"]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Subtotal'
        ],
        'cart_item_price' => [
            'selector' => '//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Price'
        ],
        'cart_item_subtotal' => [
            'selector' => '//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH,
            'column' => 'Subtotal'
        ]
    ];

    /**
     * Field selector.
     *
     * @var string
     */
    protected $fieldSelector = '//td[count(//th[contains(text(),"%s")]/preceding-sibling::th)+1]';

    /**
     * Prepare selector for field.
     *
     * @param string $field
     * @return string
     */
    protected function prepareSelector($field)
    {
        $fieldMapping = $this->pricesType[$field];
        return sprintf($this->fieldSelector, $fieldMapping['column']) . $fieldMapping['selector'];
    }
}
