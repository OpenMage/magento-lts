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
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Tax Calculation Resource Model
 *
 * @category    Mage
 * @package     Mage_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Resource_Calculation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Rates cache
     *
     * @var unknown
     */
    protected $_ratesCache              = array();

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement    = false;

    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_setMainTable('tax/tax_calculation');
    }

    /**
     * Delete calculation settings by rule id
     *
     * @param int $ruleId
     * @return $this
     */
    public function deleteByRuleId($ruleId)
    {
        $conn = $this->_getWriteAdapter();
        $where = $conn->quoteInto('tax_calculation_rule_id = ?', (int)$ruleId);
        $conn->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Retreive distinct calculation
     *
     * @param  string $field
     * @param  int $ruleId
     * @return array
     */
    public function getDistinct($field, $ruleId)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), $field)
            ->where('tax_calculation_rule_id = ?', (int)$ruleId);

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
        return array(
            'process'   => $this->getCalculationProcess($request, $rates),
            'value'     => $this->_calculateRate($rates)
        );
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
     * @param Varien_Object $request
     * @param array $rates
     * @return array
     */
    public function getCalculationProcess($request, $rates = null)
    {
        if (is_null($rates)) {
            $rates = $this->_getRates($request);
        }

        $result = array();
        $row = array();
        $ids = array();
        $currentRate = 0;
        $totalPercent = 0;
        $countedRates = count($rates);
        for ($i = 0; $i < $countedRates; $i++) {
            $rate = $rates[$i];
            $value = (isset($rate['value']) ? $rate['value'] : $rate['percent']) * 1;

            $oneRate = array(
                'code' => $rate['code'],
                'title' => $rate['title'],
                'percent' => $value,
                'position' => $rate['position'],
                'priority' => $rate['priority'],
            );
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
            if (isset($rates[$i + 1]['tax_calculation_rule_id'])) {
                $ruleId = $rate['tax_calculation_rule_id'];
            }
            $priority = $rate['priority'];
            $ids[] = $rate['code'];

            if (isset($rates[$i + 1]['tax_calculation_rule_id'])) {
                while (isset($rates[$i + 1]) && $rates[$i + 1]['tax_calculation_rule_id'] == $ruleId) {
                    $i++;
                }
            }

            $currentRate += $value;

            if (!isset($rates[$i + 1]) || $rates[$i + 1]['priority'] != $priority
                || (isset($rates[$i + 1]['process']) && $rates[$i + 1]['process'] != $rate['process'])
            ) {
                if (!empty($rates[$i]['calculate_subtotal'])) {
                    $row['percent'] = $currentRate;
                    $totalPercent += $currentRate;
                } else {
                    $row['percent'] = $this->_collectPercent($totalPercent, $currentRate);
                    $totalPercent += $row['percent'];
                }
                $row['id'] = implode('', $ids);
                $result[] = $row;
                $row = array();
                $ids = array();

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
        $strlen = strlen($postcode);
        if ($strlen > $len) {
            $postcode = substr($postcode, 0, $len);
            $strlen = $len;
        }

        $strArr = array((string)$postcode, $postcode . '*');
        if ($strlen > 1) {
            for ($i = 1; $i < $strlen; $i++) {
                $strArr[] = sprintf('%s*', substr($postcode, 0, - $i));
            }
        }

        return $strArr;
    }

    /**
     * Returns tax rates for request - either pereforms SELECT from DB, or returns already cached result
     * Notice that productClassId due to optimization can be array of ids
     *
     * @param Varien_Object $request
     * @return array
     */
    protected function _getRates($request)
    {
        // Extract params that influence our SELECT statement and use them to create cache key
        $storeId = Mage::app()->getStore($request->getStore())->getId();
        $customerClassId = $request->getCustomerClassId();
        $countryId = $request->getCountryId();
        $regionId = $request->getRegionId();
        $postcode = trim($request->getPostcode());

        // Process productClassId as it can be array or usual value. Form best key for cache.
        $productClassId = $request->getProductClassId();
        $ids = is_array($productClassId) ? $productClassId : array($productClassId);
        foreach ($ids as $key => $val) {
            $ids[$key] = (int) $val; // Make it integer for equal cache keys even in case of null/false/0 values
        }
        $ids = array_unique($ids);
        sort($ids);
        $productClassKey = implode(',', $ids);

        // Form cache key and either get data from cache or from DB
        $cacheKey = implode('|', array($storeId, $customerClassId, $productClassKey, $countryId, $regionId, $postcode));

        if (!isset($this->_ratesCache[$cacheKey])) {
            // Make SELECT and get data
            $select = $this->_getReadAdapter()->select();
            $select
                ->from(array('main_table' => $this->getMainTable()),
                array('tax_calculation_rate_id',
                      'tax_calculation_rule_id',
                      'customer_tax_class_id',
                      'product_tax_class_id'
                    )
                )
                ->where('customer_tax_class_id = ?', (int)$customerClassId);
            if ($productClassId) {
                $select->where('product_tax_class_id IN (?)', $productClassId);
            }
            $ifnullTitleValue = $this->_getReadAdapter()->getCheckSql(
                'title_table.value IS NULL',
                'rate.code',
                'title_table.value'
            );
            $ruleTableAliasName = $this->_getReadAdapter()->quoteIdentifier('rule.tax_calculation_rule_id');
            $select
                ->join(
                    array('rule' => $this->getTable('tax/tax_calculation_rule')),
                    $ruleTableAliasName . ' = main_table.tax_calculation_rule_id',
                    array('rule.priority', 'rule.position', 'rule.calculate_subtotal'))
                ->join(
                    array('rate' => $this->getTable('tax/tax_calculation_rate')),
                    'rate.tax_calculation_rate_id = main_table.tax_calculation_rate_id',
                    array(
                        'value' => 'rate.rate',
                        'rate.tax_country_id',
                        'rate.tax_region_id',
                        'rate.tax_postcode',
                        'rate.tax_calculation_rate_id',
                        'rate.code'
                ))
                ->joinLeft(
                    array('title_table' => $this->getTable('tax/tax_calculation_rate_title')),
                   "rate.tax_calculation_rate_id = title_table.tax_calculation_rate_id "
                   . "AND title_table.store_id = '{$storeId}'",
                    array('title' => $ifnullTitleValue))
                ->where('rate.tax_country_id = ?', $countryId)
                ->where("rate.tax_region_id IN(?)", array(0, (int)$regionId));
            $postcodeIsNumeric = is_numeric($postcode);
            $postcodeIsRange = false;
            if (is_string($postcode) && preg_match('/^(.+)-(.+)$/', $postcode, $matches)) {
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
                    ->where("rate.tax_postcode IS NULL OR rate.tax_postcode IN('*', '', ?)",
                        $postcodeIsRange ? $postcode : $this->_createSearchPostCodeTemplates($postcode));
                if ($postcodeIsNumeric) {
                    $selectClone
                        ->where('? BETWEEN rate.zip_from AND rate.zip_to', $postcode);
                } else if ($postcodeIsRange) {
                    $selectClone->where('rate.zip_from >= ?', $zipFrom)
                        ->where('rate.zip_to <= ?', $zipTo);
                }
            }

            /**
             * @see ZF-7592 issue http://framework.zend.com/issues/browse/ZF-7592
             */
            if ($postcodeIsNumeric || $postcodeIsRange) {
                $select = $this->_getReadAdapter()->select()->union(
                    array(
                        '(' . $select . ')',
                        '(' . $selectClone . ')'
                    )
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
            ->from(array('rate' => $this->getTable('tax/tax_calculation_rate')), array('tax_calculation_rate_id'))
            ->where('rate.tax_country_id = ?', $countryId)
            ->where("rate.tax_region_id IN(?)", array(0, (int)$regionId));

        $expr = $this->_getWriteAdapter()->getCheckSql(
            'zip_is_range is NULL',
            $this->_getWriteAdapter()->quoteInto(
                "rate.tax_postcode IS NULL OR rate.tax_postcode IN('*', '', ?)",
                $this->_createSearchPostCodeTemplates($postcode)
            ),
            $this->_getWriteAdapter()->quoteInto('? BETWEEN rate.zip_from AND rate.zip_to', $postcode)
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
        for ($i = 0; $i < $countedRates; $i++) {
            $rate       = $rates[$i];
            $rule       = $rate['tax_calculation_rule_id'];
            $value      = $rate['value'];
            $priority   = $rate['priority'];

            while (isset($rates[$i + 1]) && $rates[$i + 1]['tax_calculation_rule_id'] == $rule) {
                $i++;
            }

            $currentRate += $value;

            if (!isset($rates[$i + 1]) || $rates[$i + 1]['priority'] != $priority) {
                if (!empty($rates[$i]['calculate_subtotal'])) {
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
     */
    public function getRateIds($request)
    {
        $result = array();
        $rates  = $this->_getRates($request);
        $countedRates = count($rates);
        for ($i = 0; $i < $countedRates; $i++) {
            $rate = $rates[$i];
            $rule = $rate['tax_calculation_rule_id'];
            $result[] = $rate['tax_calculation_rate_id'];
            while (isset($rates[$i + 1]) && $rates[$i + 1]['tax_calculation_rule_id'] == $rule) {
                $i++;
            }
        }

        return $result;
    }

    /**
     * Retrieve rates by customer tax class
     *
     * @param int $customerTaxClassId
     * @param int $productTaxClassId
     * @return array
     */
    public function getRatesByCustomerTaxClass($customerTaxClass, $productTaxClass = null)
    {
        $adapter = $this->_getReadAdapter();
        $customerTaxClassId = (int)$customerTaxClass;
        $calcJoinConditions = array(
            'calc_table.tax_calculation_rate_id = main_table.tax_calculation_rate_id',
            $adapter->quoteInto('calc_table.customer_tax_class_id = ?', $customerTaxClassId),

        );
        if ($productTaxClass !== null) {
            $productTaxClassId = (int)$productTaxClass;
            $calcJoinConditions[] = $adapter->quoteInto('calc_table.product_tax_class_id = ?', $productTaxClassId);
        }

        $selectCSP = $adapter->select();
        $selectCSP
            ->from(
                array('main_table' => $this->getTable('tax/tax_calculation_rate')),
                array('country' => 'tax_country_id', 'region_id' => 'tax_region_id', 'postcode' => 'tax_postcode'))
            ->joinInner(
                array('calc_table' => $this->getTable('tax/tax_calculation')),
                implode(' AND ', $calcJoinConditions),
                array('product_class' => 'calc_table.product_tax_class_id'))
            ->joinLeft(
                array('state_table' => $this->getTable('directory/country_region')),
                'state_table.region_id = main_table.tax_region_id',
                array('region_code' => 'state_table.code'))
            ->distinct(true);

        $CSP = $adapter->fetchAll($selectCSP);

        $result = array();
        foreach ($CSP as $one) {
            $request = new Varien_Object();
            $request->setCountryId($one['country'])
                ->setRegionId($one['region_id'])
                ->setPostcode($one['postcode'])
                ->setCustomerClassId($customerTaxClassId)
                ->setProductClassId($one['product_class']);

            $rate = $this->getRate($request);
            if ($rate) {
                $row = array(
                    'value'         => $rate/100,
                    'country'       => $one['country'],
                    'state'         => $one['region_code'],
                    'postcode'      => $one['postcode'],
                    'product_class' => $one['product_class'],
                );

                $result[] = $row;
            }
        }

        return $result;
    }
}
