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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
