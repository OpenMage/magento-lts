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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Downloadable\Test\Block\Checkout\Onepage\Review\Items;

use Magento\Mtf\Client\Locator;

/**
 * Item product form on order review items block.
 */
class Product extends \Mage\Checkout\Test\Block\Onepage\Review\Items\Product
{
    /**
     * Items tax type.
     *
     * @var array
     */
    protected $pricesType = [
        'cart_item_price_excl_tax' => [
            'selector' => '//*[contains(@data-rwd-label,"Price")][1]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'cart_item_price_incl_tax' => [
            'selector' => '//*[contains(@data-rwd-label,"Price")][2]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'cart_item_subtotal_excl_tax' => [
            'selector' => '//*[contains(@data-rwd-label,"Subtotal")][1]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ],
        'cart_item_subtotal_incl_tax' => [
            'selector' => '//*[contains(@data-rwd-label,"Subtotal")][2]//span[@class="price"]',
            'strategy' => Locator::SELECTOR_XPATH
        ]
    ];
}
