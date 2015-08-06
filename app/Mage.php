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
 * @package     Mage
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

Mage::register('original_include_path', get_include_path());

if (defined('COMPILER_INCLUDE_PATH')) {
    $appPath = COMPILER_INCLUDE_PATH;
    set_include_path($appPath . PS . Mage::registry('original_include_path'));
    include_once COMPILER_INCLUDE_PATH . DS . "Mage_Core_functions.php";
    include_once COMPILER_INCLUDE_PATH . DS . "Varien_Autoload.php";
} else {
    /**
     * Set include path
     */
    $paths = array();
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
    $paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
    $paths[] = BP . DS . 'lib';

    $appPath = implode(PS, $paths);
    set_include_path($appPath . PS . Mage::registry('original_include_path'));
    include_once "Mage/Core/functions.php";
    include_once "Varien/Autoload.php";
}

Varien_Autoload::register();

/**
 * Main Mage hub class
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
final class Mage
{
    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry                   = array();

    /**
     * Application root absolute path
     *
     * @var string
     */
    static private $_appRoot;

    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    static private $_app;

    /**
     * Config Model
     *
     * @var Mage_Core_Model_Config
     */
    static private $_config;

    /**
     * Event Collection Object
     *
     * @var Varien_Event_Collection
     */
    static private $_events;

    /**
     * Object cache instance
     *
     * @var Varien_Object_Cache
     */
    static private $_objects;

    /**
     * Is downloader flag
     *
     * @var bool
     */
    static private $_isDownloader               = false;

    /**
     * Is developer mode flag
     *
     * @var bool
     */
    static private $_isDeveloperMode            = false;

    /**
     * Is allow throw Exception about headers already sent
     *
     * @var bool
     */
    public static $headersSentThrowsException   = true;

    /**
     * Is installed flag
     *
     * @var bool
     */
    static private $_isInstalled;

    /**
     * Magento edition constants
     */
    const EDITION_COMMUNITY    = 'Community';
    const EDITION_ENTERPRISE   = 'Enterprise';
    const EDITION_PROFESSIONAL = 'Professional';
    const EDITION_GO           = 'Go';

    /**
     * Current Magento edition.
     *
     * @var string
     * @static
     */
    static private $_currentEdition = self::EDITION_COMMUNITY;

    /**
     * Gets the current Magento version string
     * @link http://www.magentocommerce.com/blog/new-community-edition-release-process/
     *
     * @return string
     */
    public static function getVersion()
    {
        $i = self::getVersionInfo();
        return trim("{$i['major']}.{$i['minor']}.{$i['revision']}" . ($i['patch'] != '' ? ".{$i['patch']}" : "")
                        . "-{$i['stability']}{$i['number']}", '.-');
    }

    /**
     * Gets the detailed Magento version information
     * @link http://www.magentocommerce.com/blog/new-community-edition-release-process/
     *
     * @return array
     */
    public static function getVersionInfo()
    {
        return array(
            'major'     => '1',
            'minor'     => '9',
            'revision'  => '2',
            'patch'     => '1',
            'stability' => '',
            'number'    => '',
        );
    }

    /**
     * Get current Magento edition
     *
     * @static
     * @return string
     */
    public static function getEdition()
    {
       return self::$_currentEdition;
    }

    /**
     * Set all my static data to defaults
     *
     */
    public static function reset()
    {
        self::$_registry        = array();
        self::$_appRoot         = null;
        self::$_app             = null;
        self::$_config          = null;
        self::$_events          = null;
        self::$_objects         = null;
        self::$_isDownloader    = false;
        self::$_isDeveloperMode = false;
        self::$_isInstalled     = null;
        // do not reset $headersSentThrowsException
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws Mage_Core_Exception
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            self::throwException('Mage registry key "'.$key.'" already exists');
        }
        self::$_registry[$key] = $value;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
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
     * @throws Mage_Core_Exception
     */
    public static function setRoot($appRoot = '')
    {
        if (self::$_appRoot) {
            return ;
        }

        if ('' === $appRoot) {
            // automagically find application root by dirname of Mage.php
            $appRoot = dirname(__FILE__);
        }

        $appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            self::$_appRoot = $appRoot;
        } else {
            self::throwException($appRoot . ' is not a directory or not readable by this user');
        }
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     */
    public static function getRoot()
    {
        return self::$_appRoot;
    }

    /**
     * Retrieve Events Collection
     *
     * @return Varien_Event_Collection $collection
     */
    public static function getEvents()
    {
        return self::$_events;
    }

    /**
     * Varien Objects Cache
     *
     * @param string $key optional, if specified will load this key
     * @return Varien_Object_Cache
     */
    public static function objects($key = null)
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
     * @param string $type
     * @return string
     */
    public static function getBaseDir($type = 'base')
    {
        return self::getConfig()->getOptions()->getDir($type);
    }

    /**
     * Retrieve module absolute path by directory type
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public static function getModuleDir($type, $moduleName)
    {
        return self::getConfig()->getModuleDir($type, $moduleName);
    }

    /**
     * Retrieve config value for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public static function getStoreConfig($path, $store = null)
    {
        return self::app()->getStore($store)->getConfig($path);
    }

    /**
     * Retrieve config flag for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return bool
     */
    public static function getStoreConfigFlag($path, $store = null)
    {
        $flag = strtolower(self::getStoreConfig($path, $store));
        if (!empty($flag) && 'false' !== $flag) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @param null|bool $secure
     * @return string
     */
    public static function getBaseUrl($type = Mage_Core_Model_Store::URL_TYPE_LINK, $secure = null)
    {
        return self::app()->getStore()->getBaseUrl($type, $secure);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public static function getUrl($route = '', $params = array())
    {
        return self::getModel('core/url')->getUrl($route, $params);
    }

    /**
     * Get design package singleton
     *
     * @return Mage_Core_Model_Design_Package
     */
    public static function getDesign()
    {
        return self::getSingleton('core/design_package');
    }

    /**
     * Retrieve a config instance
     *
     * @return Mage_Core_Model_Config
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, $data = array(), $observerName = '', $observerClass = '')
    {
        if ($observerClass == '') {
            $observerClass = 'Varien_Event_Observer';
        }
        $observer = new $observerClass();
        $observer->setName($observerName)->addData($data)->setEventName($eventName)->setCallback($callback);
        return self::getEvents()->addObserver($observer);
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param string $name
     * @param array $data
     * @return Mage_Core_Model_App
     */
    public static function dispatchEvent($name, array $data = array())
    {
        Varien_Profiler::start('DISPATCH EVENT:'.$name);
        $result = self::app()->dispatchEvent($name, $data);
        Varien_Profiler::stop('DISPATCH EVENT:'.$name);
        return $result;
    }

    /**
     * Retrieve model object
     *
     * @link    Mage_Core_Model_Config::getModelInstance
     * @param   string $modelClass
     * @param   array|object $arguments
     * @return  Mage_Core_Model_Abstract|false
     */
    public static function getModel($modelClass = '', $arguments = array())
    {
        return self::getConfig()->getModelInstance($modelClass, $arguments);
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
        if (!self::registry($registryKey)) {
            self::register($registryKey, self::getModel($modelClass, $arguments));
        }
        return self::registry($registryKey);
    }

    /**
     * Retrieve object of resource model
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Object
     */
    public static function getResourceModel($modelClass, $arguments = array())
    {
        return self::getConfig()->getResourceModelInstance($modelClass, $arguments);
    }

    /**
     * Retrieve Controller instance by ClassName
     *
     * @param string $class
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param array $invokeArgs
     * @return Mage_Core_Controller_Front_Action
     */
    public static function getControllerInstance($class, $request, $response, array $invokeArgs = array())
    {
        return new $class($request, $response, $invokeArgs);
    }

    /**
     * Retrieve resource vodel object singleton
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  object
     */
    public static function getResourceSingleton($modelClass = '', array $arguments = array())
    {
        $registryKey = '_resource_singleton/'.$modelClass;
        if (!self::registry($registryKey)) {
            self::register($registryKey, self::getResourceModel($modelClass, $arguments));
        }
        return self::registry($registryKey);
    }

    /**
     * Deprecated, use self::helper()
     *
     * @param string $type
     * @return object
     */
    public static function getBlockSingleton($type)
    {
        $action = self::app()->getFrontController()->getAction();
        return $action ? $action->getLayout()->getBlockSingleton($type) : false;
    }

    /**
     * Retrieve helper object
     *
     * @param string $name the helper name
     * @return Mage_Core_Helper_Abstract
     */
    public static function helper($name)
    {
        $registryKey = '_helper/' . $name;
        if (!self::registry($registryKey)) {
            $helperClass = self::getConfig()->getHelperClassName($name);
            self::register($registryKey, new $helperClass);
        }
        return self::registry($registryKey);
    }

    /**
     * Retrieve resource helper object
     *
     * @param string $moduleName
     * @return Mage_Core_Model_Resource_Helper_Abstract
     */
    public static function getResourceHelper($moduleName)
    {
        $registryKey = '_resource_helper/' . $moduleName;
        if (!self::registry($registryKey)) {
            $helperClass = self::getConfig()->getResourceHelper($moduleName);
            self::register($registryKey, $helperClass);
        }

        return self::registry($registryKey);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param integer $code
     * @return Mage_Core_Exception
     */
    public static function exception($module = 'Mage_Core', $message = '', $code = 0)
    {
        $className = $module . '_Exception';
        return new $className($message, $code);
    }

    /**
     * Throw Exception
     *
     * @param string $message
     * @param string $messageStorage
     * @throws Mage_Core_Exception
     */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Mage_Core_Exception($message);
    }

    /**
     * Get initialized application object.
     *
     * @param string $code
     * @param string $type
     * @param string|array $options
     * @return Mage_Core_Model_App
     */
    public static function app($code = '', $type = 'store', $options = array())
    {
        if (null === self::$_app) {
            self::$_app = new Mage_Core_Model_App();
            self::setRoot();
            self::$_events = new Varien_Event_Collection();
            self::_setIsInstalled($options);
            self::_setConfigModel($options);

            Varien_Profiler::start('self::app::init');
            self::$_app->init($code, $type, $options);
            Varien_Profiler::stop('self::app::init');
            self::$_app->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
        }
        return self::$_app;
    }

    /**
     * @static
     * @param string $code
     * @param string $type
     * @param array $options
     * @param string|array $modules
     */
    public static function init($code = '', $type = 'store', $options = array(), $modules = array())
    {
        try {
            self::setRoot();
            self::$_app     = new Mage_Core_Model_App();
            self::_setIsInstalled($options);
            self::_setConfigModel($options);

            if (!empty($modules)) {
                self::$_app->initSpecified($code, $type, $options, $modules);
            } else {
                self::$_app->init($code, $type, $options);
            }
        } catch (Mage_Core_Model_Session_Exception $e) {
            header('Location: ' . self::getBaseUrl());
            die;
        } catch (Mage_Core_Model_Store_Exception $e) {
            require_once(self::getBaseDir() . DS . 'errors' . DS . '404.php');
            die;
        } catch (Exception $e) {
            self::printException($e);
            die;
        }
    }

    /**
     * Front end main entry point
     *
     * @param string $code
     * @param string $type
     * @param string|array $options
     */
    public static function run($code = '', $type = 'store', $options = array())
    {
        try {
            Varien_Profiler::start('mage');
            self::setRoot();
            if (isset($options['edition'])) {
                self::$_currentEdition = $options['edition'];
            }
            self::$_app    = new Mage_Core_Model_App();
            if (isset($options['request'])) {
                self::$_app->setRequest($options['request']);
            }
            if (isset($options['response'])) {
                self::$_app->setResponse($options['response']);
            }
            self::$_events = new Varien_Event_Collection();
            self::_setIsInstalled($options);
            self::_setConfigModel($options);
            self::$_app->run(array(
                'scope_code' => $code,
                'scope_type' => $type,
                'options'    => $options,
            ));
            Varien_Profiler::stop('mage');
        } catch (Mage_Core_Model_Session_Exception $e) {
            header('Location: ' . self::getBaseUrl());
            die();
        } catch (Mage_Core_Model_Store_Exception $e) {
            require_once(self::getBaseDir() . DS . 'errors' . DS . '404.php');
            die();
        } catch (Exception $e) {
            if (self::isInstalled() || self::$_isDownloader) {
                self::printException($e);
                exit();
            }
            try {
                self::dispatchEvent('mage_run_exception', array('exception' => $e));
                if (!headers_sent() && self::isInstalled()) {
                    header('Location:' . self::getUrl('install'));
                } else {
                    self::printException($e);
                }
            } catch (Exception $ne) {
                self::printException($ne, $e->getMessage());
            }
        }
    }

    /**
     * Set application isInstalled flag based on given options
     *
     * @param array $options
     */
    protected static function _setIsInstalled($options = array())
    {
        if (isset($options['is_installed']) && $options['is_installed']) {
            self::$_isInstalled = true;
        }
    }

    /**
     * Set application Config model
     *
     * @param array $options
     */
    protected static function _setConfigModel($options = array())
    {
        if (isset($options['config_model']) && class_exists($options['config_model'])) {
            $alternativeConfigModelName = $options['config_model'];
            unset($options['config_model']);
            $alternativeConfigModel = new $alternativeConfigModelName($options);
        } else {
            $alternativeConfigModel = null;
        }

        if (!is_null($alternativeConfigModel) && ($alternativeConfigModel instanceof Mage_Core_Model_Config)) {
            self::$_config = $alternativeConfigModel;
        } else {
            self::$_config = new Mage_Core_Model_Config($options);
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
        if (self::$_isInstalled === null) {
            self::setRoot();

            if (is_string($options)) {
                $options = array('etc_dir' => $options);
            }
            $etcDir = self::getRoot() . DS . 'etc';
            if (!empty($options['etc_dir'])) {
                $etcDir = $options['etc_dir'];
            }
            $localConfigFile = $etcDir . DS . 'local.xml';

            self::$_isInstalled = false;

            if (is_readable($localConfigFile)) {
                $localConfig = simplexml_load_file($localConfigFile);
                date_default_timezone_set('UTC');
                if (($date = $localConfig->global->install->date) && strtotime($date)) {
                    self::$_isInstalled = true;
                }
            }
        }
        return self::$_isInstalled;
    }

    /**
     * log facility (??)
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param bool $forceLog
     */
    public static function log($message, $level = null, $file = '', $forceLog = false)
    {
        if (!self::getConfig()) {
            return;
        }

        try {
            $logActive = self::getStoreConfig('dev/log/active');
            if (empty($file)) {
                $file = self::getStoreConfig('dev/log/file');
            }
        }
        catch (Exception $e) {
            $logActive = true;
        }

        if (!self::$_isDeveloperMode && !$logActive && !$forceLog) {
            return;
        }

        static $loggers = array();

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;
        $file = empty($file) ? 'system.log' : $file;

        try {
            if (!isset($loggers[$file])) {
                $logDir  = self::getBaseDir('var') . DS . 'log';
                $logFile = $logDir . DS . $file;

                if (!is_dir($logDir)) {
                    mkdir($logDir);
                    chmod($logDir, 0750);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0640);
                }

                $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $formatter = new Zend_Log_Formatter_Simple($format);
                $writerModel = (string)self::getConfig()->getNode('global/log/core/writer_model');
                if (!self::$_app || !$writerModel) {
                    $writer = new Zend_Log_Writer_Stream($logFile);
                }
                else {
                    $writer = new $writerModel($logFile);
                }
                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e) {
        }
    }

    /**
     * Write exception to log
     *
     * @param Exception $e
     */
    public static function logException(Exception $e)
    {
        if (!self::getConfig()) {
            return;
        }
        $file = self::getStoreConfig('dev/log/exception_file');
        self::log("\n" . $e->__toString(), Zend_Log::ERR, $file);
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
        } else {

            $reportData = array(
                !empty($extra) ? $extra . "\n\n" : '' . $e->getMessage(),
                $e->getTraceAsString()
            );

            // retrieve server data
            if (isset($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $reportData['url'] = $_SERVER['REQUEST_URI'];
                }
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                }
            }

            // attempt to specify store as a skin
            try {
                $storeCode = self::app()->getStore()->getCode();
                $reportData['skin'] = $storeCode;
            }
            catch (Exception $e) {}

            require_once(self::getBaseDir() . DS . 'errors' . DS . 'report.php');
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
            $runDirArray    = explode('/', $runDir);
            $count          = count($runDirArray);

            for ($i=0; $i < $count; $i++) {
                array_pop($runDirUrlArray);
                array_pop($runDirArray);
                $_runDir = implode('/', $runDirArray);
                if (!empty($_runDir)) {
                    $_runDir .= '/';
                }

                if (is_dir($_runDir.$folder)) {
                    $_runDirUrl = implode('/', $runDirUrlArray);
                    $baseUrl    = str_replace(DS, '/', $_runDirUrl);
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

    /**
     * Set is downloader flag
     *
     * @param bool $flag
     */
    public static function setIsDownloader($flag = true)
    {
        self::$_isDownloader = $flag;
    }
}
