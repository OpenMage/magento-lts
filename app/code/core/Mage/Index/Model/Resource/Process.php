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
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index Process Resource Model
 *
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'event_id = ?'   => $eventId
        ];
        $adapter->update($this->getTable('index/process_event'), ['status' => $status], $condition);
        return $this;
    }

    /**
     * Register process end
     *
     * @param Mage_Index_Model_Process $process
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
     * @param Mage_Index_Model_Process $process
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
     * @param Mage_Index_Model_Process $process
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
     * @param Mage_Index_Model_Process $process
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
     * @param Mage_Index_Model_Process $process
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
