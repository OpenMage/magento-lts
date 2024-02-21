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

namespace Mage\Core\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Setup configuration using handler.
 */
class SetupConfigurationStep implements TestStepInterface
{
    /**
     * Factory for Fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Configuration data.
     *
     * @var string
     */
    protected $configData;

    /**
     * Rollback.
     *
     * @var bool
     */
    protected $rollback;

    /**
     * Preparing step properties.
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string|null $configData
     * @param bool $rollback [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, $configData = null, $rollback = false)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->configData = $configData;
        $this->rollback = $rollback;
    }

    /**
     * Set config.
     *
     * @return array
     */
    public function run()
    {
        if ($this->configData == '-' || $this->configData === null) {
            return [];
        }
        $prefix = ($this->rollback == false) ? '' : '_rollback';

        $configData = array_map('trim', explode(',', $this->configData));
        $result = [];

        foreach ($configData as $configdataset) {
            $config = $this->fixtureFactory->createByCode('configData', ['dataset' => $configdataset . $prefix]);
            if ($config->hasData('section')) {
                $config->persist();
                $result[] = $config;
            }
        }

        return ['config' => $result];
    }
}
