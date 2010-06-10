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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Index_Model_Mysql4_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('index/event');
    }

    /**
     * Add filter by entity
     *
     * @param   string | array $entity
     * @return  Mage_Index_Model_Mysql4_Event_Collection
     */
    public function addEntityFilter($entity)
    {
        if (is_array($entity) && !empty($entity)) {
            $this->getSelect()->where('entity IN (?)', $entity);
        } else {
            $this->getSelect()->where('entity = ?', $entity);
        }
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param   string | array $type
     * @return  Mage_Index_Model_Mysql4_Event_Collection
     */
    public function addTypeFilter($type)
    {
        if (is_array($type) && !empty($type)) {
            $this->getSelect()->where('type IN (?)', $type);
        } else {
            $this->getSelect()->where('type = ?', $type);
        }
        return $this;
    }

    /**
     * Add filter by process and status to events collection
     *
     * @param   $process
     * @param   $status
     * @return  Mage_Index_Model_Mysql4_Event_Collection
     */
    public function addProcessFilter($process, $status=null)
    {
        $this->_joinProcessEventTable();
        if ($process instanceof Mage_Index_Model_Process) {
            $this->getSelect()->where('process_event.process_id = ?', $process->getId());
        } elseif (is_array($process) && !empty($process)) {
            $this->getSelect()->where('process_event.process_id IN (?)', $process);
        } else {
            $this->getSelect()->where('process_event.process_id = ?', $process);
        }

        if ($status !== null) {
            $this->getSelect()->where('process_event.status = ?', $status);
        }
        return $this;
    }

    /**
     * Join index_process_event table to event table
     *
     * @return Mage_Index_Model_Mysql4_Event_Collection
     */
    protected function _joinProcessEventTable()
    {
        if (!$this->getFlag('process_event_table_joined')) {
            $this->getSelect()->join(array('process_event' => $this->getTable('index/process_event')),
                'process_event.event_id=main_table.event_id',
                array('process_event_status' => 'status')
            );
            $this->setFlag('process_event_table_joined', true);
        }
        return $this;
    }

    /**
     * Reset collection state
     *
     * @return Mage_Index_Model_Mysql4_Event_Collection
     */
    public function reset()
    {
        $this->_totalRecords = null;
        $this->_data = null;
        $this->_isCollectionLoaded = false;
        return $this;
    }
}
