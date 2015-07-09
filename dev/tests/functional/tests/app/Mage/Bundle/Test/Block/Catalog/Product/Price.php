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

namespace Mage\Bundle\Test\Block\Catalog\Product;

/**
 * This class is used to access the price related information from the storefront.
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
        'old_price' => [
            'selector' => '.old-price .price'
        ],
        'minimal_price' => [
            'selector' => 'p.minimal-price .price',
        ],
        'price_from' => [
            'selector' => 'p.price-from .price',
        ],
        'price_to' => [
            'selector' => 'p.price-to .price',
        ]
    ];

    /**
     * Get price from.
     *
     * @param string $currency
     * @return string
     */
    public function getPriceFrom($currency = '$')
    {
        return $this->getTypePrice('price_from', $currency);
    }

    /**
     * Get price to.
     *
     * @param string $currency
     * @return string
     */
    public function getPriceTo($currency = '$')
    {
        return $this->getTypePrice('price_to', $currency);
    }
}
