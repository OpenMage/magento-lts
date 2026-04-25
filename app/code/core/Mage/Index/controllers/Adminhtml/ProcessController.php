<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * @package    Mage_Index
 */
class Mage_Index_Adminhtml_ProcessController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/index';

    /**
     * Initialize process object by request
     *
     * @return false|Mage_Index_Model_Process
     */
    protected function _initProcess()
    {
        $processId = $this->getRequest()->getParam('process');
        if ($processId) {
            /** @var Mage_Index_Model_Process $process */
            $process = Mage::getModel('index/process')->load($processId);
            if ($process->getId() && $process->getIndexer()->isVisible()) {
                return $process;
            }
        }

        return false;
    }

    /**
     * Display processes grid action
     * @return void
     */
    public function listAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Index Management'));

        $this->loadLayout();
        $this->_setActiveMenu('system/index');
        $this->renderLayout();
    }

    /**
     * Process detail and edit action
     * @return void
     */
    public function editAction()
    {
        /** @var Mage_Index_Model_Process $process */
        $process = $this->_initProcess();
        if ($process) {
            $this->_title($process->getIndexCode());

            $this->_title($this->__('System'))
                 ->_title($this->__('Index Management'))
                 ->_title($this->__($process->getIndexer()->getName()));

            Mage::register('current_index_process', $process);
            $this->loadLayout();
            $this->_setActiveMenu('system/index');
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(
                Mage::helper('index')->__('Cannot initialize the indexer process.'),
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Save process data
     * @return void
     */
    public function saveAction()
    {
        /** @var Mage_Index_Model_Process $process */
        $process = $this->_initProcess();
        if ($process) {
            $mode = $this->getRequest()->getPost('mode');
            if ($mode) {
                $process->setMode($mode);
            }

            try {
                $process->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('index')->__('The index has been saved.'),
                );
            } catch (Mage_Core_Exception $mageCoreException) {
                $this->_getSession()->addError($mageCoreException->getMessage());
            } catch (Exception $exception) {
                $this->_getSession()->addException(
                    $exception,
                    Mage::helper('index')->__('There was a problem with saving process.'),
                );
            }

            $this->_redirect('*/*/list');
        } else {
            $this->_getSession()->addError(
                Mage::helper('index')->__('Cannot initialize the indexer process.'),
            );
            $this->_redirect('*/*/list');
        }
    }

    /**
     * Reindex all data what process is responsible
     * @return void
     */
    public function reindexProcessAction()
    {
        /** @var Mage_Index_Model_Process $process */
        $process = $this->_initProcess();
        if ($process) {
            try {
                Varien_Profiler::start('__INDEX_PROCESS_REINDEX_ALL__');

                $process->reindexEverything();
                Varien_Profiler::stop('__INDEX_PROCESS_REINDEX_ALL__');
                $this->_getSession()->addSuccess(
                    Mage::helper('index')->__('%s index was rebuilt.', $process->getIndexer()->getName()),
                );
            } catch (Mage_Core_Exception $mageCoreException) {
                $this->_getSession()->addError($mageCoreException->getMessage());
            } catch (Exception $exception) {
                $this->_getSession()->addException(
                    $exception,
                    Mage::helper('index')->__('There was a problem with reindexing process.'),
                );
            }
        } else {
            $this->_getSession()->addError(
                Mage::helper('index')->__('Cannot initialize the indexer process.'),
            );
        }

        $this->_redirect('*/*/list');
    }

    /**
     * Reindex pending events for index process
     * @return void
     */
    public function reindexEventsAction() {}

    /**
     * Rebiuld all processes index
     * @return void
     */
    public function reindexAllAction() {}

    /**
     * Mass rebuild selected processes index
     * @return void
     */
    public function massReindexAction()
    {
        /** @var Mage_Index_Model_Indexer $indexer */
        $indexer    = Mage::getSingleton('index/indexer');
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_getSession()->addError(Mage::helper('index')->__('Please select Indexes'));
        } else {
            try {
                $counter = 0;
                foreach ($processIds as $processId) {
                    /** @var Mage_Index_Model_Process $process */
                    $process = $indexer->getProcessById($processId);
                    if ($process && $process->getIndexer()->isVisible()) {
                        $process->reindexEverything();
                        $counter++;
                    }
                }

                $this->_getSession()->addSuccess(
                    Mage::helper('index')->__('Total of %d index(es) have reindexed data.', $counter),
                );
            } catch (Mage_Core_Exception $mageCoreException) {
                $this->_getSession()->addError($mageCoreException->getMessage());
            } catch (Exception $exception) {
                $this->_getSession()->addException($exception, Mage::helper('index')->__('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('*/*/list');
    }

    /**
     * Mass change index mode of selected processes index
     * @return void
     */
    public function massChangeModeAction()
    {
        $processIds = $this->getRequest()->getParam('process');
        if (empty($processIds) || !is_array($processIds)) {
            $this->_getSession()->addError(Mage::helper('index')->__('Please select Index(es)'));
        } else {
            try {
                $counter = 0;
                $mode = $this->getRequest()->getParam('index_mode');
                foreach ($processIds as $processId) {
                    /** @var Mage_Index_Model_Process $process */
                    $process = Mage::getModel('index/process')->load($processId);
                    if ($process->getId() && $process->getIndexer()->isVisible()) {
                        $process->setMode($mode)->save();
                        $counter++;
                    }
                }

                $this->_getSession()->addSuccess(
                    Mage::helper('index')->__('Total of %d index(es) have changed index mode.', $counter),
                );
            } catch (Mage_Core_Exception $mageCoreException) {
                $this->_getSession()->addError($mageCoreException->getMessage());
            } catch (Exception $exception) {
                $this->_getSession()->addException($exception, Mage::helper('index')->__('Cannot initialize the indexer process.'));
            }
        }

        $this->_redirect('*/*/list');
    }
}
