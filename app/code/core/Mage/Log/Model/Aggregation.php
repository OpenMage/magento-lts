<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

use Carbon\Carbon;

/**
 * Log Aggregation Model
 *
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Aggregation _getResource()
 * @method Mage_Log_Model_Resource_Aggregation getResource()
 */
class Mage_Log_Model_Aggregation extends Mage_Core_Model_Abstract
{
    /**
     * Last record data
     *
     * @var false|int|string
     */
    protected $_lastRecord;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('log/aggregation');
    }

    /**
     * Run action
     *
     * @throws Mage_Core_Exception
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
     * @param  int                 $store
     * @return mixed
     * @throws Mage_Core_Exception
     */
    private function _process($store)
    {
        $lastDateRecord = null;
        $start          = $this->_lastRecord;
        $end            = Carbon::now()->getTimestamp();
        $date           = $start;

        while ($date < $end) {
            $toDate = $date + 3600;
            $counts = $this->_getCounts($this->_date($date), $this->_date($toDate), $store);
            $data = [
                'store_id' => $store,
                'visitor_count' => $counts['visitors'],
                'customer_count' => $counts['customers'],
                'add_date' => $this->_date($date),
            ];

            if ($counts['visitors'] || $counts['customers']) {
                $this->_save($data, $this->_date($date), $this->_date($toDate));
            }

            $lastDateRecord = $date;
            $date = $toDate;
        }

        return $lastDateRecord;
    }

    /**
     * Save log data
     *
     * @param  array               $data
     * @param  string              $from
     * @param  string              $to
     * @throws Mage_Core_Exception
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
     * @param  string              $id
     * @param  array               $data
     * @throws Mage_Core_Exception
     */
    private function _update($id, $data)
    {
        $this->_getResource()->saveLog($data, $id);
    }

    /**
     * @param  array               $data
     * @throws Mage_Core_Exception
     */
    private function _insert($data)
    {
        $this->_getResource()->saveLog($data);
    }

    /**
     * @param  string              $from
     * @param  string              $to
     * @param  int                 $store
     * @return array
     * @throws Mage_Core_Exception
     */
    private function _getCounts($from, $to, $store)
    {
        return $this->_getResource()->getCounts($from, $to, $store);
    }

    /**
     * @return int|string
     * @throws Mage_Core_Exception
     */
    public function getLastRecordDate()
    {
        $result = $this->_getResource()->getLastRecordDate();
        if (!$result) {
            return $this->_date(Carbon::parse('now - 2 months')->getTimestamp());
        }

        return $result;
    }

    /**
     * @param  int|string $in
     * @return string
     */
    private function _date($in)
    {
        $out = $in;
        if (is_numeric($in)) {
            return Carbon::createFromTimestamp($in)->format(Varien_Date::DATETIME_PHP_FORMAT);
        }

        return $out;
    }

    /**
     * @param  int|string $in
     * @return int
     */
    private function _timestamp($in)
    {
        $out = $in;
        if (!is_numeric($in)) {
            return Carbon::parse($in)->getTimestamp();
        }

        return $out;
    }

    /**
     * @param  int|string $in
     * @return string
     */
    private function _round($in)
    {
        return Carbon::createFromTimestamp($this->_timestamp($in))->format('Y-m-d H:00:00');
    }
}
