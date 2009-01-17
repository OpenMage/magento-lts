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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Log_Model_Mysql4_Aggregation extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('log/summary_table', 'log_summary_id');
    }

    public function getLastRecordDate()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('summary_table'), array('date'=>'MAX(add_date)'));

        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function getCounts($from, $to, $store)
    {
        $result = array('customers'=>0, 'visitors'=>0);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('customer'), 'visitor_id')
            ->where('login_at >= ?', $from)
            ->where('login_at <= ?', $to);
        if ($store) {
            $select->where('store_id = ?', $store);
        }

        $customers = $this->_getReadAdapter()->fetchCol($select);
        $result['customers'] = count($customers);


        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('visitor'), 'COUNT(*)')
            ->where('first_visit_at >= ?', $from)
            ->where('first_visit_at <= ?', $to);

        if ($store) {
            $select->where('store_id = ?', $store);
        }
        if ($result['customers']) {
            $select->where('visitor_id NOT IN(?)', $customers);
        }

        $result['visitors'] = $this->_getReadAdapter()->fetchOne($select);


        return $result;
    }

    public function saveLog($data, $id = null)
    {
        if (is_null($id)) {
            $this->_getWriteAdapter()->insert($this->getTable('summary_table'), $data);
        } else {
            $condition = $this->_getWriteAdapter()->quoteInto('summary_id = ?', $id);
            $this->_getWriteAdapter()->update($this->getTable('summary_table'), $data, $condition);
        }
    }

    public function removeEmpty($date)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('add_date < ? AND customer_count = 0 AND visitor_count = 0', $date);
        $this->_getWriteAdapter()->delete($this->getTable('summary_table'), $condition);
    }

    public function getLogId($from, $to)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('summary_table'), 'summary_id')
            ->where('add_date >= ?', $from)
            ->where('add_date <= ?', $to);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}