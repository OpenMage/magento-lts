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
 * Index Event Collection
 *
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Event_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('index/event');
    }

    /**
     * Add filter by entity
     *
     * @param string | array $entity
     * @return $this
     */
    public function addEntityFilter($entity)
    {
        if (is_array($entity) && !empty($entity)) {
            $this->addFieldToFilter('entity', ['in' => $entity]);
        } else {
            $this->addFieldToFilter('entity', $entity);
        }
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string | array $type
     * @return $this
     */
    public function addTypeFilter($type)
    {
        if (is_array($type) && !empty($type)) {
            $this->addFieldToFilter('type', ['in' => $type]);
        } else {
            $this->addFieldToFilter('type', $type);
        }
        return $this;
    }

    /**
     * Add filter by process and status to events collection
     *
     * @param int|array|Mage_Index_Model_Process $process
     * @param string $status
     * @return $this
     */
    public function addProcessFilter($process, $status = null)
    {
        $this->_joinProcessEventTable();
        if ($process instanceof Mage_Index_Model_Process) {
            $this->addFieldToFilter('process_event.process_id', $process->getId());
        } elseif (is_array($process) && !empty($process)) {
            $this->addFieldToFilter('process_event.process_id', ['in' => $process]);
        } else {
            $this->addFieldToFilter('process_event.process_id', $process);
        }

        if ($status !== null) {
            if (is_array($status) && !empty($status)) {
                $this->addFieldToFilter('process_event.status', ['in' => $status]);
            } else {
                $this->addFieldToFilter('process_event.status', $status);
            }
        }
        return $this;
    }

    /**
     * Join index_process_event table to event table
     *
     * @return $this
     */
    protected function _joinProcessEventTable()
    {
        if (!$this->getFlag('process_event_table_joined')) {
            $this->getSelect()->join(
                ['process_event' => $this->getTable('index/process_event')],
                'process_event.event_id=main_table.event_id',
                ['process_event_status' => 'status']
            );
            $this->setFlag('process_event_table_joined', true);
        }
        return $this;
    }

    /**
     * Reset collection state
     *
     * @return $this
     */
    public function reset()
    {
        $this->_totalRecords = null;
        $this->_data = null;
        $this->_isCollectionLoaded = false;
        $this->_items = [];
        return $this;
    }
}
