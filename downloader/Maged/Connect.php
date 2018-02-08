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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

error_reporting(E_ALL & ~E_NOTICE);

// just a shortcut
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// add Mage lib in include_path if needed
$_includePath = get_include_path();
$_libDir = dirname(dirname(__FILE__)) . DS . 'lib';
if (strpos($_includePath, $_libDir) === false) {
    if (substr($_includePath, 0, 2) === '.' . PATH_SEPARATOR) {
        $_includePath = '.' . PATH_SEPARATOR . $_libDir . PATH_SEPARATOR . substr($_includePath, 2);
    } else {
        $_includePath = $_libDir . PATH_SEPARATOR . $_includePath;
    }
    set_include_path($_includePath);
}

/**
* Class for connect
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Connect
{

    /**
     * Object of config
     *
     * @var Mage_Connect_Config
     */
    protected $_config;

    /**
     * Object of single config
     *
     * @var Mage_Connect_Singleconfig
     */
    protected $_sconfig;

    /**
    * Object of frontend
    *
    * @var Mage_Connect_Frontend
    */
    protected $_frontend;

    /**
     * Internal cache for command objects
     *
     * @var array
     */
    protected $_cmdCache = array();

    /**
     * Console Started flag
     *
     * @var boolean
     */
    protected $_consoleStarted = false;

    /**
     * Instance of class
     *
     * @var Maged_Connect
     */
    static protected $_instance;

    /**
     * Constructor loads Config, Cache Config and initializes Frontend
     */
    public function __construct()
    {
        $this->getConfig();
        $this->getSingleConfig();
        $this->getFrontend();
    }

    /**
     * Destructor, sends Console footer if Console started
     */
    public function __destruct()
    {
        if ($this->_consoleStarted) {
            $this->_consoleFooter();
        }
    }

    /**
     * Initialize instance
     *
     * @return Maged_Connect
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Retrieve object of config and set it to Mage_Connect_Command
     *
     * @return Mage_Connect_Config
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $this->_config = new Mage_Connect_Config();
            $ftp=$this->_config->__get('remote_config');
            if(!empty($ftp)){
                $packager = new Mage_Connect_Packager();
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
                $this->_config=$config;
                $this->_sconfig=$cache;
            }
            $this->_config->magento_root = dirname(dirname(__FILE__)).DS.'..';
            Mage_Connect_Command::setConfigObject($this->_config);
        }
        return $this->_config;
    }

    /**
     * Retrieve object of single config and set it to Mage_Connect_Command
     *
     * @param bool $reload
     * @return Mage_Connect_Singleconfig
     */
    public function getSingleConfig($reload = false)
    {
        if(!$this->_sconfig || $reload) {
            $this->_sconfig = new Mage_Connect_Singleconfig(
                $this->getConfig()->magento_root . DIRECTORY_SEPARATOR
                . $this->getConfig()->downloader_path . DIRECTORY_SEPARATOR
                . Mage_Connect_Singleconfig::DEFAULT_SCONFIG_FILENAME
            );
        }
        Mage_Connect_Command::setSconfig($this->_sconfig);
        return $this->_sconfig;

    }

    /**
     * Retrieve object of frontend and set it to Mage_Connect_Command
     *
     * @return Maged_Connect_Frontend
     */
    public function getFrontend()
    {
        if (!$this->_frontend) {
            $this->_frontend = new Maged_Connect_Frontend();
            Mage_Connect_Command::setFrontendObject($this->_frontend);
        }
        return $this->_frontend;
    }

    /**
     * Retrieve lof from frontend
     *
     * @return array
     */
    public function getLog()
    {
        return $this->getFrontend()->getLog();
    }

    /**
     * Retrieve output from frontend
     *
     * @return array
     */
    public function getOutput()
    {
        return $this->getFrontend()->getOutput();
    }

    /**
     * Clean registry
     *
     * @return Maged_Connect
     */
    public function cleanSconfig()
    {
        $this->getSingleConfig()->clear();
        return $this;
    }

    /**
     * Delete directory recursively
     *
     * @param string $path
     * @return Maged_Connect
     */
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

    /**
     * Run commands from Mage_Connect_Command
     *
     * @param string $command
     * @param array $options
     * @param array $params
     * @return boolean|Mage_Connect_Error
     */
    public function run($command, $options=array(), $params=array())
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '256M');

        if (empty($this->_cmdCache[$command])) {
            Mage_Connect_Command::getCommands();
            /**
            * @var $cmd Mage_Connect_Command
            */
            $cmd = Mage_Connect_Command::getInstance($command);
            if ($cmd instanceof Mage_Connect_Error) {
                return $cmd;
            }
            $this->_cmdCache[$command] = $cmd;
        } else {
            /**
            * @var $cmd Mage_Connect_Command
            */
            $cmd = $this->_cmdCache[$command];
        }
        $ftp=$this->getConfig()->remote_config;
        if(strlen($ftp)>0){
            $options=array_merge($options, array('ftp'=>$ftp));
        }
        $cmd->run($command, $options, $params);
        if ($cmd->ui()->hasErrors()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Set remote Config by URI
     *
     * @param $uri
     * @return Maged_Connect
     */
    public function setRemoteConfig($uri)
    {
        $this->getConfig()->remote_config=$uri;
        return $this;
    }

    /**
     * Show Errors
     *
     * @param array $errors Error messages
     * @return Maged_Connect
     */
    public function showConnectErrors($errors)
    {
        echo '<script type="text/javascript">';
        $run = new Maged_Model_Connect_Request();
        if ($callback = $run->get('failure_callback')) {
            if (is_array($callback)) {
                call_user_func_array($callback, array($errors));
            } else {
                echo $callback;
            }
        }
        echo '</script>';

        return $this;
    }

    /**
     * Run Mage_Connect_Command with html output console style
     *
     * @throws Maged_Exception
     * @param array|string|Maged_Model $runParams command, options, params, comment, success_callback, failure_callback
     * @return bool|Mage_Connect_Error
     */
    public function runHtmlConsole($runParams)
    {
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
        ob_implicit_flush();

        $fe = $this->getFrontend();
        $oldLogStream = $fe->getLogStream();
        $fe->setLogStream('stdout');

        if ($runParams instanceof Maged_Model) {
            $run = $runParams;
        } elseif (is_array($runParams)) {
            $run = new Maged_Model_Connect_Request($runParams);
        } elseif (is_string($runParams)) {
            $run = new Maged_Model_Connect_Request(array('comment'=>$runParams));
        } else {
            throw Maged_Exception("Invalid run parameters");
        }

        if (!$run->get('no-header')) {
            $this->_consoleHeader();
        }
        echo htmlspecialchars($run->get('comment')).'<br/>';

        if ($command = $run->get('command')) {
            $result = $this->run($command, $run->get('options'), $run->get('params'));

            if ($this->getFrontend()->hasErrors()) {
                echo "<br/>CONNECT ERROR: ";
                foreach ($this->getFrontend()->getErrors(false) as $error) {
                    echo nl2br($error[1]);
                    echo '<br/>';
                }
            }
            echo '<script type="text/javascript">';
            if ($this->getFrontend()->hasErrors()) {
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
        if ($this->getFrontend()->getErrors() || !$run->get('no-footer')) {
            //$this->_consoleFooter();
            $fe->setLogStream($oldLogStream);
        }
        return $result;
    }

    /**
     * Show HTML Console Header
     *
     * @return void
     */
    protected function _consoleHeader() {
        if (!$this->_consoleStarted) {
            $validateKey = md5(time());
            $sessionModel = new Maged_Model_Session();
            $sessionModel->set('validate_cache_key', $validateKey); ?>
<html><head><style type="text/css">
body { margin:0px;
    padding:3px;
    background:black;
    color:#2EC029;
    font:normal 11px Lucida Console, Courier New, serif;
    }
</style>
<script type="text/javascript" src="js/prototype.js"></script>
</head><body>
<script type="text/javascript">
if (parent && parent.disableInputs) {
    parent.disableInputs(true);
}
if (typeof auto_scroll=='undefined') {
    var auto_scroll = window.setInterval(console_scroll, 10);
}
function console_scroll()
{
    if (typeof top.$ != 'function') {
        return;
    }
    if (top.$('connect_iframe_scroll').checked) {
        document.body.scrollTop+=3;
    }
}
function show_message(message, newline)
{
    var bodyElement = document.getElementsByTagName('body')[0];
    if (typeof newline == 'undefined') {
        newline = true
    }
    if (newline) {
        bodyElement.innerHTML += '<br/>';
    }
    bodyElement.innerHTML += message;
}
function clear_cache(callbacks)
{
    if (typeof top.Ajax != 'object') {
        return;
    }
    var message = 'Exception during cache and session cleaning';
    var url = window.location.href.split('?')[0] + '?A=cleanCache';
    var intervalID = setInterval(function() {show_message('.', false); }, 500);
    var clean = 0;
    var maintenance = 0;
    var validate_cache_key = '<?php echo $validateKey; ?>';
    if (window.location.href.indexOf('clean_sessions') >= 0) {
        clean = 1;
    }
    if (window.location.href.indexOf('maintenance') >= 0) {
        maintenance = 1;
    }

    new top.Ajax.Request(url, {
        method: 'post',
        parameters: {clean_sessions:clean, maintenance:maintenance, validate_cache_key:validate_cache_key},
        onCreate: function() {
            show_message('Cleaning cache');
            show_message('');
        },
        onSuccess: function(transport, json) {
            var result = true;
            try{
                var response = transport.responseJSON || transport.responseText.evalJSON(true) || {};
                result = response.result || false;

                if (typeof response.message != 'undefined') {
                    if (response.message.length > 0) {
                        message = response.message;
                    } else {
                        message = 'Cache cleaned successfully';
                    }
                }
            } catch (ex){
                result = false;
            }
            if (result) {
                callbacks.success();
            } else {
                callbacks.fail();
            }
        },
        onFailure: function() {
            callbacks.fail();
        },
        onComplete: function(transport) {
            clearInterval(intervalID);
            show_message(message);
        }
    });
}
</script>
<?php
            $this->_consoleStarted = true;
        }
    }

    /**
     * Show HTML Console Footer
     *
     * @return void
     */
    protected function _consoleFooter() {
        if ($this->_consoleStarted) {
?>
<script type="text/javascript">
if (parent && parent.disableInputs) {
    parent.disableInputs(false);
}
</script>
</body></html>
<?php
            $this->_consoleStarted = false;
        }
    }
}
