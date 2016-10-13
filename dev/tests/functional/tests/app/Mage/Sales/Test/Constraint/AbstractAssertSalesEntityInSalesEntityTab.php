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

namespace Mage\Sales\Test\Constraint;

use Magento\Mtf\ObjectManager;
use Mage\Adminhtml\Test\Block\Widget\Grid;
use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderIndex;
use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Abstract assert that sales entity is present in the sales entity tab with correct data.
 */
abstract class AbstractAssertSalesEntityInSalesEntityTab extends AbstractAssertSalesEntityInGrid
{
    /**
     * Sales order view page.
     *
     * @var SalesOrderView
     */
    protected $salesOrderView;

    /**
     * Sales order index page.
     *
     * @var SalesOrderIndex
     */
    protected $orderIndex;

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param EventManagerInterface $eventManager
     * @param SalesOrderView $salesOrderView
     * @param SalesOrderIndex $orderIndex
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        SalesOrderView $salesOrderView,
        SalesOrderIndex $orderIndex
    ) {
        parent::__construct($objectManager, $eventManager);
        $this->salesOrderView = $salesOrderView;
        $this->orderIndex = $orderIndex;
    }

    /**
     * Open page for assert.
     *
     * @return void
     */
    protected function openPage()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->orderId]);
        $this->salesOrderView->getOrderForm()->openTab($this->entityType . 's');
    }

    /**
     * Get grid for assert.
     *
     * @return Grid
     */
    protected function getGrid()
    {
        return $this->salesOrderView->getOrderForm()->getTabElement($this->entityType . 's')->getGrid();
    }

    /**
     * Get default filter.
     *
     * @param string $entityId
     * @return array
     */
    protected function getDefaultFilter($entityId)
    {
        return ['id' => $entityId];
    }
}
