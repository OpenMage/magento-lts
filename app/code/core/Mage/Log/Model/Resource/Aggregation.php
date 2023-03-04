<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log aggregation resource model
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Resource_Aggregation extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('log/summary_table', 'log_summary_id');
    }

    /**
     * Retrieve last added record
     *
     * @return string
     */
    public function getLastRecordDate()
    {
        $adapter    = $this->_getReadAdapter();
        $select     = $adapter->select()
            ->from(
                $this->getTable('log/summary_table'),
                [$adapter->quoteIdentifier('date') => 'MAX(add_date)']
            );

        return $adapter->fetchOne($select);
    }

    /**
     * Retrieve count of visitors, customers
     *
     * @param string $from
     * @param string $to
     * @param int $store
     * @return array
     */
    public function getCounts($from, $to, $store)
    {
        $adapter    = $this->_getReadAdapter();
        $result     = ['customers' => 0, 'visitors' => 0];
        $select     = $adapter->select()
            ->from($this->getTable('log/customer'), 'visitor_id')
            ->where('login_at >= ?', $from)
            ->where('login_at <= ?', $to);
        if ($store) {
            $select->where('store_id = ?', $store);
        }

        $customers = $adapter->fetchCol($select);
        $result['customers'] = count($customers);

        $select = $adapter->select();
        $select->from($this->getTable('log/visitor'), 'COUNT(*)')
            ->where('first_visit_at >= ?', $from)
            ->where('first_visit_at <= ?', $to);

        if ($store) {
            $select->where('store_id = ?', $store);
        }
        if ($result['customers']) {
            $select->where('visitor_id NOT IN(?)', $customers);
        }

        $result['visitors'] = $adapter->fetchOne($select);

        return $result;
    }

    /**
     * Save log
     *
     * @param array $data
     * @param int $id
     */
    public function saveLog($data, $id = null)
    {
        $adapter = $this->_getWriteAdapter();
        if (is_null($id)) {
            $adapter->insert($this->getTable('log/summary_table'), $data);
        } else {
            $condition = $adapter->quoteInto('summary_id = ?', $id);
            $adapter->update($this->getTable('log/summary_table'), $data, $condition);
        }
    }

    /**
     * Remove empty records
     *
     * @param string $date
     */
    public function removeEmpty($date)
    {
        $adapter    = $this->_getWriteAdapter();
        $condition  = [
            'add_date < ?' => $date,
            'customer_count = 0',
            'visitor_count = 0'
        ];
        $adapter->delete($this->getTable('log/summary_table'), $condition);
    }

    /**
     * Retrieve log id
     *
     * @param string $from
     * @param string $to
     * @return string
     */
    public function getLogId($from, $to)
    {
        $adapter    = $this->_getReadAdapter();
        $select     = $adapter->select()
            ->from($this->getTable('log/summary_table'), 'summary_id')
            ->where('add_date >= ?', $from)
            ->where('add_date <= ?', $to);

        return $adapter->fetchOne($select);
    }
}
