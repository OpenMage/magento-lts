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

use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Preset for price.
 *
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 *
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
     * @param array $data
     */
    public function __construct(array $params, $data = [])
    {
        $this->params = $params;
        $this->data = isset($data['value']) ? $data['value'] : null;
        $this->currentPreset = isset($data['preset']) ? $data['preset'] : null;
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
     * @param $key [optional]
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
     * @return array|null
     */
    public function getPreset()
    {
        $presets = [
            'with_tier_price' => [
                [
                    'percent' => 95
                ],
                [
                    'percent' => 92
                ]
            ]
        ];
        if (!isset($presets[$this->currentPreset])) {
            return null;
        }
        return $presets[$this->currentPreset];
    }
}
