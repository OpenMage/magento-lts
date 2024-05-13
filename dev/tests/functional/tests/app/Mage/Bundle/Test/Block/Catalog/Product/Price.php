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
