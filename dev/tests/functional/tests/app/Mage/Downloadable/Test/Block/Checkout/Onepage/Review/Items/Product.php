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
