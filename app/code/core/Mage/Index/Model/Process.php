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
 * @category   Mage
 * @package    Mage_Index
 *
 * @method Mage_Index_Model_Resource_Process _getResource()
 * @method Mage_Index_Model_Resource_Process getResource()
 * @method string getIndexCode()
 * @method string getIndexerCode()
 * @method $this setIndexerCode(string $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method string getStartedAt()
 * @method $this setStartedAt(string $value)
 * @method string getEndedAt()
 * @method $this setEndedAt(string $value)
 * @method string getMode()
 * @method $this setMode(string $value)
 * @method bool getForcePartialReindex()
 * @method $this setForcePartialReindex(bool $value)
 */
class Mage_Index_Model_Process extends Mage_Core_Model_Abstract
{
    public const XML_PATH_INDEXER_DATA     = 'global/index/indexer';

    /**
     * Process statuses
     */
    public const STATUS_RUNNING            = 'working';
    public const STATUS_PENDING            = 'pending';
    public const STATUS_REQUIRE_REINDEX    = 'require_reindex';

    /**
     * Process event statuses
     */
    public const EVENT_STATUS_NEW          = 'new';
    public const EVENT_STATUS_DONE         = 'done';
    public const EVENT_STATUS_ERROR        = 'error';
    public const EVENT_STATUS_WORKING      = 'working';

    /**
     * Process modes
     * Process mode allow disable automatic process events processing
     */
    public const MODE_MANUAL              = 'manual';
    public const MODE_REAL_TIME           = 'real_time';
    public const MODE_SCHEDULE            = 'schedule';

    /**
     * Indexer stategy object
     *
     * @var Mage_Index_Model_Indexer_Abstract
     */
    protected $_indexer = null;

    /**
     * Locker Object
     *
     * @var Mage_Index_Model_Lock|null
     */
    protected $_lockInstance = null;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges = true;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('index/process');
    }

    /**
     * Set indexer class name as data namespace for event object
     *
     * @param   Mage_Index_Model_Event $event
     * @return  $this
     */
    protected function _setEventNamespace(Mage_Index_Model_Event $event)
    {
        $namespace = get_class($this->getIndexer());
        $event->setDataNamespace($namespace);
        $event->setProcess($this);
        return $this;
    }

    /**
     * Remove indexer namespace from event
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _resetEventNamespace($event)
    {
        $event->setDataNamespace(null);
        $event->setProcess(null);
        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function register(Mage_Index_Model_Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_setEventNamespace($event);
            $this->getIndexer()->register($event);
            $event->addProcessId($this->getId());
            $this->_resetEventNamespace($event);
            if ($this->getMode() == self::MODE_MANUAL) {
                $this->_getResource()->updateStatus($this, self::STATUS_REQUIRE_REINDEX);
            }
        }
        return $this;
    }

    /**
     * Check if event can be matched by process
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        return $this->getIndexer()->matchEvent($event);
    }

    /**
     * Check if specific entity and action type is matched
     *
     * @param   string $entity
     * @param   string $type
     * @return  bool
     */
    public function matchEntityAndType($entity, $type)
    {
        if ($entity !== null && $type !== null) {
            return $this->getIndexer()->matchEntityAndType($entity, $type);
        }
        return true;
    }

    /**
     * Reindex all data what this process responsible is
     *
     */
    public function reindexAll()
    {
        if ($this->isLocked()) {
            Mage::throwException(Mage::helper('index')->__('%s Index process is working now. Please try run this process later.', $this->getIndexer()->getName()));
        }

        $processStatus = $this->getStatus();

        $this->_getResource()->startProcess($this);
        $this->lock();
        try {
            $eventsCollection = $this->getUnprocessedEventsCollection();

            /** @var Mage_Index_Model_Resource_Event $eventResource */
            $eventResource = Mage::getResourceSingleton('index/event');

            if ($eventsCollection->count() > 0 && $processStatus == self::STATUS_PENDING
                || $this->getForcePartialReindex()
            ) {
                $this->_getResource()->beginTransaction();
                try {
                    $this->_processEventsCollection($eventsCollection, false);
                    $this->_getResource()->commit();
                } catch (Exception $e) {
                    $this->_getResource()->rollBack();
                    throw $e;
                }
            } else {
                //Update existing events since we'll do reindexAll
                $eventResource->updateProcessEvents($this);
                $this->getIndexer()->reindexAll();
            }
            $this->unlock();

            $unprocessedEvents = $eventResource->getUnprocessedEvents($this);
            if ($this->getMode() == self::MODE_MANUAL && (count($unprocessedEvents) > 0)) {
                $this->_getResource()->updateStatus($this, self::STATUS_REQUIRE_REINDEX);
            } else {
                $this->_getResource()->endProcess($this);
            }
        } catch (Exception $e) {
            $this->unlock();
            $this->_getResource()->failProcess($this);
            throw $e;
        }
        Mage::dispatchEvent('after_reindex_process_' . $this->getIndexerCode());
        return $this;
    }

    /**
     * Reindex all data what this process responsible is
     * Check and using depends processes
     *
     * @return $this
     */
    public function reindexEverything()
    {
        if ($this->getData('runed_reindexall')) {
            return $this;
        }

        /** @var Mage_Index_Model_Resource_Event $eventResource */
        $eventResource = Mage::getResourceSingleton('index/event');
        $unprocessedEvents = $eventResource->getUnprocessedEvents($this);
        $this->setForcePartialReindex(count($unprocessedEvents) > 0 && $this->getStatus() == self::STATUS_PENDING);

        if ($this->getDepends()) {
            /** @var Mage_Index_Model_Indexer $indexer */
            $indexer = Mage::getSingleton('index/indexer');
            foreach ($this->getDepends() as $code) {
                $process = $indexer->getProcessByCode($code);
                if ($process) {
                    $process->reindexEverything();
                }
            }
        }

        $this->setData('runed_reindexall', true);
        return $this->reindexAll();
    }

    /**
     * Process event with assigned indexer object
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function processEvent(Mage_Index_Model_Event $event)
    {
        if (!$this->matchEvent($event)) {
            return $this;
        }
        if ($this->getMode() == self::MODE_MANUAL) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);
            return $this;
        }

        $this->_getResource()->updateProcessStartDate($this);
        $this->_setEventNamespace($event);
        $isError = false;

        try {
            $this->getIndexer()->processEvent($event);
        } catch (Exception $e) {
            $isError = true;
        }
        $event->resetData();
        $this->_resetEventNamespace($event);
        $this->_getResource()->updateProcessEndDate($this);
        $event->addProcessId($this->getId(), $isError ? self::EVENT_STATUS_ERROR : self::EVENT_STATUS_DONE);

        return $this;
    }

    /**
     * Get Indexer strategy object
     *
     * @return Mage_Index_Model_Indexer_Abstract
     */
    public function getIndexer()
    {
        if ($this->_indexer === null) {
            $code = $this->_getData('indexer_code');
            if (!$code) {
                Mage::throwException(Mage::helper('index')->__('Indexer code is not defined.'));
            }
            $xmlPath = self::XML_PATH_INDEXER_DATA . '/' . $code;
            $config = Mage::getConfig()->getNode($xmlPath);
            if (!$config || empty($config->model)) {
                Mage::throwException(Mage::helper('index')->__('Indexer model is not defined.'));
            }
            $model = Mage::getModel((string)$config->model);
            if ($model instanceof Mage_Index_Model_Indexer_Abstract) {
                $this->_indexer = $model;
            } else {
                Mage::throwException(Mage::helper('index')->__('Indexer model should extend Mage_Index_Model_Indexer_Abstract.'));
            }
        }
        return $this->_indexer;
    }

    /**
     * Index pending events addressed to the process
     *
     * @param   null|string $entity
     * @param   null|string $type
     * @return  $this
     */
    public function indexEvents($entity = null, $type = null)
    {
        /**
         * Check if process indexer can match entity code and action type
         */
        if ($entity !== null && $type !== null) {
            if (!$this->getIndexer()->matchEntityAndType($entity, $type)) {
                return $this;
            }
        }

        if ($this->getMode() == self::MODE_MANUAL) {
            return $this;
        }

        if ($this->isLocked()) {
            return $this;
        }

        $this->lock();
        try {
            /**
             * Prepare events collection
             */
            $eventsCollection = $this->getUnprocessedEventsCollection();
            if ($entity !== null) {
                $eventsCollection->addEntityFilter($entity);
            }
            if ($type !== null) {
                $eventsCollection->addTypeFilter($type);
            }

            $this->_processEventsCollection($eventsCollection);
            $this->unlock();
        } catch (Exception $e) {
            $this->unlock();
            throw $e;
        }
        return $this;
    }

    /**
     * Process all events of the collection
     *
     * @param Mage_Index_Model_Resource_Event_Collection $eventsCollection
     * @param bool $skipUnmatched
     * @return $this
     */
    protected function _processEventsCollection(
        Mage_Index_Model_Resource_Event_Collection $eventsCollection,
        $skipUnmatched = true
    ) {
        // We can't reload the collection because of transaction
        while ($event = $eventsCollection->fetchItem()) {
            /** @var Mage_Index_Model_Event $event */
            try {
                $this->processEvent($event);
                if (!$skipUnmatched) {
                    $eventProcessIds = $event->getProcessIds();
                    if (!isset($eventProcessIds[$this->getId()])) {
                        $event->addProcessId($this->getId(), null);
                    }
                }
            } catch (Exception $e) {
                $event->addProcessId($this->getId(), self::EVENT_STATUS_ERROR);
            }
            $event->save();
        }
        return $this;
    }

    /**
     * Update status process/event association
     *
     * @param   Mage_Index_Model_Event $event
     * @param   string $status
     * @return  $this
     */
    public function updateEventStatus(Mage_Index_Model_Event $event, $status)
    {
        $this->_getResource()->updateEventStatus($this->getId(), $event->getId(), $status);
        return $this;
    }

    /**
     * Returns Process lock name
     *
     * @return string
     */
    public function getProcessLockName()
    {
        return 'index_process_' . $this->getId();
    }

    /**
     * Returns Lock object.
     *
     * @return Mage_Index_Model_Lock|null
     */
    protected function _getLockInstance()
    {
        if (is_null($this->_lockInstance)) {
            $this->_lockInstance = Mage_Index_Model_Lock::getInstance();
        }
        return $this->_lockInstance;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return $this
     */
    public function lock()
    {
        $this->_getLockInstance()->setLock($this->getProcessLockName(), true);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return $this
     */
    public function lockAndBlock()
    {
        $this->_getLockInstance()->setLock($this->getProcessLockName(), true, true);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return $this
     */
    public function unlock()
    {
        $this->_getLockInstance()->releaseLock($this->getProcessLockName(), true);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->_getLockInstance()->isLockExists($this->getProcessLockName(), true);
    }

    /**
     * Change process status
     *
     * @param string $status
     * @return $this
     */
    public function changeStatus($status)
    {
        Mage::dispatchEvent('index_process_change_status', [
            'process' => $this,
            'status' => $status
        ]);
        $this->_getResource()->updateStatus($this, $status);
        return $this;
    }

    /**
     * Get list of process mode options
     *
     * @return array
     */
    public function getModesOptions()
    {
        return [
            self::MODE_REAL_TIME => Mage::helper('index')->__('Update on Save'),
            self::MODE_MANUAL => Mage::helper('index')->__('Manual Update')
        ];
    }

    /**
     * Get list of process status options
     *
     * @return array
     */
    public function getStatusesOptions()
    {
        return [
            self::STATUS_PENDING            => Mage::helper('index')->__('Ready'),
            self::STATUS_RUNNING            => Mage::helper('index')->__('Processing'),
            self::STATUS_REQUIRE_REINDEX    => Mage::helper('index')->__('Reindex Required'),
        ];
    }

    /**
     * Get list of "Update Required" options
     *
     * @return array
     */
    public function getUpdateRequiredOptions()
    {
        return [
            0 => Mage::helper('index')->__('No'),
            1 => Mage::helper('index')->__('Yes'),
        ];
    }

    /**
     * Retrieve depend indexer codes
     *
     * @return array
     */
    public function getDepends()
    {
        $depends = $this->getData('depends');
        if (is_null($depends)) {
            $depends = [];
            $path = self::XML_PATH_INDEXER_DATA . '/' . $this->getIndexerCode();
            $node = Mage::getConfig()->getNode($path);
            if ($node) {
                $data = $node->asArray();
                if (isset($data['depends']) && is_array($data['depends'])) {
                    $depends = array_keys($data['depends']);
                }
            }

            $this->setData('depends', $depends);
        }

        return $depends;
    }

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return $this
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Disable keys in index table
     *
     * @return $this
     */
    public function disableIndexerKeys()
    {
        $indexer = $this->getIndexer();
        if ($indexer) {
            $indexer->disableKeys();
        }
        return $this;
    }

    /**
     * Enable keys in index table
     *
     * @return $this
     */
    public function enableIndexerKeys()
    {
        $indexer = $this->getIndexer();
        if ($indexer) {
            $indexer->enableKeys();
        }
        return $this;
    }

    /**
     * Process event with locks checking
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    public function safeProcessEvent(Mage_Index_Model_Event $event)
    {
        if ($this->isLocked()) {
            return $this;
        }
        if (!$this->matchEvent($event)) {
            return $this;
        }
        $this->lock();
        try {
            $this->processEvent($event);
            $this->unlock();
        } catch (Exception $e) {
            $this->unlock();
            throw $e;
        }
        return $this;
    }

    /**
     * Get unprocessed events collection
     *
     * @return Mage_Index_Model_Resource_Event_Collection
     */
    public function getUnprocessedEventsCollection()
    {
        /** @var Mage_Index_Model_Resource_Event_Collection $eventsCollection */
        $eventsCollection = Mage::getResourceModel('index/event_collection');
        $eventsCollection->addProcessFilter($this, self::EVENT_STATUS_NEW);
        return $eventsCollection;
    }
}
