<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax Calculation Resource Model
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Calculation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Rates cache
     *
     * @var array
     */
    protected $_ratesCache = [];

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    protected function _construct()
    {
        $this->_setMainTable('tax/tax_calculation');
    }

    /**
     * Delete calculation settings by rule id
     *
     * @param int $ruleId
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function deleteByRuleId($ruleId)
    {
        $conn = $this->_getWriteAdapter();
        $where = $conn->quoteInto('tax_calculation_rule_id = ?', (int) $ruleId);
        $conn->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Retrieve distinct calculation
     *
     * @param  string $field
     * @param  int $ruleId
     * @return array
     * @throws Mage_Core_Exception
     */
    public function getDistinct($field, $ruleId)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), $field)
            ->where('tax_calculation_rule_id = ?', (int) $ruleId);

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Get tax rate information: calculation process data and tax rate
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getRateInfo($request)
    {
        $rates = $this->_getRates($request);
        return [
            'process' => $this->getCalculationProcess($request, $rates),
            'value'   => $this->_calculateRate($rates),
        ];
    }

    /**
     * Get tax rate for specific tax rate request
     *
     * @param Varien_Object $request
     * @return int
     */
    public function getRate($request)
    {
        return $this->_calculateRate($this->_getRates($request));
    }

    /**
     * Retrieve Calculation Process
     *
     * @param null|Varien_Object $request
     * @param null|array $rates
     * @return array
     */
    public function getCalculationProcess($request, $rates = null)
    {
        if (is_null($rates)) {
            $rates = $this->_getRates($request);
        }

        $result = [];
        $row = [];
        $ids = [];
        $currentRate = 0;
        $totalPercent = 0;
        $countedRates = count($rates);
        for ($index = 0; $index < $countedRates; $index++) {
            $rate = $rates[$index];
            $value = ($rate['value'] ?? $rate['percent']) * 1;

            $oneRate = [
                'code' => $rate['code'],
                'title' => $rate['title'],
                'percent' => $value,
                'position' => $rate['position'],
                'priority' => $rate['priority'],
            ];
            if (isset($rate['tax_calculation_rule_id'])) {
                $oneRate['rule_id'] = $rate['tax_calculation_rule_id'];
            }

            if (isset($rate['hidden'])) {
                $row['hidden'] = $rate['hidden'];
            }

            if (isset($rate['amount'])) {
                $row['amount'] = $rate['amount'];
            }

            if (isset($rate['base_amount'])) {
                $row['base_amount'] = $rate['base_amount'];
            }

            if (isset($rate['base_real_amount'])) {
                $row['base_real_amount'] = $rate['base_real_amount'];
            }

            $row['rates'][] = $oneRate;

            $ruleId = null;
            if (isset($rates[$index + 1]['tax_calculation_rule_id'])) {
                $ruleId = $rate['tax_calculation_rule_id'];
            }

            $priority = $rate['priority'];
            $ids[] = $rate['code'];

            if (isset($rates[$index + 1]['tax_calculation_rule_id'])) {
                while (isset($rates[$index + 1]) && $rates[$index + 1]['tax_calculation_rule_id'] == $ruleId) {
                    $index++;
                }
            }

            $currentRate += $value;

            if (!isset($rates[$index + 1]) || $rates[$index + 1]['priority'] != $priority
                || (isset($rates[$index + 1]['process']) && $rates[$index + 1]['process'] != $rate['process'])
            ) {
                if (!empty($rates[$index]['calculate_subtotal'])) {
                    $row['percent'] = $currentRate;
                    $totalPercent += $currentRate;
                } else {
                    $row['percent'] = $this->_collectPercent($totalPercent, $currentRate);
                    $totalPercent += $row['percent'];
                }

                $row['id'] = implode('', $ids);
                $result[] = $row;
                $row = [];
                $ids = [];

                $currentRate = 0;
            }
        }

        return $result;
    }

    /**
     * Return combined percent value
     *
     * @param float|int $percent
     * @param float|int $rate
     * @return int
     */
    protected function _collectPercent($percent, $rate)
    {
        return (100 + $percent) * ($rate / 100);
    }

    /**
     * Create search templates for postcode
     *
     * @param string $postcode
     * @return array  $strArr
     */
    protected function _createSearchPostCodeTemplates($postcode)
    {
        $len = Mage::helper('tax')->getPostCodeSubStringLength();
        $strlen = mb_strlen($postcode);
        if ($strlen > $len) {
            $postcode = mb_substr($postcode, 0, $len);
            $strlen = $len;
        }

        $strArr = [(string) $postcode, $postcode . '*'];
        if ($strlen > 1) {
            for ($index = 1; $index < $strlen; $index++) {
                $strArr[] = sprintf('%s*', mb_substr($postcode, 0, - $index));
            }
        }

        return $strArr;
    }

    /**
     * Returns tax rates for request - either pereforms SELECT from DB, or returns already cached result
     * Notice that productClassId due to optimization can be array of ids
     *
     * @param null|Varien_Object $request
     * @return array
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Select_Exception
     */
    protected function _getRates($request)
    {
        if (!$request instanceof Varien_Object) {
            return [];
        }

        // Extract params that influence our SELECT statement and use them to create cache key
        $storeId = Mage::app()->getStore($request->getStore())->getId();
        $customerClassId = $request->getCustomerClassId();
        $countryId = $request->getCountryId();
        $regionId = $request->getRegionId();
        $postcode = trim((string) $request->getPostcode());

        // Process productClassId as it can be array or usual value. Form best key for cache.
        $productClassId = $request->getProductClassId();
        $ids = is_array($productClassId) ? $productClassId : [$productClassId];
        foreach ($ids as $key => $val) {
            $ids[$key] = (int) $val; // Make it integer for equal cache keys even in case of null/false/0 values
        }

        $ids = array_unique($ids);
        sort($ids);
        $productClassKey = implode(',', $ids);

        // Form cache key and either get data from cache or from DB
        $cacheKey = implode('|', [$storeId, $customerClassId, $productClassKey, $countryId, $regionId, $postcode]);

        if (!isset($this->_ratesCache[$cacheKey])) {
            // Make SELECT and get data
            $select = $this->_getReadAdapter()->select();
            $select
                ->from(
                    ['main_table' => $this->getMainTable()],
                    ['tax_calculation_rate_id',
                        'tax_calculation_rule_id',
                        'customer_tax_class_id',
                        'product_tax_class_id',
                    ],
                )
                ->where('customer_tax_class_id = ?', (int) $customerClassId);
            if ($productClassId) {
                $select->where('product_tax_class_id IN (?)', $productClassId);
            }

            $ifnullTitleValue = $this->_getReadAdapter()->getCheckSql(
                'title_table.value IS NULL',
                'rate.code',
                'title_table.value',
            );
            $ruleTableAliasName = $this->_getReadAdapter()->quoteIdentifier('rule.tax_calculation_rule_id');
            $select
                ->join(
                    ['rule' => $this->getTable('tax/tax_calculation_rule')],
                    $ruleTableAliasName . ' = main_table.tax_calculation_rule_id',
                    ['rule.priority', 'rule.position', 'rule.calculate_subtotal'],
                )
                ->join(
                    ['rate' => $this->getTable('tax/tax_calculation_rate')],
                    'rate.tax_calculation_rate_id = main_table.tax_calculation_rate_id',
                    [
                        'value' => 'rate.rate',
                        'rate.tax_country_id',
                        'rate.tax_region_id',
                        'rate.tax_postcode',
                        'rate.tax_calculation_rate_id',
                        'rate.code',
                    ],
                )
                ->joinLeft(
                    ['title_table' => $this->getTable('tax/tax_calculation_rate_title')],
                    'rate.tax_calculation_rate_id = title_table.tax_calculation_rate_id '
                    . "AND title_table.store_id = '$storeId'",
                    ['title' => $ifnullTitleValue],
                )
                ->where('rate.tax_country_id = ?', $countryId)
                ->where('rate.tax_region_id IN(?)', [0, (int) $regionId]);
            $postcodeIsNumeric = is_numeric($postcode);
            $postcodeIsRange = false;
            if (preg_match('/^(.+)-(.+)$/', $postcode, $matches)) {
                if (is_numeric($matches[2]) && strlen($matches[2]) < 5) {
                    $postcodeIsNumeric = true;
                } else {
                    $postcodeIsRange = true;
                    $zipFrom = $matches[1];
                    $zipTo = $matches[2];
                }
            }

            if ($postcodeIsNumeric || $postcodeIsRange) {
                $selectClone = clone $select;
                $selectClone->where('rate.zip_is_range IS NOT NULL');
            }

            $select->where('rate.zip_is_range IS NULL');

            if ($postcode != '*' || $postcodeIsRange) {
                $select
                    ->where(
                        "rate.tax_postcode IS NULL OR rate.tax_postcode IN('*', '', ?)",
                        $postcodeIsRange ? $postcode : $this->_createSearchPostCodeTemplates($postcode),
                    );
                if (isset($selectClone) && $postcodeIsNumeric) {
                    $selectClone
                        ->where('? BETWEEN rate.zip_from AND rate.zip_to', $postcode);
                } elseif (isset($selectClone, $zipFrom, $zipTo) && $postcodeIsRange) {
                    $selectClone->where('rate.zip_from >= ?', $zipFrom)
                        ->where('rate.zip_to <= ?', $zipTo);
                }
            }

            /**
             * @see ZF-7592 issue http://framework.zend.com/issues/browse/ZF-7592
             */
            if (isset($selectClone) && ($postcodeIsNumeric || $postcodeIsRange)) {
                $select = $this->_getReadAdapter()->select()->union(
                    [
                        '(' . $select . ')',
                        '(' . $selectClone . ')',
                    ],
                );
            }

            $select->order('priority ' . Varien_Db_Select::SQL_ASC)
                   ->order('tax_calculation_rule_id ' . Varien_Db_Select::SQL_ASC)
                   ->order('tax_country_id ' . Varien_Db_Select::SQL_DESC)
                   ->order('tax_region_id ' . Varien_Db_Select::SQL_DESC)
                   ->order('tax_postcode ' . Varien_Db_Select::SQL_DESC)
                   ->order('value ' . Varien_Db_Select::SQL_DESC);

            $this->_ratesCache[$cacheKey] = $this->_getReadAdapter()->fetchAll($select);
        }

        return $this->_ratesCache[$cacheKey];
    }

    /**
     * Get rate ids applicable for some address
     *
     * @param Varien_Object $request
     * @return array
     */
    public function getApplicableRateIds($request)
    {
        $countryId = $request->getCountryId();
        $regionId = $request->getRegionId();
        $postcode = $request->getPostcode();

        $select = $this->_getReadAdapter()->select()
            ->from(['rate' => $this->getTable('tax/tax_calculation_rate')], ['tax_calculation_rate_id'])
            ->where('rate.tax_country_id = ?', $countryId)
            ->where('rate.tax_region_id IN(?)', [0, (int) $regionId]);

        $expr = $this->_getWriteAdapter()->getCheckSql(
            'zip_is_range is NULL',
            $this->_getWriteAdapter()->quoteInto(
                "rate.tax_postcode IS NULL OR rate.tax_postcode IN('*', '', ?)",
                $this->_createSearchPostCodeTemplates($postcode),
            ),
            $this->_getWriteAdapter()->quoteInto('? BETWEEN rate.zip_from AND rate.zip_to', $postcode),
        );
        $select->where($expr);
        $select->order('tax_calculation_rate_id');
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Calculate rate
     *
     * @param array $rates
     * @return int
     */
    protected function _calculateRate($rates)
    {
        $result      = 0;
        $currentRate = 0;
        $countedRates = count($rates);
        for ($index = 0; $index < $countedRates; $index++) {
            $rate       = $rates[$index];
            $rule       = $rate['tax_calculation_rule_id'];
            $value      = $rate['value'];
            $priority   = $rate['priority'];

            while (isset($rates[$index + 1]) && $rates[$index + 1]['tax_calculation_rule_id'] == $rule) {
                $index++;
            }

            $currentRate += $value;

            if (!isset($rates[$index + 1]) || $rates[$index + 1]['priority'] != $priority) {
                if (!empty($rates[$index]['calculate_subtotal'])) {
                    $result += $currentRate;
                } else {
                    $result += $this->_collectPercent($result, $currentRate);
                }

                $currentRate = 0;
            }
        }

        return $result;
    }

    /**
     * Retrieve rate ids
     *
     * @param Varien_Object $request
     * @return array
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Select_Exception
     */
    public function getRateIds($request)
    {
        $result = [];
        $rates  = $this->_getRates($request);
        $countedRates = count($rates);
        for ($index = 0; $index < $countedRates; $index++) {
            $rate = $rates[$index];
            $rule = $rate['tax_calculation_rule_id'];
            $result[] = $rate['tax_calculation_rate_id'];
            while (isset($rates[$index + 1]) && $rates[$index + 1]['tax_calculation_rule_id'] == $rule) {
                $index++;
            }
        }

        return $result;
    }

    /**
     * Retrieve rates by customer tax class
     *
     * @param int $customerTaxClass
     * @param null|int $productTaxClass
     * @return array
     */
    public function getRatesByCustomerTaxClass($customerTaxClass, $productTaxClass = null)
    {
        $adapter = $this->_getReadAdapter();
        $customerTaxClassId = (int) $customerTaxClass;
        $calcJoinConditions = [
            'calc_table.tax_calculation_rate_id = main_table.tax_calculation_rate_id',
            $adapter->quoteInto('calc_table.customer_tax_class_id = ?', $customerTaxClassId),

        ];
        if ($productTaxClass !== null) {
            $productTaxClassId = (int) $productTaxClass;
            $calcJoinConditions[] = $adapter->quoteInto('calc_table.product_tax_class_id = ?', $productTaxClassId);
        }

        $selectCSP = $adapter->select();
        $selectCSP
            ->from(
                ['main_table' => $this->getTable('tax/tax_calculation_rate')],
                ['country' => 'tax_country_id', 'region_id' => 'tax_region_id', 'postcode' => 'tax_postcode'],
            )
            ->joinInner(
                ['calc_table' => $this->getTable('tax/tax_calculation')],
                implode(' AND ', $calcJoinConditions),
                ['product_class' => 'calc_table.product_tax_class_id'],
            )
            ->joinLeft(
                ['state_table' => $this->getTable('directory/country_region')],
                'state_table.region_id = main_table.tax_region_id',
                ['region_code' => 'state_table.code'],
            )
            ->distinct(true);

        $csp = $adapter->fetchAll($selectCSP);

        $result = [];
        foreach ($csp as $one) {
            $request = new Varien_Object();
            $request->setCountryId($one['country'])
                ->setRegionId($one['region_id'])
                ->setPostcode($one['postcode'])
                ->setCustomerClassId($customerTaxClassId)
                ->setProductClassId($one['product_class']);

            $rate = $this->getRate($request);
            if ($rate) {
                $row = [
                    'value'         => $rate / 100,
                    'country'       => $one['country'],
                    'state'         => $one['region_code'],
                    'postcode'      => $one['postcode'],
                    'product_class' => $one['product_class'],
                ];

                $result[] = $row;
            }
        }

        return $result;
    }
}
