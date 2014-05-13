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
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Indexer strategy
 */
class Mage_Index_Model_Indexer
{
    /**
     * Collection of available processes
     *
     * @var Mage_Index_Model_Resource_Process_Collection
     */
    protected $_processesCollection;

    /**
     * Indexer processes lock flag
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_lockFlag = false;

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Current processing event(s)
     * In array case it should be array(Entity type, Event type)
     *
     * @var null|Mage_Index_Model_Event|array
     */
    protected $_currentEvent = null;

    /**
     * Array of errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Class constructor. Initialize index processes based on configuration
     */
    public function __construct()
    {
        $this->getProcessesCollection();
    }

    /**
     * Get collection of all available processes
     *
     * @return Mage_Index_Model_Resource_Process_Collection
     */
    public function getProcessesCollection()
    {
        if (is_null($this->_processesCollection)) {
            $this->_processesCollection = Mage::getResourceModel('index/process_collection');
        }
        return $this->_processesCollection;
    }

    /**
     * Get index process by specific id
     *
     * @param int $processId
     * @return Mage_Index_Model_Process | false
     */
    public function getProcessById($processId)
    {
        foreach ($this->getProcessesCollection() as $process) {
            if ($process->getId() == $processId) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Get index process by specific code
     *
     * @param string $code
     * @return Mage_Index_Model_Process | false
     */
    public function getProcessByCode($code)
    {
        foreach ($this->getProcessesCollection() as $process) {
            if ($process->getIndexerCode() == $code) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Function returns array of indexer's process with order by sort_order field
     *
     * @param array $codes
     * @return array
     */
    public function getProcessesCollectionByCodes(array $codes)
    {
        $processes = array();
        $this->_errors = array();
        foreach($codes as $code) {
            $process = $this->getProcessByCode($code);
            if (!$process) {
                $this->_errors[] = sprintf('Warning: Unknown indexer with code %s', trim($code));
                continue;
            }
            $processes[$process->getIndexerCode()] = $process;
        }
        return $processes;
    }

    /**
     * Return true if model has errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)count($this->_errors);
    }

    /**
     * Return array of errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Lock indexer actions
     * @deprecated after 1.6.1.0
     *
     * @return Mage_Index_Model_Indexer
     */
    public function lockIndexer()
    {
        $this->_lockFlag = true;
        return $this;
    }

    /**
     * Unlock indexer actions
     * @deprecated after 1.6.1.0
     *
     * @return Mage_Index_Model_Indexer
     */
    public function unlockIndexer()
    {
        $this->_lockFlag = false;
        return $this;
    }

    /**
     * Check if onject actions are locked
     *
     * @deprecated after 1.6.1.0
     * @return bool
     */
    public function isLocked()
    {
        return $this->_lockFlag;
    }

    /**
     * Indexing all pending events.
     * Events set can be limited by event entity and type
     *
     * @param   null | string $entity
     * @param   null | string $type
     * @throws Exception
     * @return  Mage_Index_Model_Indexer
     */
    public function indexEvents($entity=null, $type=null)
    {
        Mage::dispatchEvent('start_index_events' . $this->_getEventTypeName($entity, $type));

        /** @var $resourceModel Mage_Index_Model_Resource_Process */
        $resourceModel = Mage::getResourceSingleton('index/process');

        $allowTableChanges = $this->_allowTableChanges && !$resourceModel->isInTransaction();
        if ($allowTableChanges) {
            $this->_currentEvent = array($entity, $type);
            $this->_changeKeyStatus(false);
        }

        $resourceModel->beginTransaction();
        $this->_allowTableChanges = false;
        try {
            $this->_runAll('indexEvents', array($entity, $type));
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
        if ($allowTableChanges) {
            $this->_allowTableChanges = true;
            $this->_changeKeyStatus(true);
            $this->_currentEvent = null;
        }
        Mage::dispatchEvent('end_index_events' . $this->_getEventTypeName($entity, $type));
        return $this;
    }

    /**
     * Index one event by all processes
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Index_Model_Indexer
     */
    public function indexEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('safeProcessEvent', array($event));
        return $this;
    }

    /**
     * Register event in each indexing process process
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Index_Model_Indexer
     */
    public function registerEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('register', array($event));
        return $this;
    }

    /**
     * Create new event log and register event in all processes
     *
     * @param   Varien_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @param   bool $doSave
     * @return  Mage_Index_Model_Event
     */
    public function logEvent(Varien_Object $entity, $entityType, $eventType, $doSave=true)
    {
        $event = Mage::getModel('index/event')
            ->setEntity($entityType)
            ->setType($eventType)
            ->setDataObject($entity)
            ->setEntityPk($entity->getId());

        $this->registerEvent($event);
        if ($doSave) {
            $event->save();
        }
        return $event;
    }

    /**
     * Create new event log and register event in all processes.
     * Initiate events indexing procedure.
     *
     * @param   Varien_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @throws Exception
     * @return  Mage_Index_Model_Indexer
     */
    public function processEntityAction(Varien_Object $entity, $entityType, $eventType)
    {
        $event = $this->logEvent($entity, $entityType, $eventType, false);
        /**
         * Index and save event just in case if some process matched it
         */
        if ($event->getProcessIds()) {
            Mage::dispatchEvent('start_process_event' . $this->_getEventTypeName($entityType, $eventType));

            /** @var $resourceModel Mage_Index_Model_Resource_Process */
            $resourceModel = Mage::getResourceSingleton('index/process');

            $allowTableChanges = $this->_allowTableChanges && !$resourceModel->isInTransaction();
            if ($allowTableChanges) {
                $this->_currentEvent = $event;
                $this->_changeKeyStatus(false);
            }

            $resourceModel->beginTransaction();
            $this->_allowTableChanges = false;
            try {
                $this->indexEvent($event);
                $resourceModel->commit();
            } catch (Exception $e) {
                $resourceModel->rollBack();
                if ($allowTableChanges) {
                    $this->_allowTableChanges = true;
                    $this->_changeKeyStatus(true);
                    $this->_currentEvent = null;
                }
                throw $e;
            }
            if ($allowTableChanges) {
                $this->_allowTableChanges = true;
                $this->_changeKeyStatus(true);
                $this->_currentEvent = null;
            }
            $event->save();
            Mage::dispatchEvent('end_process_event' . $this->_getEventTypeName($entityType, $eventType));
        }
        return $this;
    }

    /**
     * Run all processes method with parameters
     * Run by depends priority
     * Not recursive call is not implement
     *
     * @param string $method
     * @param array $args
     * @return Mage_Index_Model_Indexer
     */
    protected function _runAll($method, $args)
    {
        $checkLocks = $method != 'register';
        $processed = array();
        foreach ($this->getProcessesCollection() as $process) {
            $code = $process->getIndexerCode();
            if (in_array($code, $processed)) {
                continue;
            }
            $hasLocks = false;

            if ($process->getDepends()) {
                foreach ($process->getDepends() as $processCode) {
                    $dependProcess = $this->getProcessByCode($processCode);
                    if ($dependProcess && !in_array($processCode, $processed)) {
                        if ($checkLocks && $dependProcess->isLocked()) {
                            $hasLocks = true;
                        } else {
                            call_user_func_array(array($dependProcess, $method), $args);
                            if ($checkLocks && $dependProcess->getMode() == Mage_Index_Model_Process::MODE_MANUAL) {
                                $hasLocks = true;
                            } else {
                                $processed[] = $processCode;
                            }
                        }
                    }
                }
            }

            if (!$hasLocks) {
                call_user_func_array(array($process, $method), $args);
                $processed[] = $code;
            }
        }
    }

    /**
     * Enable/Disable keys in index tables
     *
     * @param bool $enable
     * @return Mage_Index_Model_Indexer
     */
    protected function _changeKeyStatus($enable = true)
    {
        $processed = array();
        foreach ($this->getProcessesCollection() as $process) {
            $code = $process->getIndexerCode();
            if (in_array($code, $processed)) {
                continue;
            }

            if ($process->getDepends()) {
                foreach ($process->getDepends() as $processCode) {
                    $dependProcess = $this->getProcessByCode($processCode);
                    if ($dependProcess && !in_array($processCode, $processed)) {
                        if ($this->_changeProcessKeyStatus($dependProcess, $enable)) {
                            $processed[] = $processCode;
                        }
                    }
                }
            }

            if ($this->_changeProcessKeyStatus($process, $enable)) {
                $processed[] = $code;
            }
        }

        return $this;
    }

    /**
     * Check if the event will be processed and disable/enable keys in index tables
     *
     * @param mixed|Mage_Index_Model_Process $process
     * @param bool $enable
     * @return bool
     */
    protected function _changeProcessKeyStatus($process, $enable = true)
    {
        $event = $this->_currentEvent;
        if ($process instanceof Mage_Index_Model_Process
            && $process->getMode() !== Mage_Index_Model_Process::MODE_MANUAL
            && !$process->isLocked()
            && (is_null($event)
                || ($event instanceof Mage_Index_Model_Event && $process->matchEvent($event))
                || (is_array($event) && $process->matchEntityAndType($event[0], $event[1]))
        )) {
            if ($enable) {
                $process->enableIndexerKeys();
            } else {
                $process->disableIndexerKeys();
            }
            return true;
        }
        return false;
    }

    /**
     * Allow DDL operations while indexing
     *
     * @return Mage_Index_Model_Indexer
     */
    public function allowTableChanges()
    {
        $this->_allowTableChanges = true;
        return $this;
    }

    /**
     * Disallow DDL operations while indexing
     *
     * @return Mage_Index_Model_Indexer
     */
    public function disallowTableChanges()
    {
        $this->_allowTableChanges = false;
        return $this;
    }

    /**
     * Get event type name
     *
     * @param null|string $entityType
     * @param null|string $eventType
     * @return string
     */
    protected function _getEventTypeName($entityType = null, $eventType = null)
    {
        $eventName = $entityType . '_' . $eventType;
        $eventName = trim($eventName, '_');
        if (!empty($eventName)) {
            $eventName = '_' . $eventName;
        }
        return $eventName;
    }
}
