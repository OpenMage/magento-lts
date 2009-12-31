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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Index_Model_Mysql4_Process extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize  table and table pk
     */
    protected function _construct()
    {
        $this->_init('index/process', 'process_id');
    }

    /**
     * Update process/event association row status
     *
     * @param   int $processId
     * @param   int $eventId
     * @param   string $status
     * @return  Mage_Index_Model_Mysql4_Process
     */
    public function updateEventStatus($processId, $eventId, $status)
    {
        $adapter = $this->_getWriteAdapter();
        $condition = $adapter->quoteInto('process_id = ? AND ', $processId)
            . $adapter->quoteInto('event_id = ?', $eventId);
        $adapter->update($this->getTable('index/process_event'), array('status' => $status), $condition);
        return $this;
    }

    /**
     * Register process end
     *
     * @param   Mage_Index_Model_Process $process
     * @return  Mage_Index_Model_Mysql4_Process
     */
    public function endProcess(Mage_Index_Model_Process $process)
    {
        $data = array(
            'status'    => Mage_Index_Model_Process::STATUS_PENDING,
            'ended_at'  =>$this->formatDate(time()),
        );
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            $data,
            $this->_getWriteAdapter()->quoteInto('process_id=?', $process->getId())
        );
        return $this;
    }

    /**
     * Register process start
     *
     * @param   Mage_Index_Model_Process $process
     * @return  Mage_Index_Model_Mysql4_Process
     */
    public function startProcess(Mage_Index_Model_Process $process)
    {
        $data = array(
            'status'    => Mage_Index_Model_Process::STATUS_RUNNING,
            'started_at'=>$this->formatDate(time()),
        );
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            $data,
            $this->_getWriteAdapter()->quoteInto('process_id=?', $process->getId())
        );
        return $this;
    }

    /**
     * Update process status field
     *
     * @param Mage_Index_Model_Process
     * @param string status
     * @return Mage_Index_Model_Mysql4_Process
     */
    public function updateStatus($process, $status)
    {
        $data = array('status' => $status);
        $this->_getWriteAdapter()->update(
            $this->getMainTable(),
            $data,
            $this->_getWriteAdapter()->quoteInto('process_id=?', $process->getId())
        );
        return $this;
    }
}
