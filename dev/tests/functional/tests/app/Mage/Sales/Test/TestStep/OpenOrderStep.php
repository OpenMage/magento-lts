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

namespace Mage\Sales\Test\TestStep;

use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Open order step.
 */
class OpenOrderStep implements TestStepInterface
{
    /**
     * Sales order index page.
     *
     * @var SalesOrderIndex
     */
    protected $orderIndex;

    /**
     * Order instance.
     *
     * @var Order
     */
    protected $order;

    /**
     * @constructor
     * @param Order $order
     * @param SalesOrderIndex $orderIndex
     */
    public function __construct(Order $order, SalesOrderIndex $orderIndex)
    {
        $this->orderIndex = $orderIndex;
        $this->order = $order;
    }

    /**
     * Open order.
     *
     * @return void
     */
    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
    }
}
