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

namespace Mage\Catalog\Test\Fixture\GroupedProduct;

use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data for fill product form on frontend.
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData extends \Mage\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData implements FixtureInterface
{
    /**
     * Return array preset.
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'three_simple_products' => [
                'options' => [
                    [
                        'name' => 'product_key_0',
                        'qty' => 3
                    ],
                    [
                        'name' => 'product_key_1',
                        'qty' => 1
                    ],
                    [
                        'name' => 'product_key_2',
                        'qty' => 2
                    ]
                ],
                'cartItem' => [
                    'price' => [
                        'product_key_0' => 100,
                        'product_key_1' => 100,
                        'product_key_2' => 100
                    ],
                    'qty' => [
                        'product_key_0' => 3,
                        'product_key_1' => 1,
                        'product_key_2' => 2
                    ],
                    'subtotal' => [
                        'product_key_0' => 300,
                        'product_key_1' => 100,
                        'product_key_2' => 200
                    ]
                ]
            ]
        ];
        return isset($presets[$name]) ? $presets[$name] : [];
    }
}
