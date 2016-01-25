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

namespace Mage\Catalog\Test\Fixture\ConfigurableProduct;

/**
 * Data for fill product form on frontend.
 *
 * Data keys:
 *  - preset (Checkout data verification preset name).
 */
class CheckoutData extends \Mage\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData
{
    /**
     * Get preset array.
     *
     * @param string $name
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'options' => [
                    'configurable_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'option_key_1',
                        ],
                        [
                            'title' => 'attribute_key_1',
                            'value' => 'option_key_1',
                        ]
                    ],
                ],
                'qty' => 2,
                'cartItem' => [
                    'price' => 160,
                    'qty' => 2,
                    'subtotal' => 320
                ]
            ],
            'with_custom_options' => [
                'options' => [
                    'configurable_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'option_key_0',
                        ],
                        [
                            'title' => 'attribute_key_1',
                            'value' => 'option_key_0',
                        ]
                    ],
                    'custom_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'option_key_0'
                        ]
                    ]
                ],
                'qty' => 2,
                'cartItem' => [
                    'price' => 188.48,
                    'qty' => 2,
                    'subtotal' => 376.96
                ]
            ]
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
