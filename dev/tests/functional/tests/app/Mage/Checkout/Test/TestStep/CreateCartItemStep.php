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

namespace Mage\Checkout\Test\TestStep;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create cart item step.
 */
class CreateCartItemStep implements TestStepInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Cart item data.
     *
     * @var array
     */
    protected $cart;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $products
     * @param array|null $cart
     */
    public function __construct(FixtureFactory $fixtureFactory, array $products, array $cart = null)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->cart = array_merge_recursive($cart, ['data' => ['items' => ['products' => $products]]]);
    }

    /**
     * Create cart item.
     *
     * @return array
     */
    public function run()
    {
        return ['cart' => $this->fixtureFactory->createByCode('cart', $this->cart)];
    }
}
