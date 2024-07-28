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

namespace Mage\Checkout\Test\Block\Onepage\Review\Items;

use Mage\Checkout\Test\Block\AbstractItem;

/**
 * Item product form on order review items block.
 */
class Product extends AbstractItem
{
    /**
     * Items tax type.
     *
     * @var array
     */
    protected $pricesType = [
        'cart_item_price_excl_tax' => [
            'selector' => '[data-rwd-label="Price (Excl. Tax)"] span.price'
        ],
        'cart_item_price_incl_tax' => [
            'selector' => '[data-rwd-label="Price (Incl. Tax)"] span.price'
        ],
        'cart_item_subtotal_excl_tax' => [
            'selector' => '[data-rwd-label="Subtotal (Excl. Tax)"] span.price'
        ],
        'cart_item_subtotal_incl_tax' => [
            'selector' => '[data-rwd-label="Subtotal (Incl. Tax)"] span.price'
        ],
        'cart_item_price' => ['selector' => '[data-rwd-label="Price"] .cart-price .price'],
        'cart_item_subtotal' => ['selector' => '[data-rwd-label="Subtotal"] .cart-price .price']
    ];
}
