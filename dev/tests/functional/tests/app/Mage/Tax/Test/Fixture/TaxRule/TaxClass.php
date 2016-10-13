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

namespace Mage\Tax\Test\Fixture\TaxRule;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Tax class source for TaxRule fixture.
 *
 * Data keys:
 *  - dataset
 */
class TaxClass extends DataSource
{
    /**
     * Array with tax class fixtures.
     *
     * @var array
     */
    protected $fixtures;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset'])) {
            foreach ($data['dataset'] as $dataset) {
                if ($dataset !== '-') {
                    /** @var \Mage\Tax\Test\Fixture\TaxClass $taxClass */
                    $taxClass = $fixtureFactory->createByCode('taxClass', ['dataset' => $dataset]);
                    if (!$taxClass->hasData('id')) {
                        $taxClass->persist();
                    }
                    $this->fixtures[] = $taxClass;
                    $this->data[] = $taxClass->getClassName();
                }
            }
        }
    }

    /**
     * Return tax class fixtures
     *
     * @return array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}
