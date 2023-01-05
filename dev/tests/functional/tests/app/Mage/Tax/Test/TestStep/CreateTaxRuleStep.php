<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
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
