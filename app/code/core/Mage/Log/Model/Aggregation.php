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
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Aggregation Model
 *
 * @method Mage_Log_Model_Resource_Aggregation getResource()
 * @method Mage_Log_Model_Resource_Aggregation _getResource()
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * Remove empty records before $lastDate
     *
     * @param  string $lastDate
     * @return void
     */
    private function _removeEmpty($lastDate)
    {
        return $this->_getResource()->removeEmpty($lastDate);
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
            $data = array(
                'store_id'=>$store,
                'visitor_count'=>$counts['visitors'],
                'customer_count'=>$counts['customers'],
                'add_date'=>$this->_date($date)
                );

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

    private function _update($id, $data)
    {
        return $this->_getResource()->saveLog($data, $id);
    }

    private function _insert($data)
    {
        return $this->_getResource()->saveLog($data);
    }

    private function _getCounts($from, $to, $store)
    {
        return $this->_getResource()->getCounts($from, $to, $store);
    }

    public function getLastRecordDate()
    {
        $result = $this->_getResource()->getLastRecordDate();
        if (!$result)
            $result = $this->_date(strtotime('now - 2 months'));

        return $result;
    }

    private function _date($in, $offset = null)
    {
        $out = $in;
        if (is_numeric($in))
            $out = date("Y-m-d H:i:s", $in);
        return $out;
    }

    private function _timestamp($in, $offset = null)
    {
        $out = $in;
        if (!is_numeric($in))
            $out = strtotime($in);
        return $out;
    }

    /**
     * @param  $in
     * @return string
     */
    private function _round($in)
    {
        return date("Y-m-d H:00:00", $this->_timestamp($in));
    }
}
