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

namespace Mage\Weee\Test\Block\Product;

/**
 * This class is used to access the fpt price from the storefront.
 */
class Price extends \Mage\Catalog\Test\Block\Product\Price
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
        'special_price' => [
            'selector' => '.special-price .price'
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
        ],
        'fpt_price' => [
            'selector' => '.weee .price'
        ],
        'final_price' => [
            'selector' => '[id^="product-price-weee"] .price'
        ]
    ];
}
