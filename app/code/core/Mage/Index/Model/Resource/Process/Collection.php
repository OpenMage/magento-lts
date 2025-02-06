<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Index
 */

/**
 * Index Process Collection
 *
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Model_Resource_Process_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventObject = 'process_collection';

    /**
     * @var string
     */
    protected $_eventPrefix = 'process_collection';

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('index/process');
    }

    /**
     * Add count of unprocessed events to process collection
     *
     * @return $this
     */
    public function addEventsStats()
    {
        $countsSelect = $this->getConnection()
            ->select()
            ->from($this->getTable('index/process_event'), ['process_id', 'events' => 'COUNT(*)'])
            ->where('status=?', Mage_Index_Model_Process::EVENT_STATUS_NEW)
            ->group('process_id');
        $this->getSelect()
            ->joinLeft(
                ['e' => $countsSelect],
                'e.process_id=main_table.process_id',
                ['events' => $this->getConnection()->getCheckSql(
                    $this->getConnection()->prepareSqlCondition('e.events', ['null' => null]),
                    '0',
                    'e.events',
                )],
            );
        return $this;
    }
}
