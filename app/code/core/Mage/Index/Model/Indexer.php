<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Indexer strategy
 *
 * @package    Mage_Index
 */
class Mage_Index_Model_Indexer
{
    /**
     * Collection of available processes
     *
     * @var null|Mage_Index_Model_Resource_Process_Collection
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
     * @var null|array|Mage_Index_Model_Event
     */
    protected $_currentEvent = null;

    /**
     * Array of errors
     *
     * @var array
     */
    protected $_errors = [];

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
     * @return false|Mage_Index_Model_Process
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
     * @return false|Mage_Index_Model_Process
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
     * @return array
     */
    public function getProcessesCollectionByCodes(array $codes)
    {
        $processes = [];
        $this->_errors = [];
        foreach ($codes as $code) {
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
        return (bool) count($this->_errors);
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
     *
     * @return $this
     * @deprecated after 1.6.1.0
     */
    public function lockIndexer()
    {
        $this->_lockFlag = true;
        return $this;
    }

    /**
     * Unlock indexer actions
     *
     * @return $this
     * @deprecated after 1.6.1.0
     */
    public function unlockIndexer()
    {
        $this->_lockFlag = false;
        return $this;
    }

    /**
     * Check if object actions are locked
     *
     * @return bool
     * @deprecated after 1.6.1.0
     */
    public function isLocked()
    {
        return $this->_lockFlag;
    }

    /**
     * Indexing all pending events.
     * Events set can be limited by event entity and type
     *
     * @param   null|string $entity
     * @param   null|string $type
     * @return  Mage_Index_Model_Indexer
     * @throws Exception
     */
    public function indexEvents($entity = null, $type = null)
    {
        Mage::dispatchEvent('start_index_events' . $this->_getEventTypeName($entity, $type));

        /** @var Mage_Index_Model_Resource_Process $resourceModel */
        $resourceModel = Mage::getResourceSingleton('index/process');

        $allowTableChanges = $this->_allowTableChanges && !$resourceModel->isInTransaction();
        if ($allowTableChanges) {
            $this->_currentEvent = [$entity, $type];
            $this->_changeKeyStatus(false);
        }

        $resourceModel->beginTransaction();
        $this->_allowTableChanges = false;
        try {
            $this->_runAll('indexEvents', [$entity, $type]);
            $resourceModel->commit();
        } catch (Exception $exception) {
            $resourceModel->rollBack();
            throw $exception;
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
     * @return  Mage_Index_Model_Indexer
     */
    public function indexEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('safeProcessEvent', [$event]);
        return $this;
    }

    /**
     * Register event in each indexing process process
     *
     * @return $this
     */
    public function registerEvent(Mage_Index_Model_Event $event)
    {
        $this->_runAll('register', [$event]);
        return $this;
    }

    /**
     * Create new event log and register event in all processes
     *
     * @param   string $entityType
     * @param   string $eventType
     * @param   bool $doSave
     * @return  Mage_Index_Model_Event
     */
    public function logEvent(Varien_Object $entity, $entityType, $eventType, $doSave = true)
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
     * @param   string $entityType
     * @param   string $eventType
     * @throws Exception|Throwable
     */
    public function processEntityAction(Varien_Object $entity, $entityType, $eventType): Mage_Index_Model_Indexer
    {
        $event = $this->logEvent($entity, $entityType, $eventType, false);
        /**
         * Index and save event just in case if some process matched it
         */
        if ($event->getProcessIds()) {
            Mage::dispatchEvent('start_process_event' . $this->_getEventTypeName($entityType, $eventType));

            /** @var Mage_Index_Model_Resource_Process $resourceModel */
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
     */
    protected function _runAll($method, $args)
    {
        $checkLocks = $method != 'register';
        $processed = [];
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
                            call_user_func_array([$dependProcess, $method], $args);
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
                call_user_func_array([$process, $method], $args);
                $processed[] = $code;
            }
        }
    }

    /**
     * Enable/Disable keys in index tables
     *
     * @param bool $enable
     * @return $this
     */
    protected function _changeKeyStatus($enable = true)
    {
        $processed = [];
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
     * @param Mage_Index_Model_Process|mixed $process
     * @param bool $enable
     * @return bool
     */
    protected function _changeProcessKeyStatus($process, $enable = true)
    {
        $event = $this->_currentEvent;
        if ($process instanceof Mage_Index_Model_Process
            && $process->getMode() !== Mage_Index_Model_Process::MODE_MANUAL
            && !$process->isLocked()
            && (
                is_null($event)
                || ($event instanceof Mage_Index_Model_Event && $process->matchEvent($event))
                || (is_array($event) && $process->matchEntityAndType($event[0], $event[1]))
            )
        ) {
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
     * @return $this
     */
    public function allowTableChanges()
    {
        $this->_allowTableChanges = true;
        return $this;
    }

    /**
     * Disallow DDL operations while indexing
     *
     * @return $this
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
