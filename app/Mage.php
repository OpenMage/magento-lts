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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

Mage::register('original_include_path', get_include_path());

if (defined('COMPILER_INCLUDE_PATH')) {
    $app_path = COMPILER_INCLUDE_PATH;
    set_include_path($app_path . PS . Mage::registry('original_include_path'));
    include_once "Mage_Core_functions.php";
    include_once "Varien_Autoload.php";
} else {
    /**
     * Set include path
     */
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
    $paths[] = BP . DS . 'lib';

    $app_path = implode(PS, $paths);
    set_include_path($app_path . PS . Mage::registry('original_include_path'));
    include_once "Mage/Core/functions.php";
    include_once "Varien/Autoload.php";
}

Varien_Autoload::register();

/**
 * Main Mage hub class
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
final class Mage {
    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry = array();

    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    static private $_app;

    static private $_useCache = array();

    static private $_objects;

    static private $_isDownloader = false;

    static private $_isDeveloperMode = false;

    public static $headersSentThrowsException = true;

    public static function getVersion()
    {
        return '1.3.2.4';
    }

    /**
     * Set all my static data to defaults
     *
     */
    public static function reset()
    {
        self::$_registry = array();
        self::$_app      = null;
        self::$_useCache = array();
        self::$_objects  = null;
        self::$_isDownloader    = false;
        self::$_isDeveloperMode = false;
        // do not reset $headersSentThrowsException
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     */
    public static function register($key, $value, $graceful = false)
    {
        if(isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            Mage::throwException('Mage registry key "'.$key.'" already exists');
        }
        self::$_registry[$key] = $value;
    }

    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key],'__destruct'))) {
                self::$_registry[$key]->__destruct();
            }
            unset(self::$_registry[$key]);
        }
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    public static function registry($key)
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }
        return null;
    }

    /**
     * Set application root absolute path
     *
     * @param string $appRoot
     */
    public static function setRoot($appRoot='')
    {
        if (self::registry('appRoot')) {
            return ;
        }
        if (''===$appRoot) {
            // automagically find application root by dirname of Mage.php
            $appRoot = dirname(__FILE__);
        }

        $appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            Mage::register('appRoot', $appRoot);
        } else {
            Mage::throwException($appRoot.' is not a directory or not readable by this user');
        }
    }

    /**
     * Get application root absolute path
     *
     * @return string
     */

    public static function getRoot()
    {
        return Mage::registry('appRoot');
    }

    /**
     * Varien Objects Cache
     *
     * @param string $key optional, if specified will load this key
     * @return Varien_Object_Cache
     */
    public static function objects($key=null)
    {
        if (!self::$_objects) {
            self::$_objects = new Varien_Object_Cache;
        }
        if (is_null($key)) {
            return self::$_objects;
        } else {
            return self::$_objects->load($key);
        }
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     */
    public static function getBaseDir($type='base')
    {
        return Mage::getConfig()->getOptions()->getDir($type);
    }

    public static function getModuleDir($type, $moduleName)
    {
        return Mage::getConfig()->getModuleDir($type, $moduleName);
    }

    public static function getStoreConfig($path, $id=null)
    {
        return self::app()->getStore($id)->getConfig($path);
    }

    public static function getStoreConfigFlag($path, $id=null)
    {
        $flag = strtolower(Mage::getStoreConfig($path, $id));
        if (!empty($flag) && 'false'!==$flag && '0'!==$flag) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($type=Mage_Core_Model_Store::URL_TYPE_LINK, $secure=null)
    {
        return Mage::app()->getStore()->getBaseUrl($type, $secure);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public static function getUrl($route='', $params=array())
    {
        return Mage::getModel('core/url')->getUrl($route, $params);
    }

    /**
     * Get design package singleton
     *
     * @return Mage_Core_Model_Design_Package
     */
    public static function getDesign()
    {
        return Mage::getSingleton('core/design_package');
    }

    /**
     * Get a config object
     *
     * @return Mage_Core_Model_Config
     */
    public static function getConfig()
    {
        return Mage::registry('config');
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, $data=array(), $observerName='', $observerClass='')
    {
        if ($observerClass=='') {
            $observerClass = 'Varien_Event_Observer';
        }
        $observer = new $observerClass();
        $observer->setName($observerName)->addData($data)->setEventName($eventName)->setCallback($callback);
        return Mage::registry('events')->addObserver($observer);
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiobservers matching event name pattern
     *
     * @param string $name
     * @param array $args
     */
    public static function dispatchEvent($name, array $data=array())
    {
        Varien_Profiler::start('DISPATCH EVENT:'.$name);
        $result = Mage::app()->dispatchEvent($name, $data);
        #$result = Mage::registry('events')->dispatch($name, $data);
        Varien_Profiler::stop('DISPATCH EVENT:'.$name);
        return $result;
    }

    /**
     * Retrieve model object
     *
     * @link    Mage_Core_Model_Config::getModelInstance
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Mage_Core_Model_Abstract
     */
    public static function getModel($modelClass='', $arguments=array())
    {
        return Mage::getConfig()->getModelInstance($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Mage_Core_Model_Abstract
     */
    public static function getSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_singleton/'.$modelClass;
        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getModel($modelClass, $arguments));
        }
        return Mage::registry($registryKey);
    }

    /**
     * Retrieve object of resource model
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Object
     */
    public static function getResourceModel($modelClass, $arguments=array())
    {
        return Mage::getConfig()->getResourceModelInstance($modelClass, $arguments);
    }

    /**
     * Retrieve resource vodel object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  object
     */
    public static function getResourceSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_resource_singleton/'.$modelClass;
        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getResourceModel($modelClass, $arguments));
        }
        return Mage::registry($registryKey);
    }

    /**
     * Deprecated, use Mage::helper()
     *
     * @param string $type
     * @return object
     */
    public static function getBlockSingleton($type)
    {
        $action = Mage::app()->getFrontController()->getAction();
        return $action ? $action->getLayout()->getBlockSingleton($type) : false;
    }

    /**
     * Retrieve helper object
     *
     * @param   helper name $name
     * @return  Mage_Core_Helper_Abstract
     */
    public static function helper($name)
    {
        return Mage::app()->getHelper($name);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param integer $code
     */
    public static function exception($module='Mage_Core', $message='', $code=0)
    {
        $className = $module.'_Exception';
        return new $className($message, $code);
    }

    public static function throwException($message, $messageStorage=null)
    {
        if ($messageStorage && ($storage = Mage::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Mage_Core_Exception($message);
    }

    /**
     * Initialize and retrieve application
     *
     * @param   string $code
     * @param   string $type
     * @param   string|array $options
     * @return  Mage_Core_Model_App
     */
    public static function app($code = '', $type = 'store', $options=array())
    {
        if (null === self::$_app) {
            Varien_Profiler::start('mage::app::construct');
            self::$_app = new Mage_Core_Model_App();
            Varien_Profiler::stop('mage::app::construct');

            Mage::setRoot();
            Mage::register('events', new Varien_Event_Collection());


            Varien_Profiler::start('mage::app::register_config');
            Mage::register('config', new Mage_Core_Model_Config());
            Varien_Profiler::stop('mage::app::register_config');

            Varien_Profiler::start('mage::app::init');
            self::$_app->init($code, $type, $options);
            Varien_Profiler::stop('mage::app::init');

            self::$_app->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
        }
        return self::$_app;
    }

    /**
     * Front end main entry point
     *
     * @param string $code
     * @param string $type
     * @param string|array $options
     */
    public static function run($code = '', $type = 'store', $options=array())
    {
        try {
            Varien_Profiler::start('mage');

            Varien_Profiler::start('mage::app');
            self::app($code, $type, $options);
            Varien_Profiler::stop('mage::app');

            Varien_Profiler::start('mage::dispatch');
            self::app()->getFrontController()->dispatch();
            Varien_Profiler::stop('mage::dispatch');

            Varien_Profiler::stop('mage');
        }
        catch (Mage_Core_Model_Session_Exception $e) {
            header('Location: ' . Mage::getBaseUrl());
            die();
        }
        catch (Mage_Core_Model_Store_Exception $e) {
            $baseUrl = self::getScriptSystemUrl('404');
            if (!headers_sent()) {
                header('Location: ' . rtrim($baseUrl, '/').'/404/');
            }
            else {
                print '<script type="text/javascript">';
                print "window.location.href = '{$baseUrl}';";
                print '</script>';
            }
            die();
        }
        catch (Exception $e) {
            if (self::isInstalled() || self::$_isDownloader) {
                self::printException($e);
                exit();
            }
            try {
                self::dispatchEvent('mage_run_exception', array('exception' => $e));
                if (!headers_sent()) {
                    header('Location:'.self::getUrl('install'));
                }
                else {
                    self::printException($e);
                }
            }
            catch (Exception $ne) {
                self::printException($ne, $e->getMessage());
            }
        }
    }

    /**
     * Retrieve application installation flag
     *
     * @param string|array $options
     * @return bool
     */
    public static function isInstalled($options = array())
    {
        $isInstalled = self::registry('_is_installed');
        if ($isInstalled === null) {
            self::setRoot();

            if (is_string($options)) {
                $options = array(
                    'etc_dir' => $options
                );
            }
            $etcDir = 'etc';
            if (!empty($options['etc_dir'])) {
                $etcDir = $options['etc_dir'];
            }
            $localConfigFile = self::getRoot() . DS . $etcDir . DS . 'local.xml';

            $isInstalled = false;

            if (is_readable($localConfigFile)) {
                $localConfig = simplexml_load_file($localConfigFile);
                date_default_timezone_set('UTC');
                if (($date = $localConfig->global->install->date) && strtotime($date)) {
                    $isInstalled = true;
                }
            }
            self::register('_is_installed', $isInstalled);
        }
        return $isInstalled;
    }

    /**
     * log facility (??)
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     */
    public static function log($message, $level=null, $file = '')
    {
        if (!self::getConfig()) {
            return;
        }
        if (!Mage::getStoreConfig('dev/log/active')) {
            return;
        }

        static $loggers = array();

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;
        if (empty($file)) {
            $file = Mage::getStoreConfig('dev/log/file');
            $file   = empty($file) ? 'system.log' : $file;
        }

        try {
            if (!isset($loggers[$file])) {
                $logFile = Mage::getBaseDir('var').DS.'log'.DS.$file;
                $logDir = Mage::getBaseDir('var').DS.'log';

                if (!is_dir(Mage::getBaseDir('var').DS.'log')) {
                    mkdir(Mage::getBaseDir('var').DS.'log', 0777);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile,'');
                    chmod($logFile, 0777);
                }

                $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $formatter = new Zend_Log_Formatter_Simple($format);
                $writer = new Zend_Log_Writer_Stream($logFile);
                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e){

        }
    }

    public static function logException(Exception $e)
    {
        if (!self::getConfig()) {
            return;
        }
        $file = Mage::getStoreConfig('dev/log/exception_file');
        self::log("\n".(string)$e, Zend_Log::ERR, $file);
    }

    /**
     * Set enabled developer mode
     *
     * @param bool $mode
     * @return bool
     */
    public static function setIsDeveloperMode($mode)
    {
        self::$_isDeveloperMode = (bool)$mode;
        return self::$_isDeveloperMode;
    }

    /**
     * Retrieve enabled developer mode
     *
     * @return bool
     */
    public static function getIsDeveloperMode()
    {
        return self::$_isDeveloperMode;
    }

    /**
     * Display exception
     *
     * @param Exception $e
     */
    public static function printException(Exception $e, $extra = '')
    {
        if (self::$_isDeveloperMode) {
            print '<pre>';

            if (!empty($extra)) {
                print $extra . "\n\n";
            }

            print $e->getMessage() . "\n\n";
            print $e->getTraceAsString();
            print '</pre>';
        }
        else {
            self::getConfig()->createDirIfNotExists(self::getBaseDir('var') . DS . 'report');
            $reportId   = intval(microtime(true) * rand(100, 1000));
            $reportFile = self::getBaseDir('var') . DS . 'report' . DS . $reportId;
            $reportData = array(
                !empty($extra) ? $extra . "\n\n" : '' . $e->getMessage(),
                $e->getTraceAsString()
            );
            $reportData = serialize($reportData);

            file_put_contents($reportFile, $reportData);
            chmod($reportFile, 0777);

            $storeCode = 'default';
            try {
                $storeCode = self::app()->getStore()->getCode();
            }
            catch (Exception $e) {}

            $baseUrl = self::getScriptSystemUrl('report', true);
            $reportUrl = rtrim($baseUrl, '/') . '/report/?id='
            . $reportId . '&s=' . $storeCode;

            if (!headers_sent()) {
                header('Location: ' . $reportUrl);
            }
            else {
                print '<script type="text/javascript">';
                print "window.location.href = '{$reportUrl}';";
                print '</script>';
            }
        }

        die();
    }

    /**
     * Define system folder directory url by virtue of running script directory name
     * Try to find requested folder by shifting to domain root directory
     *
     * @param   string  $folder
     * @param   boolean $exitIfNot
     * @return  string
     */
    public static function getScriptSystemUrl($folder, $exitIfNot = false)
    {
        $runDirUrl  = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        $runDir     = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), DS);

        $baseUrl    = null;
        if (is_dir($runDir.'/'.$folder)) {
            $baseUrl = str_replace(DS, '/', $runDirUrl);
        } else {
            $runDirUrlArray = explode('/', $runDirUrl);
            $runDirArray = explode('/', $runDir);
            $count       = count($runDirArray);

            for ($i=0; $i < $count; $i++) {
                array_pop($runDirUrlArray);
                array_pop($runDirArray);
                $_runDir = implode('/', $runDirArray);
                if (!empty($_runDir)) {
                    $_runDir .= '/';
                }

                if (is_dir($_runDir.$folder)) {
                    $_runDirUrl = implode('/', $runDirUrlArray);
                    $baseUrl = str_replace(DS, '/', $_runDirUrl);
                    break;
                }
            }
        }

        if (is_null($baseUrl)) {
            $errorMessage = "Unable detect system directory: $folder";
            if ($exitIfNot) {
                // exit because of infinity loop
                exit($errorMessage);
            } else {
                self::printException(new Exception(), $errorMessage);
            }
        }

        return $baseUrl;
    }

    public static function setIsDownloader($flag=true)
    {
        self::$_isDownloader = $flag;
    }
}
