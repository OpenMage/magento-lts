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

namespace Mage\Downloadable\Test\Fixture\DownloadableProduct;

/**
 * Data for fill product form on frontend.
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 */
class CheckoutData extends \Mage\Catalog\Test\Fixture\CatalogProductSimple\CheckoutData
{
    /**
     * Get preset array.
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'with_two_separately_links' => [
                'options' => [
                    'links' => [
                        [
                            'label' => 'link_0',
                            'value' => 'Yes'
                        ],
                    ],
                ],
                'cartItem' => [
                    'price' => 22.43,
                    'subtotal' => 22.43
                ],
                'qty' => 1
            ],
            'with_two_bought_links' => [
                'options' => [
                    'links' => [
                        [
                            'label' => 'link_0',
                            'value' => 'Yes'
                        ],
                        [
                            'label' => 'link_1',
                            'value' => 'Yes'
                        ]
                    ],
                    'cartItem' => [
                        'price' => 23,
                        'subtotal' => 23
                    ]
                ],
                'qty' => 1
            ],
            'one_custom_option_and_downloadable_link' => [
                'options' => [
                    'custom_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'option_key_0'
                        ]
                    ],
                    'links' => [
                        [
                            'label' => 'link_0',
                            'value' => 'Yes'
                        ]
                    ],
                ],
                'qty' => 1
            ],
        ];
        return isset($presets[$name]) ? $presets[$name] : [];
    }
}
