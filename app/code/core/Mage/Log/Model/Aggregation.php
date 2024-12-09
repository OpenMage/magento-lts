<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Aggregation Model
 *
 * @category   Mage
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Aggregation getResource()
 * @method Mage_Log_Model_Resource_Aggregation _getResource()
 */
class Mage_Log_Model_Aggregation extends Mage_Core_Model_Abstract
{
    /**
     * Last record data
     *
     * @var string
     */
    protected $_lastRecord;

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('log/aggregation');
    }

    /**
     * Run action
     */
    public function run()
    {
        $this->_lastRecord = $this->_timestamp($this->_round($this->getLastRecordDate()));
        foreach (Mage::app()->getStores(false) as $store) {
            $this->_process($store->getId());
        }
    }

    /**
     * Process
     *
     * @param  int $store
     * @return mixed
     */
    private function _process($store)
    {
        $lastDateRecord = null;
        $start          = $this->_lastRecord;
        $end            = time();
        $date           = $start;

        while ($date < $end) {
            $to = $date + 3600;
            $counts = $this->_getCounts($this->_date($date), $this->_date($to), $store);
            $data = [
                'store_id' => $store,
                'visitor_count' => $counts['visitors'],
                'customer_count' => $counts['customers'],
                'add_date' => $this->_date($date)
            ];

            if ($counts['visitors'] || $counts['customers']) {
                $this->_save($data, $this->_date($date), $this->_date($to));
            }

            $lastDateRecord = $date;
            $date = $to;
        }
        return $lastDateRecord;
    }

    /**
     * Save log data
     *
     * @param  array $data
     * @param  string $from
     * @param  string $to
     */
    private function _save($data, $from, $to)
    {
        if ($logId = $this->_getResource()->getLogId($from, $to)) {
            $this->_update($logId, $data);
        } else {
            $this->_insert($data);
        }
    }

    /**
     * @param int $id
     * @param array $data
     */
    private function _update($id, $data)
    {
        return $this->_getResource()->saveLog($data, $id);
    }

    /**
     * @param array $data
     */
    private function _insert($data)
    {
        return $this->_getResource()->saveLog($data);
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $store
     * @return array
     */
    private function _getCounts($from, $to, $store)
    {
        return $this->_getResource()->getCounts($from, $to, $store);
    }

    /**
     * @return false|string
     */
    public function getLastRecordDate()
    {
        $result = $this->_getResource()->getLastRecordDate();
        if (!$result) {
            $result = $this->_date(strtotime('now - 2 months'));
        }

        return $result;
    }

    /**
     * @param string|int $in
     * @param null $offset deprecated
     * @return false|string
     */
    private function _date($in, $offset = null)
    {
        $out = $in;
        if (is_numeric($in)) {
            $out = date(Varien_Date::DATETIME_PHP_FORMAT, $in);
        }
        return $out;
    }

    /**
     * @param string|int $in
     * @param null $offset deprecated
     * @return false|int
     */
    private function _timestamp($in, $offset = null)
    {
        $out = $in;
        if (!is_numeric($in)) {
            $out = strtotime($in);
        }
        return $out;
    }

    /**
     * @param  string|int $in
     * @return string
     */
    private function _round($in)
    {
        return date('Y-m-d H:00:00', $this->_timestamp($in));
    }
}
