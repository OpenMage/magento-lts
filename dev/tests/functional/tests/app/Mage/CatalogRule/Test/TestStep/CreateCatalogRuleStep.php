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

namespace Mage\CatalogRule\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Creating catalog rule.
 */
class CreateCatalogRuleStep implements TestStepInterface
{
    /**
     * Catalog Rule dataset name.
     *
     * @var string
     */
    protected $catalogRule;

    /**
     * Factory for Fixture.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string|null $catalogRule
     */
    public function __construct(FixtureFactory $fixtureFactory, $catalogRule = null)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->catalogRule = empty($catalogRule) ? null : $catalogRule;
    }

    /**
     * Create catalog rule
     *
     * @return array
     */
    public function run()
    {
        $result['catalogRule'] = null;
        if (!empty($this->catalogRule) && $this->catalogRule != '-') {
            $catalogRule = $this->fixtureFactory->createByCode(
                'catalogRule',
                ['dataset' => $this->catalogRule]
            );
            $catalogRule->persist();
            $result['catalogRule'] = $catalogRule;
        }

        return $result;
    }
}
