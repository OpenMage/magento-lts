<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Index Event Resource Model
 *
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('index/event', 'event_id');
    }

    /**
     * Check if semilar event exist before start saving data
     *
     * @param Mage_Index_Model_Event $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * Check if event already exist and merge previous data
         */
        if (!$object->getId()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('type=?', $object->getType())
                ->where('entity=?', $object->getEntity());
            if ($object->hasEntityPk()) {
                $select->where('entity_pk=?', $object->getEntityPk());
            }
            $data = $this->_getWriteAdapter()->fetchRow($select);
            if ($data) {
                $object->mergePreviousData($data);
            }
        }
        $object->cleanNewData();
        return parent::_beforeSave($object);
    }

    /**
     * Save assigned processes
     *
     * @param Mage_Index_Model_Event $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $processIds = $object->getProcessIds();
        if (is_array($processIds)) {
            $processTable = $this->getTable('index/process_event');
            if (empty($processIds)) {
                $this->_getWriteAdapter()->delete($processTable);
            } else {
                foreach ($processIds as $processId => $processStatus) {
                    if (is_null($processStatus) || $processStatus == Mage_Index_Model_Process::EVENT_STATUS_DONE) {
                        $this->_getWriteAdapter()->delete($processTable, [
                            'process_id = ?' => $processId,
                            'event_id = ?'   => $object->getId(),
                        ]);
                        continue;
                    }
                    $data = [
                        'process_id' => $processId,
                        'event_id'   => $object->getId(),
                        'status'     => $processStatus
                    ];
                    $this->_getWriteAdapter()->insertOnDuplicate($processTable, $data, ['status']);
                }
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Update status for events of process
     *
     * @param int|array|Mage_Index_Model_Process $process
     * @param string $status
     * @return $this
     */
    public function updateProcessEvents($process, $status = Mage_Index_Model_Process::EVENT_STATUS_DONE)
    {
        $whereCondition = '';
        if ($process instanceof Mage_Index_Model_Process) {
            $whereCondition = ['process_id = ?' => $process->getId()];
        } elseif (is_array($process) && !empty($process)) {
            $whereCondition = ['process_id IN (?)' => $process];
        } elseif (!is_array($whereCondition)) {
            $whereCondition = ['process_id = ?' => $process];
        }
        $this->_getWriteAdapter()->update(
            $this->getTable('index/process_event'),
            ['status' => $status],
            $whereCondition
        );
        return $this;
    }

    /**
     * Retrieve unprocessed events list by specified process
     *
     * @param Mage_Index_Model_Process $process
     * @return array
     */
    public function getUnprocessedEvents($process)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('index/process_event'))
            ->where('process_id = ?', $process->getId())
            ->where('status = ?', Mage_Index_Model_Process::EVENT_STATUS_NEW);

        return $this->_getReadAdapter()->fetchAll($select);
    }
}
