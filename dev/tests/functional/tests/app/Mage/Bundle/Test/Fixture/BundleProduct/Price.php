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

namespace Mage\Bundle\Test\Fixture\BundleProduct;

use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 */
class Price implements FixtureInterface
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
     * Current preset.
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * @constructor
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(array $params, $data = [])
    {
        $this->params = $params;
        $this->currentPreset = isset($data['preset']) ? $data['preset'] : null;
        $this->data = isset($data['value']) ? $data['value'] : null;
    }

    /**
     * Persists prepared data into application.
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
     * Get preset array.
     *
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'default_dynamic' => [
                'price_from' => '150.00',
                'price_to' => '200.00'
            ],
            'default_fixed' => [
                'price_from' => '760.00',
                'price_to' => '768.00'
            ],
            'default_bundle_fixed' => [
                'price_from' => '760.00',
                'price_to' => '768.00'
            ],
            'dynamic_with_tier_price' => [
                'price_from' => '150.00',
                'price_to' => '200.00'
            ],
            'dynamic_with_group_price' => [
                'price_from' => '15.00',
                'price_to' => '20.00'
            ],
            'fixed_with_special_price' => [
                'price_from' => '11.00',
                'price_to' => '11.80'
            ],
            'dynamic_as_low_as_price' => [
                'price_from' => '150.00'
            ],
            'fixed_as_low_as_price' => [
                'price_from' => '110.00'
            ],
            'all_types_bundle_fixed_and_custom_options' => [
                'price_from' => '290.00',
                'price_to' => '372.00'
            ]
        ];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
