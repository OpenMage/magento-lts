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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Compiler extends Mage_Shell_Abstract
{
    /**
     * Get Indexer instance
     *
     * @return Mage_Index_Model_Indexer
     */
    protected function _getIndexer()
    {
        return $this->_factory->getSingleton($this->_factory->getIndexClassAlias());
    }

    /**
     * Parse string with indexers and return array of indexer instances
     *
     * @param string $string
     * @return array
     */
    protected function _parseIndexerString($string)
    {
        $processes = array();
        if ($string == 'all') {
            $collection = $this->_getIndexer()->getProcessesCollection();
            foreach ($collection as $process) {
                if ($process->getIndexer()->isVisible() === false) {
                    continue;
                }
                $processes[] = $process;
            }
        } else if (!empty($string)) {
            $codes = explode(',', $string);
            $codes = array_map('trim', $codes);
            $processes = $this->_getIndexer()->getProcessesCollectionByCodes($codes);
            foreach($processes as $key => $process) {
                if ($process->getIndexer()->getVisibility() === false) {
                    unset($processes[$key]);
                }
            }
            if ($this->_getIndexer()->hasErrors()) {
                echo implode(PHP_EOL, $this->_getIndexer()->getErrors()), PHP_EOL;
            }
        }
        return $processes;
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        $_SESSION = array();
        if ($this->getArg('info')) {
            $processes = $this->_parseIndexerString('all');
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                echo sprintf('%-30s', $process->getIndexerCode());
                echo $process->getIndexer()->getName() . "\n";
            }
        } else if ($this->getArg('status') || $this->getArg('mode')) {
            if ($this->getArg('status')) {
                $processes  = $this->_parseIndexerString($this->getArg('status'));
            } else {
                $processes  = $this->_parseIndexerString($this->getArg('mode'));
            }
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                $status = 'unknown';
                if ($this->getArg('status')) {
                    switch ($process->getStatus()) {
                        case Mage_Index_Model_Process::STATUS_PENDING:
                            $status = 'Pending';
                            break;
                        case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX:
                            $status = 'Require Reindex';
                            break;
                        case Mage_Index_Model_Process::STATUS_RUNNING:
                            $status = 'Running';
                            break;
                        default:
                            $status = 'Ready';
                            break;
                    }
                } else {
                    switch ($process->getMode()) {
                        case Mage_Index_Model_Process::MODE_SCHEDULE:
                            $status = 'Update by schedule';
                            break;
                        case Mage_Index_Model_Process::MODE_REAL_TIME:
                            $status = 'Update on Save';
                            break;
                        case Mage_Index_Model_Process::MODE_MANUAL:
                            $status = 'Manual Update';
                            break;
                    }
                }
                echo sprintf('%-35s ', $process->getIndexer()->getName() . ':') . $status ."\n";

            }
        } else if ($this->getArg('mode-realtime') || $this->getArg('mode-manual')) {
            if ($this->getArg('mode-realtime')) {
                $mode       = Mage_Index_Model_Process::MODE_REAL_TIME;
                $processes  = $this->_parseIndexerString($this->getArg('mode-realtime'));
            } else {
                $mode       = Mage_Index_Model_Process::MODE_MANUAL;
                $processes  = $this->_parseIndexerString($this->getArg('mode-manual'));
            }
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                try {
                    $process->setMode($mode)->save();
                    echo $process->getIndexer()->getName() . " index was successfully changed index mode\n";
                } catch (Mage_Core_Exception $e) {
                    echo $e->getMessage() . "\n";
                } catch (Exception $e) {
                    echo $process->getIndexer()->getName() . " index process unknown error:\n";
                    echo $e . "\n";
                }
            }
        } else if ($this->getArg('reindex') || $this->getArg('reindexall')) {
            if ($this->getArg('reindex')) {
                $processes = $this->_parseIndexerString($this->getArg('reindex'));
            } else {
                $processes = $this->_parseIndexerString('all');
            }

            try {
                Mage::dispatchEvent('shell_reindex_init_process');
                foreach ($processes as $process) {
                    /* @var $process Mage_Index_Model_Process */
                    try {
                        $startTime = microtime(true);
                        $process->reindexEverything();
                        $resultTime = microtime(true) - $startTime;
                        Mage::dispatchEvent($process->getIndexerCode() . '_shell_reindex_after');
                        echo $process->getIndexer()->getName()
                            . " index was rebuilt successfully in " . gmdate('H:i:s', $resultTime) . "\n";
                    } catch (Mage_Core_Exception $e) {
                        echo $e->getMessage() . "\n";
                    } catch (Exception $e) {
                        echo $process->getIndexer()->getName() . " index process unknown error:\n";
                        echo $e . "\n";
                    }
                }
                Mage::dispatchEvent('shell_reindex_finalize_process');
            } catch (Exception $e) {
                Mage::dispatchEvent('shell_reindex_finalize_process');
                echo $e->getMessage() . "\n";
            }

        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f indexer.php -- [options]

  --status <indexer>            Show Indexer(s) Status
  --mode <indexer>              Show Indexer(s) Index Mode
  --mode-realtime <indexer>     Set index mode type "Update on Save"
  --mode-manual <indexer>       Set index mode type "Manual Update"
  --reindex <indexer>           Reindex Data
  info                          Show allowed indexers
  reindexall                    Reindex Data by all indexers
  help                          This help

  <indexer>     Comma separated indexer codes or value "all" for all indexers

USAGE;
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
