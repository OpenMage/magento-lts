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
 * Index Process Collection
 *
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
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
                    0,
                    'e.events'
                )]
            );
        return $this;
    }
}
