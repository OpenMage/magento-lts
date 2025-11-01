<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Index Process Resource Model
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Process extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('index/process', 'process_id');
    }

    /**
     * Update process/event association row status
     *
     * @param int $processId
     * @param int $eventId
     * @param string $status
     * @return $this
     */
    public function updateEventStatus($processId, $eventId, $status)
    {
        $adapter = $this->_getWriteAdapter();
        $condition = [
            'process_id = ?' => $processId,
            'event_id = ?'   => $eventId,
        ];
        $adapter->update($this->getTable('index/process_event'), ['status' => $status], $condition);
        return $this;
    }

    /**
     * Register process end
     *
     * @return $this
     */
    public function endProcess(Mage_Index_Model_Process $process)
    {
        $data = [
            'status'    => Mage_Index_Model_Process::STATUS_PENDING,
            'ended_at'  => $this->formatDate(time()),
        ];
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process start
     *
     * @return $this
     */
    public function startProcess(Mage_Index_Model_Process $process)
    {
        $data = [
            'status'        => Mage_Index_Model_Process::STATUS_RUNNING,
            'started_at'    => $this->formatDate(time()),
        ];
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Register process fail
     *
     * @return $this
     */
    public function failProcess(Mage_Index_Model_Process $process)
    {
        $data = [
            'status'   => Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX,
            'ended_at' => $this->formatDate(time()),
        ];
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Update process status field
     *
     * @param Mage_Index_Model_Process $process
     * @param string $status
     * @return $this
     */
    public function updateStatus($process, $status)
    {
        $data = ['status' => $status];
        $this->_updateProcessData($process->getId(), $data);
        return $this;
    }

    /**
     * Updates process data
     * @param int $processId
     * @param array $data
     * @return $this
     */
    protected function _updateProcessData($processId, $data)
    {
        $bind = ['process_id=?' => $processId];
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $bind);

        return $this;
    }

    /**
     * Update process start date
     *
     * @return $this
     */
    public function updateProcessStartDate(Mage_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), ['started_at' => $this->formatDate(time())]);
        return $this;
    }

    /**
     * Update process end date
     *
     * @return $this
     */
    public function updateProcessEndDate(Mage_Index_Model_Process $process)
    {
        $this->_updateProcessData($process->getId(), ['ended_at' => $this->formatDate(time())]);
        return $this;
    }

    /**
     * Whether transaction is already started
     *
     * @return bool
     */
    public function isInTransaction()
    {
        return $this->_getWriteAdapter()->getTransactionLevel() > 0;
    }
}
