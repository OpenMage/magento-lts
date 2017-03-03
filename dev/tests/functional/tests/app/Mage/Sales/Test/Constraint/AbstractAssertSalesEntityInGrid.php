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

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Fixture\Order;
use Mage\Adminhtml\Test\Block\Widget\Grid;

/**
 * Abstract assert sales entity with corresponding filter is present in 'Sales Entity' with correct data.
 */
abstract class AbstractAssertSalesEntityInGrid extends AbstractAssertSales
{
    /**
     * Special filter fields.
     *
     * @var array
     */
    protected $specialFilterFields;

    /**
     * Entity ids.
     *
     * @var array
     */
    protected $ids;

    /**
     * Get default filter.
     *
     * @param string $entityId
     * @return array
     */
    protected abstract function getDefaultFilter($entityId);

    /**
     * Open page for assert.
     *
     * @return void
     */
    protected abstract function openPage();

    /**
     * Get grid for assert.
     *
     * @return Grid
     */
    protected abstract function getGrid();

    /**
     * Assert sales entity with corresponding filter is present in 'Sales Entity' with correct data.
     *
     * @param array $ids [optional]
     * @param Order|null $order
     * @param string|null $orderId
     * @param array|null $verifyData
     * @return void
     */
    public function processAssert(array $ids = [], Order $order = null, $orderId = null, array $verifyData = null)
    {
        // Set values
        $this->order = $order;
        $this->ids = $ids;
        $this->verifyData = $verifyData;
        $this->orderId = ($orderId == null) ? $order->getId() : $orderId;

        // Process assert
        $this->openPage();
        $grid = $this->getGrid();
        $filters = $this->prepareFilters();
        $this->assert($filters, $grid);
    }

    /**
     * Process assert.
     *
     * @param array $filters
     * @param Grid $grid
     */
    protected function assert(array $filters, $grid)
    {
        foreach ($filters as $filter) {
            \PHPUnit_Framework_Assert::assertTrue(
                $this->isItemInGridVisible($grid, $filter),
                $this->errorMessage
            );
        }
    }

    /**
     * Check visible item in grid.
     *
     * @param Grid $grid
     * @param array $filter
     * @return bool
     */
    protected function isItemInGridVisible($grid, array $filter)
    {
        return $grid->isRowVisible($filter);
    }

    /**
     * Prepare filters for assert.
     *
     * @return array
     */
    protected function prepareFilters()
    {
        $filters = [];
        if (!empty($this->ids)) {
            foreach ($this->ids[$this->entityType . 'Ids'] as $key => $entityId) {
                $filters[] = array_merge($this->getDefaultFilter($entityId), $this->getSpecialFilter($key));
            }
        } else {
            $filters[] = $this->getSpecialFilter();
        }

        return $filters;
    }

    /**
     * Get special filter.
     *
     * @param int $key [optional]
     * @param Order|null $order
     * @return array
     */
    protected function getSpecialFilter($key = 0, Order $order = null)
    {
        $result = [];
        foreach ($this->specialFilterFields as $fieldKey => $fields) {
            foreach ($fields as $field) {
                $result[$field] = isset($this->verifyData[$fieldKey][$this->entityType . 's'][$key][$field])
                    ? $this->verifyData[$fieldKey][$this->entityType . 's'][$key][$field]
                    : $order->{'get' . ucfirst($fieldKey)}()[$this->entityType . 's'][$key][$field];
            }
        }

        return $result;
    }
}
