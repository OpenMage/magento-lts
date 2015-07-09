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

namespace Mage\Catalog\Test\Fixture\CatalogProductSimple;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Preset for custom options.
 *
 * Data keys:
 *  - preset (Custom options preset name)
 *  - import_products (comma separated data set name)
 */
class CustomOptions implements FixtureInterface
{
    /**
     * Prepared dataSet data.
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * @constructor
     * @param array $params
     * @param array $data
     * @param FixtureFactory|null $fixtureFactory
     */
    public function __construct(array $params, array $data, FixtureFactory $fixtureFactory)
    {
        $this->params = $params;
        $preset = [];
        if (isset($data['preset'])) {
            $preset = $this->replaceData($this->getPreset($data['preset']), mt_rand());
            unset($data['preset']);
        }
        $this->data = array_merge_recursive($data, $preset);
    }

    /**
     * Replace custom options data.
     *
     * @param array $data
     * @param int $replace
     * @return array
     */
    protected function replaceData(array $data, $replace)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->replaceData($value, $replace);
            }
            $result[$key] = str_replace('%isolation%', $replace, $value);
        }

        return $result;
    }

    /**
     * Persist custom selections products.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
     *
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Get preset.
     *
     * @param string $name
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getPreset($name)
    {
        $presets = [
            'drop_down_with_two_options' => [
                [
                    'title' => 'custom option Drop-down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '30 bucks',
                            'price' => 30,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_drop_down_row_1'
                        ],
                        [
                            'title' => '40 bucks',
                            'price' => 40,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_2'
                        ]
                    ]
                ]
            ],
            'drop_down_with_one_option_percent_price' => [
                [
                    'title' => 'custom option Drop-down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '40 bucks',
                            'price' => 40,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_1'
                        ]
                    ]
                ]
            ],
            'default' => [
                [
                    'title' => 'custom option Drop-down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '10 percent',
                            'price' => 10,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_1',
                        ],
                    ],
                ],
                [
                    'title' => 'custom option Drop-down2 %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '20 percent',
                            'price' => 20,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_2',
                        ],
                    ]
                ],
            ],
            'all_types' => [
                [
                    'title' => 'custom option Field %isolation%',
                    'type' => 'Text/Field',
                    'is_require' => 'Yes',
                    'options' => [
                        'price' => 10,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_field_option_%isolation%',
                        'max_characters' => 1024,
                    ],
                ],
                [
                    'title' => 'custom option Area %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Text/Area',
                    'options' => [
                        'price' => 10,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_area_row_%isolation%',
                        'max_characters' => '10',
                    ]
                ],
                [
                    'title' => 'custom option File %isolation%',
                    'is_require' => 'No',
                    'type' => 'File/File',
                    'options' => [
                        'price' => 10,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_file_row_%isolation%',
                        'file_extension' => 'jpg',
                        'image_size_x' => '100',
                        'image_size_y' => '100',
                    ]
                ],
                [
                    'title' => 'custom option Drop-down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '10 percent',
                            'price' => 10,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_1_%isolation%',
                        ],
                        [
                            'title' => '20 percent',
                            'price' => 20,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_2_%isolation%'
                        ],
                        [
                            'title' => '30 fixed',
                            'price' => 30,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_drop_down_row_3_%isolation%'
                        ],
                    ]
                ],
                [
                    'title' => 'custom option Radio Buttons %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Radio Buttons',
                    'options' => [
                        [
                            'title' => '20 fixed',
                            'price' => 20,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_radio_buttons_row%isolation%',
                        ],
                    ]
                ],
                [
                    'title' => 'custom option Checkbox %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Checkbox',
                    'options' => [
                        [
                            'title' => '20 fixed',
                            'price' => 20,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_checkbox_row%isolation%',
                        ],
                    ]
                ],
                [
                    'title' => 'custom option Multiple Select %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Multiple Select',
                    'options' => [
                        [
                            'title' => '20 fixed',
                            'price' => 20,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_multiple_select_row%isolation%',
                        ],
                    ]
                ],
                [
                    'title' => 'custom option Date %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Date/Date',
                    'options' => [
                        'price' => 20,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_date_row%isolation%',
                    ]
                ],
                [
                    'title' => 'custom option Date & Time %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Date/Date & Time',
                    'options' => [
                        'price' => 20,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_date_and_time_row%isolation%',
                    ]
                ],
                [
                    'title' => 'custom option Time %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Date/Time',
                    'options' => [
                        'price' => 20,
                        'price_type' => 'Fixed',
                        'sku' => 'sku_time_row%isolation%',
                    ]
                ],
            ],
            'two_options' => [
                [
                    'title' => 'custom option drop down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '10 percent',
                            'price' => 10,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_1',
                        ],
                        [
                            'title' => '20 percent',
                            'price' => 20,
                            'price_type' => 'Percent',
                            'sku' => 'sku_drop_down_row_2'
                        ],
                    ],
                ],
                [
                    'title' => 'custom option field %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Text/Field',
                    'options' => [
                        [
                            'price' => 10,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_field_option_%isolation%',
                            'max_characters' => 1024,
                        ],
                    ]
                ],
            ],
            'drop_down_with_one_option_fixed_price' => [
                [
                    'title' => 'custom option drop down %isolation%',
                    'is_require' => 'Yes',
                    'type' => 'Select/Drop-down',
                    'options' => [
                        [
                            'title' => '30 bucks',
                            'price' => 30,
                            'price_type' => 'Fixed',
                            'sku' => 'sku_drop_down_row_1',
                        ],
                    ],
                ],
            ],
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
