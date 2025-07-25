<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Reports orders collection
 *
 * @package    Mage_Reports
 *
 * @method Mage_Sales_Model_Order getFirstItem()
 */
class Mage_Reports_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Order_Collection
{
    /**
     * Is live
     *
     * @var bool
     */
    protected $_isLive   = false;

    /**
     * Sales amount expression
     *
     * @var string|null
     */
    protected $_salesAmountExpression;

    /**
     * Check range for live mode
     *
     * @param mixed $range
     * @return $this
     */
    public function checkIsLive($range)
    {
        $this->_isLive = (bool) !Mage::getStoreConfig('sales/dashboard/use_aggregated_data');
        return $this;
    }

    /**
     * Retrieve is live flag for rep
     *
     * @return bool
     */
    public function isLive()
    {
        return $this->_isLive;
    }

    /**
     * Prepare report summary
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return $this
     */
    public function prepareSummary($range, $customStart, $customEnd, $isFilter = 0)
    {
        $this->checkIsLive($range);
        if ($this->_isLive) {
            $this->_prepareSummaryLive($range, $customStart, $customEnd, $isFilter);
        } else {
            $this->_prepareSummaryAggregated($range, $customStart, $customEnd, $isFilter);
        }

        return $this;
    }

    /**
     * Get sales amount expression
     *
     * @return string
     */
    protected function _getSalesAmountExpression()
    {
        if (is_null($this->_salesAmountExpression)) {
            $adapter = $this->getConnection();
            $expressionTransferObject = new Varien_Object([
                'expression' => '%s - %s - %s - (%s - %s - %s)',
                'arguments' => [
                    $adapter->getIfNullSql('main_table.base_total_invoiced', 0),
                    $adapter->getIfNullSql('main_table.base_tax_invoiced', 0),
                    $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0),
                    $adapter->getIfNullSql('main_table.base_total_refunded', 0),
                    $adapter->getIfNullSql('main_table.base_tax_refunded', 0),
                    $adapter->getIfNullSql('main_table.base_shipping_refunded', 0),
                ],
            ]);

            Mage::dispatchEvent('sales_prepare_amount_expression', [
                'collection' => $this,
                'expression_object' => $expressionTransferObject,
            ]);
            $this->_salesAmountExpression = vsprintf(
                $expressionTransferObject->getExpression(),
                $expressionTransferObject->getArguments(),
            );
        }

        return $this->_salesAmountExpression;
    }

    /**
     * Prepare report summary from live data
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @param int $isFilter
     * @return $this
     */
    protected function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter = 0)
    {
        $this->setMainTable('sales/order');
        $adapter = $this->getConnection();

        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);

        $expression = $this->_getSalesAmountExpression();
        if ($isFilter == 0) {
            $this->getSelect()->columns([
                'revenue' => new Zend_Db_Expr(
                    sprintf(
                        'SUM((%s) * %s)',
                        $expression,
                        $adapter->getIfNullSql('main_table.base_to_global_rate', 0),
                    ),
                ),
            ]);
        } else {
            $this->getSelect()->columns([
                'revenue' => new Zend_Db_Expr(sprintf('SUM(%s)', $expression)),
            ]);
        }

        $dateRange = $this->getDateRange($range, $customStart, $customEnd);

        $tzRangeOffsetExpression = $this->_getTZRangeOffsetExpression(
            $range,
            'created_at',
            $dateRange['from'],
            $dateRange['to'],
        );

        $this->getSelect()
            ->columns([
                'quantity' => new Zend_Db_Expr('COUNT(main_table.entity_id)'),
                'range' => $tzRangeOffsetExpression,
            ])
            ->where('main_table.state NOT IN (?)', [
                Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage_Sales_Model_Order::STATE_NEW])
            ->order('range ' . Zend_Db_Select::SQL_ASC)
            ->group($tzRangeOffsetExpression);

        $this->addFieldToFilter('created_at', $dateRange);

        return $this;
    }

    /**
     * Prepare report summary from aggregated data
     *
     * @param string $range
     * @param mixed $customStart
     * @param mixed $customEnd
     * @return $this
     */
    protected function _prepareSummaryAggregated($range, $customStart, $customEnd)
    {
        $this->setMainTable('sales/order_aggregated_created');
        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $rangePeriod = $this->_getRangeExpressionForAttribute($range, 'main_table.period');

        $tableName = $this->getConnection()->quoteIdentifier('main_table.period');
        $rangePeriod2 = str_replace($tableName, "MIN($tableName)", $rangePeriod);

        $this->getSelect()->columns([
            'revenue'  => new Zend_Db_Expr('SUM(main_table.total_revenue_amount)'),
            'quantity' => new Zend_Db_Expr('SUM(main_table.orders_count)'),
            'range' => $rangePeriod2,
        ])
        ->order('range')
        ->group($rangePeriod);

        $this->getSelect()->where(
            $this->_getConditionSql('main_table.period', $this->getDateRange($range, $customStart, $customEnd)),
        );

        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = [0];
        }
        $this->addFieldToFilter('main_table.order_status', ['nin' => $statuses]);

        return $this;
    }

    /**
     * Get range expression
     *
     * @param Mage_Reports_Helper_Data::PERIOD_* $range
     * @return Zend_Db_Expr
     */
    protected function _getRangeExpression($range)
    {
        return match ($range) {
            Mage_Reports_Helper_Data::PERIOD_24_HOURS => $this->getConnection()->getConcatSql([
                $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:'),
                $this->getConnection()->quote('00'),
            ]),
            Mage_Reports_Helper_Data::PERIOD_7_DAYS, Mage_Reports_Helper_Data::PERIOD_1_MONTH, Mage_Reports_Helper_Data::PERIOD_3_MONTHS, Mage_Reports_Helper_Data::PERIOD_6_MONTHS => $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d'),
            default => $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m'),
        };
    }

    /**
     * Retrieve range expression adapted for attribute
     *
     * @param string $range
     * @param string $attribute
     * @return string
     */
    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }

    /**
     * Retrieve query for attribute with timezone conversion
     *
     * @param string $range
     * @param string $attribute
     * @param mixed $from
     * @param mixed $to
     * @return string
     */
    protected function _getTZRangeOffsetExpression($range, $attribute, $from = null, $to = null)
    {
        return str_replace(
            '{{attribute}}',
            Mage::getResourceModel('sales/report_order')
                    ->getStoreTZOffsetQuery($this->getMainTable(), $attribute, $from, $to),
            $this->_getRangeExpression($range),
        );
    }

    /**
     * Retrieve range expression with timezone conversion adapted for attribute
     *
     * @param string $range
     * @param string $attribute
     * @param string $tzFrom
     * @param string $tzTo
     * @return string
     */
    protected function _getTZRangeExpressionForAttribute($range, $attribute, $tzFrom = '+00:00', $tzTo = null)
    {
        if ($tzTo == null) {
            $tzTo = Mage::app()->getLocale()->storeDate()->toString(Zend_Date::GMT_DIFF_SEP);
        }
        $adapter = $this->getConnection();
        $expression = $this->_getRangeExpression($range);
        $attribute  = $adapter->quoteIdentifier($attribute);
        $periodExpr = $adapter->getDateAddSql($attribute, $tzTo, Varien_Db_Adapter_Interface::INTERVAL_HOUR);

        return str_replace('{{attribute}}', $periodExpr, $expression);
    }

    /**
     * Calculate From and To dates (or times) by given period
     *
     * @param Mage_Reports_Helper_Data::PERIOD_* $range
     * @param string $customStart
     * @param string $customEnd
     * @param bool $returnObjects
     * @return array
     */
    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
        $dateEnd   = Mage::app()->getLocale()->date();
        $dateStart = clone $dateEnd;

        // go to the end of a day
        $dateEnd->setHour(23);
        $dateEnd->setMinute(59);
        $dateEnd->setSecond(59);

        $dateStart->setHour(0);
        $dateStart->setMinute(0);
        $dateStart->setSecond(0);

        switch ($range) {
            case Mage_Reports_Helper_Data::PERIOD_24_HOURS:
                $dateEnd = Mage::app()->getLocale()->date();
                $dateEnd->addHour(1);
                $dateStart = clone $dateEnd;
                $dateStart->subDay(1);
                break;

            case Mage_Reports_Helper_Data::PERIOD_7_DAYS:
                // subtract 6 days we need to include
                // only today and not the last one from range
                $dateStart->subDay(6);
                break;

            case Mage_Reports_Helper_Data::PERIOD_1_MONTH:
            case Mage_Reports_Helper_Data::PERIOD_3_MONTHS:
            case Mage_Reports_Helper_Data::PERIOD_6_MONTHS:
                $dateStart->setDay(Mage::getStoreConfig('reports/dashboard/mtd_start'));
                if ($range === Mage_Reports_Helper_Data::PERIOD_3_MONTHS) {
                    $dateStart->subMonth(2);
                } elseif ($range === Mage_Reports_Helper_Data::PERIOD_6_MONTHS) {
                    $dateStart->subMonth(5);
                }
                break;

            case Mage_Reports_Helper_Data::PERIOD_CUSTOM:
                $dateStart = $customStart ?: $dateEnd;
                $dateEnd   = $customEnd ?: $dateEnd;
                break;

            case Mage_Reports_Helper_Data::PERIOD_1_YEAR:
            case Mage_Reports_Helper_Data::PERIOD_2_YEARS:
                $startMonthDay = explode(',', Mage::getStoreConfig('reports/dashboard/ytd_start'));
                $startMonth = isset($startMonthDay[0]) ? (int) $startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int) $startMonthDay[1] : 1;
                $dateStart->setMonth($startMonth);
                $dateStart->setDay($startDay);
                if ($range == '2y') {
                    $dateStart->subYear(1);
                }
                break;
        }

        $dateStart->setTimezone('Etc/UTC');
        $dateEnd->setTimezone('Etc/UTC');

        if ($returnObjects) {
            return [$dateStart, $dateEnd];
        } else {
            return ['from' => $dateStart, 'to' => $dateEnd, 'datetime' => true];
        }
    }

    /**
     * Add item count expression
     *
     * @return $this
     */
    public function addItemCountExpr()
    {
        $this->getSelect()->columns(['items_count' => 'total_item_count'], 'main_table');
        return $this;
    }

    /**
     * Calculate totals report
     *
     * @param int|bool $isFilter
     * @return $this
     */
    public function calculateTotals($isFilter = 0)
    {
        if ($this->isLive()) {
            $this->_calculateTotalsLive($isFilter);
        } else {
            $this->_calculateTotalsAggregated($isFilter);
        }

        return $this;
    }

    /**
     * Calculate totals live report
     *
     * @param int $isFilter
     * @return $this
     */
    protected function _calculateTotalsLive($isFilter = 0)
    {
        $this->setMainTable('sales/order');
        $this->removeAllFieldsFromSelect();

        $adapter = $this->getConnection();

        $baseTaxInvoiced      = $adapter->getIfNullSql('main_table.base_tax_invoiced', 0);
        $baseTaxRefunded      = $adapter->getIfNullSql('main_table.base_tax_refunded', 0);
        $baseShippingInvoiced = $adapter->getIfNullSql('main_table.base_shipping_invoiced', 0);
        $baseShippingRefunded = $adapter->getIfNullSql('main_table.base_shipping_refunded', 0);

        $revenueExp = $this->_getSalesAmountExpression();
        $taxExp = sprintf('%s - %s', $baseTaxInvoiced, $baseTaxRefunded);
        $shippingExp = sprintf('%s - %s', $baseShippingInvoiced, $baseShippingRefunded);

        if ($isFilter == 0) {
            $rateExp = $adapter->getIfNullSql('main_table.base_to_global_rate', 0);
            $this->getSelect()->columns(
                [
                    'revenue'  => new Zend_Db_Expr(sprintf('SUM((%s) * %s)', $revenueExp, $rateExp)),
                    'tax'      => new Zend_Db_Expr(sprintf('SUM((%s) * %s)', $taxExp, $rateExp)),
                    'shipping' => new Zend_Db_Expr(sprintf('SUM((%s) * %s)', $shippingExp, $rateExp)),
                ],
            );
        } else {
            $this->getSelect()->columns(
                [
                    'revenue'  => new Zend_Db_Expr(sprintf('SUM(%s)', $revenueExp)),
                    'tax'      => new Zend_Db_Expr(sprintf('SUM(%s)', $taxExp)),
                    'shipping' => new Zend_Db_Expr(sprintf('SUM(%s)', $shippingExp)),
                ],
            );
        }

        $this->getSelect()->columns([
            'quantity' => 'COUNT(main_table.entity_id)',
        ])
        ->where('main_table.state NOT IN (?)', [
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
            Mage_Sales_Model_Order::STATE_NEW]);

        return $this;
    }

    /**
     * Calculate totals aggregated report
     *
     * @param int $isFilter
     * @return $this
     */
    protected function _calculateTotalsAggregated($isFilter = 0)
    {
        $this->setMainTable('sales/order_aggregated_created');
        $this->removeAllFieldsFromSelect();

        $this->getSelect()->columns([
            'revenue'  => 'SUM(main_table.total_revenue_amount)',
            'tax'      => 'SUM(main_table.total_tax_amount_actual)',
            'shipping' => 'SUM(main_table.total_shipping_amount_actual)',
            'quantity' => 'SUM(orders_count)',
        ]);

        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = [0];
        }

        $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);

        return $this;
    }

    /**
     * Calculate lifitime sales
     *
     * @param bool|int $isFilter
     * @return $this
     */
    public function calculateSales($isFilter = 0)
    {
        $statuses = Mage::getSingleton('sales/config')
            ->getOrderStatusesForState(Mage_Sales_Model_Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = [0];
        }
        $adapter = $this->getConnection();

        if (Mage::getStoreConfig('sales/dashboard/use_aggregated_data')) {
            $this->setMainTable('sales/order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $adapter->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                '0',
            );
            $this->getSelect()->columns([
                'lifetime' => new Zend_Db_Expr('SUM(main_table.total_revenue_amount)'),
                'average'  => $averageExpr,
            ]);

            if (!$isFilter) {
                $this->addFieldToFilter(
                    'store_id',
                    ['eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId()],
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        } else {
            $this->setMainTable('sales/order');
            $this->removeAllFieldsFromSelect();

            $expr = $this->_getSalesAmountExpression();

            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }

            $this->getSelect()
                ->columns([
                    'lifetime' => new Zend_Db_Expr("SUM({$expr})"),
                    'average'  => new Zend_Db_Expr("AVG({$expr})"),
                ])
                ->where('main_table.status NOT IN(?)', $statuses)
                ->where('main_table.state NOT IN(?)', [
                    Mage_Sales_Model_Order::STATE_NEW,
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT]);
        }
        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addFieldToFilter('created_at', ['from' => $from, 'to' => $to])
            ->addFieldToFilter('state', ['neq' => Mage_Sales_Model_Order::STATE_CANCELED])
            ->getSelect()
            ->columns(['orders' => 'COUNT(DISTINCT(main_table.entity_id))'])
            ->group('entity_id');

        $this->getSelect()->columns([
            'items' => 'SUM(main_table.total_qty_ordered)']);

        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $adapter = $this->getConnection();
        $baseSubtotalInvoiced = $adapter->getIfNullSql('main_table.base_subtotal_invoiced', 0);
        $baseDiscountRefunded = $adapter->getIfNullSql('main_table.base_discount_refunded', 0);
        $baseSubtotalRefunded = $adapter->getIfNullSql('main_table.base_subtotal_refunded', 0);
        $baseDiscountInvoiced = $adapter->getIfNullSql('main_table.base_discount_invoiced', 0);
        $baseTotalInvocedCost = $adapter->getIfNullSql('main_table.base_total_invoiced_cost', 0);
        if ($storeIds) {
            $this->getSelect()->columns([
                'subtotal'  => new Zend_Db_Expr('SUM(main_table.base_subtotal)'),
                'tax'       => new Zend_Db_Expr('SUM(main_table.base_tax_amount)'),
                'shipping'  => new Zend_Db_Expr('SUM(main_table.base_shipping_amount)'),
                'discount'  => new Zend_Db_Expr('SUM(main_table.base_discount_amount)'),
                'total'     => new Zend_Db_Expr('SUM(main_table.base_grand_total)'),
                'invoiced'  => new Zend_Db_Expr('SUM(main_table.base_total_paid)'),
                'refunded'  => new Zend_Db_Expr('SUM(main_table.base_total_refunded)'),
                'profit'    => new Zend_Db_Expr("SUM($baseSubtotalInvoiced) "
                                . "+ SUM({$baseDiscountRefunded}) - SUM({$baseSubtotalRefunded}) "
                                . "- SUM({$baseDiscountInvoiced}) - SUM({$baseTotalInvocedCost})"),
            ]);
        } else {
            $this->getSelect()->columns([
                'subtotal'  => new Zend_Db_Expr('SUM(main_table.base_subtotal * main_table.base_to_global_rate)'),
                'tax'       => new Zend_Db_Expr('SUM(main_table.base_tax_amount * main_table.base_to_global_rate)'),
                'shipping'  => new Zend_Db_Expr('SUM(main_table.base_shipping_amount * main_table.base_to_global_rate)'),
                'discount'  => new Zend_Db_Expr('SUM(main_table.base_discount_amount * main_table.base_to_global_rate)'),
                'total'     => new Zend_Db_Expr('SUM(main_table.base_grand_total * main_table.base_to_global_rate)'),
                'invoiced'  => new Zend_Db_Expr('SUM(main_table.base_total_paid * main_table.base_to_global_rate)'),
                'refunded'  => new Zend_Db_Expr('SUM(main_table.base_total_refunded * main_table.base_to_global_rate)'),
                'profit'    => new Zend_Db_Expr("SUM({$baseSubtotalInvoiced} *  main_table.base_to_global_rate) "
                                . "+ SUM({$baseDiscountRefunded} * main_table.base_to_global_rate) "
                                . "- SUM({$baseSubtotalRefunded} * main_table.base_to_global_rate) "
                                . "- SUM({$baseDiscountInvoiced} * main_table.base_to_global_rate) "
                                . "- SUM({$baseTotalInvocedCost} * main_table.base_to_global_rate)"),
            ]);
        }

        return $this;
    }

    /**
     * Add group By customer attribute
     *
     * @return $this
     */
    public function groupByCustomer()
    {
        $this->getSelect()
            ->where('main_table.customer_id IS NOT NULL')
            ->group('main_table.customer_id');

        /*
         * Allow Analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Join Customer Name (concat)
     *
     * @param string $alias
     * @return $this
     */
    public function joinCustomerName($alias = 'name')
    {
        $fields  = [
            'main_table.customer_firstname',
            'main_table.customer_middlename',
            'main_table.customer_lastname',
        ];
        $fieldConcat = $this->getConnection()->getConcatSql($fields, ' ');
        $this->getSelect()->columns([$alias => $fieldConcat]);
        return $this;
    }

    /**
     * Add Order count field to select
     *
     * @return $this
     */
    public function addOrdersCount()
    {
        $this->addFieldToFilter('state', ['neq' => Mage_Sales_Model_Order::STATE_CANCELED]);
        $this->getSelect()
            ->columns(['orders_count' => 'COUNT(main_table.entity_id)']);

        return $this;
    }

    /**
     * Add revenue
     *
     * @param bool $convertCurrency
     * @return $this
     */
    public function addRevenueToSelect($convertCurrency = false)
    {
        if ($convertCurrency) {
            $this->getSelect()->columns([
                'revenue' => '(main_table.base_grand_total * main_table.base_to_global_rate)',
            ]);
        } else {
            $this->getSelect()->columns([
                'revenue' => 'base_grand_total',
            ]);
        }

        return $this;
    }

    /**
     * Add summary average totals
     *
     * @param int $storeId
     * @return $this
     */
    public function addSumAvgTotals($storeId = 0)
    {
        $adapter = $this->getConnection();
        $baseSubtotalRefunded = $adapter->getIfNullSql('main_table.base_subtotal_refunded', 0);
        $baseSubtotalCanceled = $adapter->getIfNullSql('main_table.base_subtotal_canceled', 0);
        $baseDiscountCanceled = $adapter->getIfNullSql('main_table.base_discount_canceled', 0);

        /**
         * calculate average and total amount
         */
        $expr = ($storeId == 0)
            ? "(main_table.base_subtotal -
            {$baseSubtotalRefunded} - {$baseSubtotalCanceled} - ABS(main_table.base_discount_amount) -
            {$baseDiscountCanceled}) * main_table.base_to_global_rate"
            : "main_table.base_subtotal - {$baseSubtotalCanceled} - {$baseSubtotalRefunded} -
            ABS(main_table.base_discount_amount) - {$baseDiscountCanceled}";

        $this->getSelect()
            ->columns(['orders_avg_amount' => "AVG({$expr})"])
            ->columns(['orders_sum_amount' => "SUM({$expr})"]);

        return $this;
    }

    /**
     * Sort order by total amount
     *
     * @param string $dir
     * @return $this
     */
    public function orderByTotalAmount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('orders_sum_amount ' . $dir);
        return $this;
    }

    /**
     * Order by orders count
     *
     * @param string $dir
     * @return $this
     */
    public function orderByOrdersCount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('orders_count ' . $dir);
        return $this;
    }

    /**
     * Order by customer registration
     *
     * @param string $dir
     * @return $this
     */
    public function orderByCustomerRegistration($dir = self::SORT_ORDER_DESC)
    {
        $this->setOrder('customer_id', $dir);
        return $this;
    }

    /**
     * Sort order by order created_at date
     *
     * @param string $dir
     * @return $this
     */
    public function orderByCreatedAt($dir = self::SORT_ORDER_DESC)
    {
        $this->setOrder('created_at', $dir);
        return $this;
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns('COUNT(DISTINCT main_table.entity_id)');

        return $countSelect;
    }

    /**
     * Initialize initial fields to select
     *
     * @return $this
     */
    protected function _initInitialFieldsToSelect()
    {
        // No fields should be initialized
        return $this;
    }

    /**
     * Add period filter by created_at attribute
     *
     * @param string $period
     * @return $this
     */
    public function addCreateAtPeriodFilter($period)
    {
        [$from, $to] = $this->getDateRange($period, 0, 0, true);

        $this->checkIsLive($period);

        if ($this->isLive()) {
            $fieldToFilter = 'created_at';
        } else {
            $fieldToFilter = 'period';
        }

        $this->addFieldToFilter($fieldToFilter, [
            'from'  => $from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
            'to'    => $to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
        ]);

        return $this;
    }
}
