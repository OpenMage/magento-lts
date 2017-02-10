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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\TestStep;

use Mage\Sales\Test\Fixture\Order;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create new order via curlstep.
 */
class CreateNewOrderViaCurlStep implements TestStepInterface
{
    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Order dataset.
     *
     * @var string
     */
    protected $orderdataset;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param string $orderdataset
     */
    public function __construct(FixtureFactory $fixtureFactory, $orderdataset)
    {
        $this->fixtureFactory = $fixtureFactory;
        $this->dataset = $orderdataset;
    }

    /**
     * Create new order via curl.
     *
     * @return array
     */
    public function run()
    {
        /** @var Order $order */
        $order = $this->fixtureFactory->createByCode('order', ['dataset' => $this->orderdataset]);
        $order->persist();

        return ['order' => $order];
    }
}
