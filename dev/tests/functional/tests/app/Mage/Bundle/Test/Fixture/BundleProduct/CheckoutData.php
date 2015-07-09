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

namespace Mage\Bundle\Test\Fixture\BundleProduct;

/**
 * Data for fill product form on frontend.
 *
 * Data keys:
 *  - preset (Checkout data verification preset name)
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
            'default_dynamic' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'title' => 'Drop-down Option',
                            'type' => 'Drop-down',
                            'value' => [
                                'name' => 'product_key_1',
                                'qty' => 2
                            ]
                        ]
                    ]
                ],
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 50.00
                            ]
                        ]
                    ],
                    'price' => 100.00,
                    'subtotal' => 100.00
                ]
            ],
            'default_fixed' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'title' => 'Drop-down Option',
                            'type' => 'Drop-down',
                            'value' => [
                                'name' => 'product_key_1',
                                'qty' => 3
                            ]
                        ]
                    ]
                ],
                'qty' => 1,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 6.00
                            ]
                        ]
                    ],
                    'price' => 768.00,
                    'subtotal' => 768.00
                ]
            ],
            'dynamic_with_tier_price' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_0',
                                'qty' => 2
                            ]
                        ]
                    ]
                ],
                'qty' => 15,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 76.00
                            ]
                        ]
                    ],
                    'price' => 152.00,
                    'subtotal' => '2,280.00'
                ]
            ],
            'dynamic_with_group_price' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_0',
                                'qty' => 4
                            ]
                        ]
                    ]
                ],
                'qty' => 7,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 10.00
                            ]
                        ]
                    ],
                    'price' => 40.00,
                    'subtotal' => 40.00
                ]
            ],
            'fixed_with_special_price' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_1',
                                'qty' => 3
                            ]
                        ]
                    ]
                ],
                'qty' => 2,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 0.60
                            ]
                        ]
                    ],
                    'price' => 11.80,
                    'subtotal' => 23.60
                ]
            ],
            'dynamic_as_low_as_price' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_1',
                                'qty' => 1
                            ]
                        ]
                    ]
                ],
                'qty' => 3,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 50.00
                            ]
                        ]
                    ],
                    'price' => 50.00,
                    'subtotal' => 150.00
                ]
            ],
            'fixed_as_low_as_price' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_1',
                                'qty' => 2
                            ]
                        ]
                    ]
                ],
                'qty' => 4,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 6.00
                            ]
                        ]
                    ],
                    'price' => 112.00,
                    'subtotal' => 448.00
                ]
            ],
            'all_types_bundle_fixed_and_custom_options' => [
                'options' => [
                    'bundle_options' => [
                        'option_key_0' => [
                            'value' => [
                                'name' => 'product_key_0',
                                'qty' => 2
                            ]
                        ],
                        'option_key_1' => [
                            'value' => [
                                'name' => 'product_key_0',
                                'qty' => 2
                            ]
                        ],
                        'option_key_2' => [
                            'value' => [
                                'name' => 'product_key_0',
                            ]
                        ],
                        'option_key_3' => [
                            'value' => [
                                'name' => 'product_key_0',
                            ]
                        ],
                    ],
                    'custom_options' => [
                        [
                            'title' => 'attribute_key_0',
                            'value' => 'Field',
                        ],
                        [
                            'title' => 'attribute_key_1',
                            'value' => 'Area',
                        ],
                        [
                            'title' => 'attribute_key_3',
                            'value' => 'option_key_0',
                        ],
                        [
                            'title' => 'attribute_key_4',
                            'value' => 'option_key_0',
                        ],
                        [
                            'title' => 'attribute_key_5',
                            'value' => 'option_key_0',
                        ],
                        [
                            'title' => 'attribute_key_6',
                            'value' => 'option_key_0',
                        ],
                        [
                            'title' => 'attribute_key_7',
                            'value' => '12/12/2014',
                        ],
                        [
                            'title' => 'attribute_key_8',
                            'value' => '12/12/2014/12/30/AM',
                        ],
                        [
                            'title' => 'attribute_key_9',
                            'value' => '12/12/AM',
                        ],
                    ],
                ],
                'qty' => 4,
                'cartItem' => [
                    'options' => [
                        'bundle_options' => [
                            'option_key_0' => [
                                'price' => 5.00
                            ],
                            'option_key_1' => [
                                'price' => 5.00
                            ],
                            'option_key_2' => [
                                'price' => 5.00
                            ],
                            'option_key_3' => [
                                'price' => 5.00
                            ]
                        ]
                    ],
                    'price' => 290.00,
                    'subtotal' => '1,160.00'
                ]
            ]
        ];
        return isset($presets[$name]) ? $presets[$name] : null;
    }
}
