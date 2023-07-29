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

namespace Mage\Customer\Test\TestStep;

use Mage\Customer\Test\Fixture\Address;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create new addresses fixtures step.
 */
class CreateNewAddressesFixturesStep implements TestStepInterface
{
    /**
     * New addresses data.
     *
     * @var Address[]
     */
    protected $newAddresses;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string|null $newAddresses
     */
    public function __construct(FixtureFactory $fixtureFactory, $newAddresses = null)
    {
        $this->newAddresses = $newAddresses;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Create addresses.
     *
     * @return array
     */
    public function run()
    {
        if ($this->newAddresses === null) {
            return [];
        }
        $newAddressesFixtures = [];
        $datasets = explode(',', $this->newAddresses);
        foreach ($datasets as $dataset) {
            $newAddressesFixtures[] = $this->fixtureFactory->createByCode('address', ['dataset' => $dataset]);
        }

        return ['newAddresses' => $newAddressesFixtures];
    }
}
