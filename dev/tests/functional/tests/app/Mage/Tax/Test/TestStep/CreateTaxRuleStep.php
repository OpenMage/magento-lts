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

namespace Mage\Tax\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Creating tax rule.
 */
class CreateTaxRuleStep implements TestStepInterface
{
    /**
     * Tax Rule.
     *
     * @var string
     */
    protected $taxRule;

    /**
     * Factory for Fixture.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $taxRule [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, $taxRule = '-')
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->taxRule = $taxRule;
    }

    /**
     * Create tax rule.
     *
     * @return array
     */
    public function run()
    {
        $result['taxRule'] = null;
        if ($this->taxRule != '-') {
            $taxRule = $this->fixtureFactory->createByCode('taxRule', ['dataset' => $this->taxRule]);
            $taxRule->persist();
            $result['taxRule'] = $taxRule;
        }
        return $result;
    }
}
