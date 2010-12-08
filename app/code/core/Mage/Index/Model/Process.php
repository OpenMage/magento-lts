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

class Mage_Index_Model_Process extends Mage_Core_Model_Abstract
{
    const XML_PATH_INDEXER_DATA     = 'global/index/indexer';
    /**
     * Process statuses
     */
    const STATUS_RUNNING            = 'working';
    const STATUS_PENDING            = 'pending';
    const STATUS_REQUIRE_REINDEX    = 'require_reindex';

    /**
     * Process event statuses
     */
    const EVENT_STATUS_NEW          = 'new';
    const EVENT_STATUS_DONE         = 'done';
    const EVENT_STATUS_ERROR        = 'error';
    const EVENT_STATUS_WORKING      = 'working';

    /**
     * Process modes
     * Process mode allow disable automatic process events processing
     */
    const MODE_MANUAL              = 'manual';
    const MODE_REAL_TIME           = 'real_time';

    /**
     * Indexer stategy object
     *
     * @var Mage_Index_Model_Indexer_Abstract
     */
    protected $_indexer = null;

    /**
     * Process lock properties
     */
    protected $_isLocked = null;
    protected $_lockFile = null;

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
     * @return  Mage_Index_Model_Process
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
     * @return  Mage_Index_Model_Process
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
     */
    public function register(Mage_Index_Model_Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_setEventNamespace($event);
            $this->getIndexer()->register($event);
            $event->addProcessId($this->getId());
            $this->_resetEventNamespace($event);
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
     * Reindex all data what this process responsible is
     *
     * @return unknown_type
     */
    public function reindexAll()
    {
        if ($this->isLocked()) {
            Mage::throwException(Mage::helper('index')->__('%s Index process is working now. Please try run this process later.', $this->getIndexer()->getName()));
        }
        $this->_getResource()->startProcess($this);
        $this->lock();
        $this->getIndexer()->reindexAll();
        $this->unlock();
        $this->_getResource()->endProcess($this);
    }

    /**
     * Reindex all data what this process responsible is
     * Check and using depends processes
     *
     * @return Mage_Index_Model_Process
     */
    public function reindexEverything()
    {
        if ($this->getData('runed_reindexall')) {
            return $this;
        }

        if ($this->getDepends()) {
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
     * @return Mage_Index_Model_Process
     */
    public function processEvent(Mage_Index_Model_Event $event)
    {
        if ($this->getMode() == self::MODE_MANUAL) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);
            return $this;
        }
        if (!$this->getIndexer()->matchEvent($event)) {
            return $this;
        }
        $this->_setEventNamespace($event);
        $this->getIndexer()->processEvent($event);
        $event->resetData();
        $this->_resetEventNamespace($event);
        $event->addProcessId($this->getId(), self::EVENT_STATUS_DONE);
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
     * @return  Mage_Index_Model_Process
     */
    public function indexEvents($entity=null, $type=null)
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

        /**
         * Prepare events collection
         */
        $eventsCollection = Mage::getResourceModel('index/event_collection')
            ->addProcessFilter($this, self::EVENT_STATUS_NEW);
        if ($entity !== null) {
            $eventsCollection->addEntityFilter($entity);
        }
        if ($type !== null) {
            $eventsCollection->addTypeFilter($type);
        }

        /**
         * Process all new events
         */
        while ($eventsCollection->getSize()) {
            foreach ($eventsCollection as $event) {
                try {
                    $this->processEvent($event);
                } catch (Exception $e) {
                    $event->addProcessId($this->getId(), self::EVENT_STATUS_ERROR);
                }
                $event->save();
            }
            $eventsCollection->reset();
        }

        $this->unlock();
        return $this;
    }

    /**
     * Update status process/event association
     *
     * @param   Mage_Index_Model_Event $event
     * @param   string $status
     * @return  Mage_Index_Model_Process
     */
    public function updateEventStatus(Mage_Index_Model_Event $event, $status)
    {
        $this->_getResource()->updateEventStatus($this->getId(), $event->getId(), $status);
        return $this;
    }

    /**
     * Get lock file resource
     *
     * @return resource
     */
    protected function _getLockFile()
    {
        if ($this->_lockFile === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $file = $varDir . DS . 'index_process_'.$this->getId().'.lock';
            if (is_file($file)) {
                $this->_lockFile = fopen($file, 'w');
            } else {
                $this->_lockFile = fopen($file, 'x');
            }
            fwrite($this->_lockFile, date('r'));
        }
        return $this->_lockFile;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process runing and fast lock validation.
     *
     * @return Mage_Index_Model_Process
     */
    public function lock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX | LOCK_NB);
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Mage_Index_Model_Process
     */
    public function lockAndBlock()
    {
        $this->_isLocked = true;
        flock($this->_getLockFile(), LOCK_EX);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Mage_Index_Model_Process
     */
    public function unlock()
    {
        $this->_isLocked = false;
        flock($this->_getLockFile(), LOCK_UN);
        return $this;
    }

    /**
     * Check if process is locked
     *
     * @return bool
     */
    public function isLocked()
    {
        if ($this->_isLocked !== null) {
            return $this->_isLocked;
        } else {
            $fp = $this->_getLockFile();
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);
                return false;
            }
            return true;
        }
    }

    /**
     * Close file resource if it was opened
     */
    public function __destruct()
    {
        if ($this->_lockFile) {
            fclose($this->_lockFile);
        }
    }

    /**
     * Change process status
     *
     * @param string $status
     * @return Mage_Index_Model_Process
     */
    public function changeStatus($status)
    {
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
        return array(
            self::MODE_REAL_TIME => Mage::helper('index')->__('Update on Save'),
            self::MODE_MANUAL => Mage::helper('index')->__('Manual Update')
        );
    }

    /**
     * Get list of process status options
     *
     * @return array
     */
    public function getStatusesOptions()
    {
        return array(
            self::STATUS_PENDING            => Mage::helper('index')->__('Ready'),
            self::STATUS_RUNNING            => Mage::helper('index')->__('Processing'),
            self::STATUS_REQUIRE_REINDEX    => Mage::helper('index')->__('Reindex Required'),
        );
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
            $depends = array();
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
}
