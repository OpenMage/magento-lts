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

namespace Mage\Sales\Test\TestStep;

use Mage\Customer\Test\Fixture\Address;
use Mage\Customer\Test\Fixture\Customer;
use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Submit Order step.
 */
class SubmitOrderStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $salesOrderCreateIndex;

    /**
     * Sales order view.
     *
     * @var SalesOrderView
     */
    protected $salesOrderView;

    /**
     * Factory for fixtures.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Order fixture.
     *
     * @var Order
     */
    protected $order;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $salesOrderCreateIndex
     * @param SalesOrderView $salesOrderView
     * @param FixtureFactory $fixtureFactory
     * @param Customer|null $customer
     * @param Address|null $billingAddress
     * @param InjectableFixture[] $products [optional]
     * @param Order $order
     */
    public function __construct(
        SalesOrderCreateIndex $salesOrderCreateIndex,
        SalesOrderView $salesOrderView,
        FixtureFactory $fixtureFactory,
        Customer $customer = null,
        Address $billingAddress = null,
        array $products = [],
        Order $order = null
    ) {
        $this->salesOrderCreateIndex = $salesOrderCreateIndex;
        $this->salesOrderView = $salesOrderView;
        $this->fixtureFactory = $fixtureFactory;
        $this->customer = $customer;
        $this->billingAddress = $billingAddress;
        $this->products = $products;
        $this->order = $order;
    }

    /**
     * Fill Sales Data.
     *
     * @return array
     */
    public function run()
    {
        $this->salesOrderCreateIndex->getCreateBlock()->submitOrder();
        $this->salesOrderView->getMessagesBlock()->waitSuccessMessage();
        $orderId = $this->salesOrderView->getTitleBlock()->getOrderId();
        $order = $this->createOrderFixture($orderId);

        return ['orderId' => $orderId, 'order' => $order];
    }

    /**
     * Create order fixture.
     *
     * @param string $orderId
     * @return Order
     */
    protected function createOrderFixture($orderId)
    {
        return ($this->order !== null)
            ? $this->order
            : $this->fixtureFactory->createByCode(
                'order',
                [
                    'data' => [
                        'id' => $orderId,
                        'customer_id' => ['customer' => $this->customer],
                        'entity_id' => ['products' => $this->products],
                        'billing_address_id' => ['billingAddress' => $this->billingAddress],
                    ]
                ]
            );
    }
}
