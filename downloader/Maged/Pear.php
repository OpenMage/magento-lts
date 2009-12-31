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
 * @category   Varien
 * @package    Varien_Object
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

error_reporting(E_ALL & ~E_NOTICE);

// just a shortcut
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// add PEAR lib in include_path if needed
$_includePath = get_include_path();
$_pearDir = dirname(dirname(__FILE__)) . DS . 'pearlib';
if (!getenv('PHP_PEAR_INSTALL_DIR')) {
    putenv('PHP_PEAR_INSTALL_DIR=' . $_pearDir);
}
$_pearPhpDir = $_pearDir . DS . 'php';
if (strpos($_includePath, $_pearPhpDir) === false) {
    if (substr($_includePath, 0, 2) === '.' . PATH_SEPARATOR) {
        $_includePath = '.' . PATH_SEPARATOR . $_pearPhpDir . PATH_SEPARATOR . substr($_includePath, 2);
    } else {
        $_includePath = $_pearPhpDir . PATH_SEPARATOR . $_includePath;
    }
    set_include_path($_includePath);
}

// include necessary PEAR libs
require_once "PEAR.php";
require_once "PEAR/Frontend.php";
require_once "PEAR/Registry.php";
require_once "PEAR/Config.php";
require_once "PEAR/Command.php";
require_once "PEAR/Exception.php";

require_once "Maged/Pear/Frontend.php";
require_once "Maged/Pear/Package.php";
require_once "Maged/Pear/Registry.php";
require_once "Maged/Model/Pear/Request.php";

class Maged_Pear
{
    protected $_config;

    protected $_registry;

    protected $_frontend;

    protected $_cmdCache = array();

    static protected $_instance;

    public function __construct()
    {
        $this->getConfig();
    }

    public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function isSystemPackage($pkg)
    {
        return in_array($pkg, array('Archive_Tar', 'Console_Getopt', 'PEAR', 'Structures_Graph'));
    }

    public function getBaseDir()
    {
        return dirname(dirname(dirname(__FILE__)));
    }

    public function getPearDir()
    {
        return dirname(dirname(__FILE__)).DS.'pearlib';
    }

    public function getConfig()
    {
        if (!$this->_config) {
            $pear_dir = $this->getPearDir();

            $config = PEAR_Config::singleton($pear_dir.DS.'pear.ini', '-');
            //$config = PEAR_Config::singleton();

            $config->set('auto_discover', 1);
            $config->set('cache_ttl', 60);
            #$config->set('preferred_state', 'beta');

            $config->set('bin_dir', $pear_dir);
            $config->set('php_dir', $pear_dir.DS.'php');
            $config->set('download_dir', $pear_dir.DS.'download');
            $config->set('temp_dir', $pear_dir.DS.'temp');
            $config->set('data_dir', $pear_dir.DS.'data');
            $config->set('cache_dir', $pear_dir.DS.'cache');
            $config->set('test_dir', $pear_dir.DS.'tests');
            $config->set('doc_dir', $pear_dir.DS.'docs');

            foreach ($config->getKeys() as $key) {
                if (!(substr($key, 0, 5)==='mage_' && substr($key, -4)==='_dir')) {
                    continue;
                }
                $config->set($key, preg_replace('#^\.#', addslashes($this->getBaseDir()), $config->get($key)));
                //echo $key.' : '.$config->get($key).'<br>';
            }

            $reg = $this->getRegistry();
            $config->setRegistry($reg);

            PEAR_DependencyDB::singleton($config, $pear_dir.DS.'php'.DS.'.depdb');

            PEAR_Frontend::setFrontendObject($this->getFrontend());

            PEAR_Command::registerCommands(false, $pear_dir.DS.'php'.DS.'PEAR'.DS.'Command'.DS);

            $this->_config = $config;
        }
        return $this->_config;
    }

    public function getMagentoChannels()
    {
        return array(
            'connect.magentocommerce.com/core' => 'Core Team Extensions',
            'connect.magentocommerce.com/community' => 'Community Extensions',
        );
    }

    public function getRegistry()
    {
//        return $this->getConfig()->getRegistry();

        if (!$this->_registry) {
            $this->_registry = new Maged_Pear_Registry($this->getPearDir().DS.'php');

            $changed = false;
            foreach ($this->getMagentoChannels() as $channel=>$channelName) {
                if (!$this->getRegistry()->channelExists($channel)) {
                    $this->run('channel-discover', array(), array($channel));
                    $changed = true;
                }
            }
            if ($changed && empty($_GET['pear_registry'])) {
                //TODO:refresh registry in memory to reflect discovered channels without redirect
                header("Location: ".$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&pear_registry=1');
                exit;
            }
        }
        return $this->_registry;
    }

    public function getFrontend()
    {
        if (!$this->_frontend) {
            $this->_frontend = new Maged_Pear_Frontend;
        }
        return $this->_frontend;
    }

    public function getLog()
    {
        return $this->getFrontend()->getLog();
    }

    public function getOutput()
    {
        return $this->getFrontend()->getOutput();
    }

    public function cleanRegistry()
    {
        $oldDir = @getcwd();
        @chdir($this->getPearDir().DIRECTORY_SEPARATOR.'php');
        $this->delTree('.registry');
        @chdir($oldDir);
        return $this;
    }

    public function delTree($path) {
        if (@is_dir($path)) {
            $entries = @scandir($path);
            foreach ($entries as $entry) {
                if ($entry != '.' && $entry != '..') {
                    $this->delTree($path.DS.$entry);
                }
            }
            @rmdir($path);
        } else {
            @unlink($path);
        }
        return $this;
    }

    public function run($command, $options=array(), $params=array())
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '256M');

        if (empty($this->_cmdCache[$command])) {
            $cmd = PEAR_Command::factory($command, $this->getConfig());
            if ($cmd instanceof PEAR_Error) {
                return $cmd;
            }
            $this->_cmdCache[$command] = $cmd;
        } else {
            $cmd = $this->_cmdCache[$command];
        }
        #$cmd = PEAR_Command::factory($command, $this->getConfig());
        $result = $cmd->run($command, $options, $params);
        return $result;
    }

    public function setRemoteConfig($uri) #$host, $user, $password, $path='', $port=null)
    {
        #$uri = 'ftp://' . $user . ':' . $password . '@' . $host . (is_numeric($port) ? ':' . $port : '') . '/' . trim($path, '/') . '/';
        $this->run('config-set', array(), array('remote_config', $uri));
        return $this;
    }

    /**
     * Run PEAR command with html output console style
     *
     * @param array|Maged_Model $runParams command, options, params,
     *        comment, success_callback, failure_callback
     */
    public function runHtmlConsole($runParams)
    {
        ob_implicit_flush();

        $fe = $this->getFrontend();
        $oldLogStream = $fe->getLogStream();
        $fe->setLogStream('stdout');

        if ($runParams instanceof Maged_Model) {
            $run = $runParams;
        } elseif (is_array($runParams)) {
            $run = new Maged_Model_Pear_Request($runParams);
        } elseif (is_string($runParams)) {
            $run = new Maged_Model_Pear_Request(array('comment'=>$runParams));
        } else {
            throw Maged_Exception("Invalid run parameters");
        }

        if (!$run->get('no-header')) {
?>
<html><head><style type="text/css">
body { margin:0px;
    padding:3px;
    background:black;
    color:#2EC029;
    font:normal 11px Lucida Console, Courier New, serif;
    }
</style></head><body>
<script type="text/javascript">
if (parent && parent.disableInputs) {
    parent.disableInputs(true);
}
if (typeof auto_scroll=='undefined') {
    var auto_scroll = window.setInterval(console_scroll, 10);
}
function console_scroll()
{
    if (typeof top.$!='function') {
        return;
    }
    if (top.$('pear_iframe_scroll').checked) {
        document.body.scrollTop+=3;
    }
}
</script>
<?php
        }
        echo htmlspecialchars($run->get('comment'));

        if ($command = $run->get('command')) {
            $result = $this->run($command, $run->get('options'), $run->get('params'));

            if ($result instanceof PEAR_Error) {
                echo "\r\n\r\nPEAR ERROR: ".nl2br($result->getMessage());
            }
            echo '<script type="text/javascript">';
            if ($result instanceof PEAR_Error) {
                if ($callback = $run->get('failure_callback')) {
                    if (is_array($callback)) {
                        call_user_func_array($callback, array($result));
                    } else {
                        echo $callback;
                    }
                }
            } else {
                if (!$run->get('no-footer')) {
                    if ($callback = $run->get('success_callback')) {
                        if (is_array($callback)) {
                            call_user_func_array($callback, array($result));
                        } else {
                            echo $callback;
                        }
                    }
                }
            }
            echo '</script>';
        } else {
            $result = false;
        }
        if ($result instanceof PEAR_Error || !$run->get('no-footer')) {
?>
<script type="text/javascript">
if (parent && parent.disableInputs) {
    parent.disableInputs(false);
}
</script>
</body></html>
<?php
            $fe->setLogStream($oldLogStream);
        }
        return $result;
    }
}

