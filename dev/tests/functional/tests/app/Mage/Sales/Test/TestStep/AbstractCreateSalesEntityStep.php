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
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderInvoiceNew;
use Mage\Shipping\Test\Page\Adminhtml\SalesOrderShipmentNew;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreditMemoNew;
use Magento\Mtf\TestStep\TestStepInterface;
use Magento\Mtf\ObjectManager;

/**
 * Create sales entity from order on backend.
 */
abstract class AbstractCreateSalesEntityStep implements TestStepInterface
{
    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Sales entity type.
     *
     * @var string
     */
    protected $entityType;

    /**
     * Orders page.
     *
     * @var SalesOrderIndex
     */
    protected $orderIndex;

    /**
     * Order view page.
     *
     * @var SalesOrderView
     */
    protected $salesOrderView;

    /**
     * New order invoice page.
     *
     * @var SalesOrderInvoiceNew|SalesOrderShipmentNew|SalesOrderCreditMemoNew
     */
    protected $orderSalesEntityNew;

    /**
     * Order fixture.
     *
     * @var Order|null
     */
    protected $order;

    /**
     * Order id.
     *
     * @var string|null
     */
    protected $orderId;

    /**
     * Shipment data.
     *
     * @var array|null
     */
    protected $data;

    /**
     * Products data.
     *
     * @var array|null
     */
    protected $products;

    /**
     * Action for step.
     *
     * @var array|null
     */
    protected $action;

    /**
     * Get sales entity new page.
     *
     * @return SalesOrderInvoiceNew|SalesOrderShipmentNew|SalesOrderCreditMemoNew
     */
    protected abstract function getSalesEntityNewPage();

    /**
     * @construct
     * @param ObjectManager $objectManager
     * @param SalesOrderIndex $orderIndex
     * @param SalesOrderView $salesOrderView
     * @param Order|null $order
     * @param string|null $orderId
     * @param array|null $data
     * @param array|null $products
     * @param array|null $action
     */
    public function __construct(
        ObjectManager $objectManager,
        SalesOrderIndex $orderIndex,
        SalesOrderView $salesOrderView,
        Order $order = null,
        $orderId = null,
        $data = null,
        $products = null,
        array $action = null
    ) {
        $this->objectManager = $objectManager;
        $this->orderIndex = $orderIndex;
        $this->salesOrderView = $salesOrderView;
        $this->orderSalesEntityNew = $this->getSalesEntityNewPage();
        $this->order = $order;
        $this->data = $data;
        $this->action = $action;
        $this->orderId = ($orderId == null) ? $this->order->getId() : $orderId;
        $this->products = ($products == null) ? $this->order->getEntityId()['products'] : $products;
    }

    /**
     * Create sales entity for order on backend.
     *
     * @return array
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->orderId]);
        $this->processStep();

        return ['ids' => [$this->entityType . 'Ids' => $this->getEntityIds($this->entityType)]];
    }

    /**
     * Process step.
     *
     * @param int $index [optional]
     * @return void
     */
    protected function processStep($index = 0)
    {
        if (isset($this->action[$this->entityType][$index]) && $this->action[$this->entityType][$index] == false) {
            return;
        }
        $this->salesEntityAction();
        $this->fillData($index);
        $this->salesEntitySubmit();
    }

    /**
     * Fill data for step.
     *
     * @param int $index
     * @return void
     */
    protected function fillData($index)
    {
        if (!empty($this->data[$this->entityType][$index])) {
            $data = $this->data[$this->entityType][$index];
            $this->orderSalesEntityNew->getFormBlock()->fillData($data, $this->products);
        }
    }

    /**
     * Sales entity submit.
     *
     * @return void
     */
    protected function salesEntitySubmit()
    {
        $this->orderSalesEntityNew->getFormBlock()->submit();
    }

    /**
     * Sales entity action.
     *
     * @return void
     */
    protected function salesEntityAction()
    {
        $this->salesOrderView->getPageActions()->{$this->entityType}();
    }

    /**
     * Get sales entity id.
     *
     * @param string $entity
     * @return array
     */
    public function getEntityIds($entity)
    {
        $this->salesOrderView->getOrderForm()->openTab($entity . 's');
        return $this->salesOrderView->getOrderForm()->getTabElement($entity . 's')->getGrid()->getIds();
    }
}
