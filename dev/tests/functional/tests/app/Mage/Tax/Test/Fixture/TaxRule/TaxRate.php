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

namespace Mage\Tax\Test\Fixture\TaxRule;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;

/**
 * Tax rate source for TaxRule fixture.
 *
 * Data keys:
 *  - dataset
 */
class TaxRate extends DataSource
{

    /**
     * Array with tax rate fixtures.
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
                    /** @var \Mage\Tax\Test\Fixture\TaxRate $taxRate */
                    $taxRate = $fixtureFactory->createByCode('taxRate', ['dataset' => $dataset]);
                    if (!$taxRate->hasData('id')) {
                        $taxRate->persist();
                    }
                    $this->fixtures[] = $taxRate;
                    $this->data[] = $taxRate->getCode();
                }
            }
        }
    }

    /**
     * Return tax rate fixtures.
     *
     * @return array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}
