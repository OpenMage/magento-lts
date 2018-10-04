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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
