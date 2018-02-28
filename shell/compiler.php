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
     * Compiler process object
     *
     * @var Mage_Compiler_Model_Process
     */
    protected $_compiler;

    /**
     * Get compiler process object
     *
     * @return Mage_Compiler_Model_Process
     */
    protected function _getCompiler()
    {
        if ($this->_compiler === null) {
            $this->_compiler = Mage::getModel('compiler/process');
        }
        return $this->_compiler;
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if (isset($this->_args['disable'])) {
            $this->_getCompiler()->registerIncludePath(false);
            echo "Compiler include path disabled\n";
        } else if (isset($this->_args['enable'])) {
            if ($this->_getCompiler()->getCompiledFilesCount() == 0) {
                die("Compilation State: Not Compiled\nPlease run with option compile\n");
            }

            $this->_getCompiler()->registerIncludePath();
            echo "Compiler include path enabled\n";
        } else if (isset($this->_args['compile'])) {
            try {
                $this->_getCompiler()->run();
                echo "Compilation successfully finished\n";
            } catch (Mage_Core_Exception $e) {
                echo $e->getMessage() . "\n";
            } catch (Exception $e) {
                echo "Compilation unknown error:\n\n";
                echo $e . "\n";
            }
        } else if (isset($this->_args['clear'])) {
            try {
                $this->_getCompiler()->clear();
                echo "Compilation successfully cleared\n";
            } catch (Mage_Core_Exception $e) {
                echo $e->getMessage() . "\n";
            } catch (Exception $e) {
                echo "Compilation unknown error:\n\n";
                echo $e . "\n";
            }
        } else if (isset($this->_args['state']) || isset($this->_args['fullstate'])) {
            $compiler = $this->_getCompiler();
            $compilerConfig = '../includes/config.php';
            if (file_exists($compilerConfig)) {
                include $compilerConfig;
            }
            $status = defined('COMPILER_INCLUDE_PATH') ? 'Enabled' : 'Disabled';
            $state  = $compiler->getCollectedFilesCount() > 0 ? 'Compiled' : 'Not Compiled';
            echo "Compiler Status:          " . $status . "\n";
            echo "Compilation State:        " . $state . "\n";
            echo "Collected Files Count:    " . $compiler->getCollectedFilesCount() . "\n";
            echo "Compiled Scopes Count:    " . $compiler->getCompiledFilesCount() . "\n";
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
Usage:  php -f compiler.php -- [options]

  state         Show Compilation State
  compile       Run Compilation Process
  clear         Disable Compiler include path and Remove compiled files
  enable        Enable Compiler include path
  disable       Disable Compiler include path
  help          This help

USAGE;
    }
}

$shell = new Mage_Shell_Compiler();
$shell->run();
