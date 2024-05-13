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

namespace Mage\Catalog\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create product using handler.
 */
class CreateProductStep implements TestStepInterface
{
    /**
     * Product fixture from dataset.
     *
     * @var string
     */
    protected $product;

    /**
     * Factory for Fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Product data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $product
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, $product, array $data = [])
    {
        $this->product = $product;
        $this->fixtureFactory = $fixtureFactory;
        $this->data = $data;
    }

    /**
     * Create product.
     *
     * @return array
     */
    public function run()
    {
        list($fixtureClass, $dataset) = explode('::', $this->product);
        /** @var FixtureInterface $product */
        $product = $this->fixtureFactory->createByCode(
            trim($fixtureClass),
            ['dataset' => trim($dataset), 'data' => $this->data]
        );
        if ($product->hasData('id') === false) {
            $product->persist();
        }

        return ['product' => $product];
    }
}
