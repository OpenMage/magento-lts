<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage
 */

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);

define('BP', dirname(__DIR__));

Mage::register('original_include_path', get_include_path());

if (!empty($_SERVER['MAGE_IS_DEVELOPER_MODE']) || !empty($_ENV['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
    ini_set('display_errors', '1');
    ini_set('error_prepend_string', '<pre>');
    ini_set('error_append_string', '</pre>');
}

/**
 * Set include path
 */
$paths = [];
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . Mage::registry('original_include_path'));
include_once 'Mage/Core/functions.php';
include_once 'Varien/Autoload.php';

Varien_Autoload::register();

/** AUTOLOADER PATCH **/
$autoloaderPath = getenv('COMPOSER_VENDOR_PATH');
if (!$autoloaderPath) {
    $autoloaderPath = dirname(BP) . DS . 'vendor';
    if (!is_dir($autoloaderPath)) {
        $autoloaderPath = BP . DS . 'vendor';
    }
}

require_once $autoloaderPath . DS . 'autoload.php';
/** AUTOLOADER PATCH **/

/* Support additional includes, such as composer's vendor/autoload.php files */
foreach (glob(BP . DS . 'app' . DS . 'etc' . DS . 'includes' . DS . '*.php') as $path) {
    include_once $path;
}

/**
 * Main Mage hub class
 */
final class Mage
{
    /**
     * Registry collection
     *
     * @var array
     */
    private static $_registry = [];

    /**
     * Application root absolute path
     *
     * @var string|null
     */
    private static $_appRoot;

    /**
     * Application model
     *
     * @var Mage_Core_Model_App|null
     */
    private static $_app;

    /**
     * Config Model
     *
     * @var Mage_Core_Model_Config|null
     */
    private static $_config;

    /**
     * Event Collection Object
     *
     * @var Varien_Event_Collection|null
     */
    private static $_events;

    /**
     * Object cache instance
     *
     * @var Varien_Object_Cache|null
     */
    private static $_objects;

    /**
     * Is developer mode flag
     *
     * @var bool
     */
    private static $_isDeveloperMode = false;

    /**
     * Is allow throw Exception about headers already sent
     *
     * @var bool
     */
    public static $headersSentThrowsException = true;

    /**
     * Is installed flag
     *
     * @var bool|null
     */
    private static $_isInstalled;

    /**
     * Magento edition constants
     */
    public const EDITION_COMMUNITY    = 'Community';

    public const EDITION_ENTERPRISE   = 'Enterprise';

    public const EDITION_PROFESSIONAL = 'Professional';

    public const EDITION_GO           = 'Go';

    /**
     * Current Magento edition.
     *
     * @var string
     * @static
     */
    private static $_currentEdition = self::EDITION_COMMUNITY;

    /**
     * Gets the current Magento version string
     *
     * @return string
     */
    public static function getVersion()
    {
        $i = self::getVersionInfo();
        return trim("{$i['major']}.{$i['minor']}.{$i['revision']}" . ($i['patch'] != '' ? ".{$i['patch']}" : '')
                        . "-{$i['stability']}{$i['number']}", '.-');
    }

    /**
     * Gets the detailed Magento version information
     *
     * @return array
     * @deprecated
     */
    public static function getVersionInfo()
    {
        return [
            'major'     => '1',
            'minor'     => '9',
            'revision'  => '4',
            'patch'     => '5',
            'stability' => '',
            'number'    => '',
        ];
    }

    /**
     * Gets the current OpenMage version string
     * @link https://openmage.github.io/supported-versions.html
     * @link https://semver.org/
     */
    public static function getOpenMageVersion(): string
    {
        $info = self::getOpenMageVersionInfo();
        $versionString = "{$info['major']}.{$info['minor']}.{$info['patch']}";

        if ($info['stability'] && $info['number']) {
            return "{$versionString}-{$info['stability']}.{$info['number']}";
        }

        if ($info['stability']) {
            return "{$versionString}-{$info['stability']}";
        }

        if ($info['number']) {
            return "{$versionString}-{$info['number']}";
        }

        return $versionString;
    }

    /**
     * Gets the detailed OpenMage version information
     * @link https://openmage.github.io/supported-versions.html
     * @link https://semver.org/
     */
    public static function getOpenMageVersionInfo(): array
    {
        /**
         * This code construct is to make merging for forward porting of changes easier.
         * By having the version numbers of different branches in own lines, they do not provoke a merge conflict
         * also as releases are usually done together, this could in theory be done at once.
         * The major Version then needs to be only changed once per branch.
         */
        if (self::getOpenMageMajorVersion() === 20) {
            return [
                'major'     => '20',
                'minor'     => '15',
                'patch'     => '0',
                'stability' => '', // beta,alpha,rc
                'number'    => '', // 1,2,3,0.3.7,x.7.z.92 @see https://semver.org/#spec-item-9
            ];
        }

        return [
            'major'     => '19',
            'minor'     => '5',
            'patch'     => '3',
            'stability' => '', // beta,alpha,rc
            'number'    => '', // 1,2,3,0.3.7,x.7.z.92 @see https://semver.org/#spec-item-9
        ];
    }

    /**
     * @return int<19,20>
     */
    public static function getOpenMageMajorVersion(): int
    {
        return 20;
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
        self::$_registry        = [];
        self::$_appRoot         = null;
        self::$_app             = null;
        self::$_config          = null;
        self::$_events          = null;
        self::$_objects         = null;
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

            self::throwException("Mage registry key $key already exists");
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
        return self::$_registry[$key] ?? null;
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

        if ($appRoot === '') {
            // automagically find application root by __DIR__ constant of Mage.php
            $appRoot = __DIR__;
        }

        $appRoot = realpath($appRoot);

        if (is_dir($appRoot) && is_readable($appRoot)) {
            self::$_appRoot = $appRoot;
        } else {
            self::throwException("$appRoot is not a directory or not readable by this user");
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
            self::$_objects = new Varien_Object_Cache();
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
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return mixed
     */
    public static function getStoreConfig($path, $store = null)
    {
        return self::app()->getStore($store)->getConfig($path);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public static function getStoreConfigAsFloat(string $path, $store = null): float
    {
        return (float) self::getStoreConfig($path, $store);
    }

    /**
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     */
    public static function getStoreConfigAsInt(string $path, $store = null): int
    {
        return (int) self::getStoreConfig($path, $store);
    }

    /**
     * Retrieve config flag for store by path
     *
     * @param string $path
     * @param null|string|bool|int|Mage_Core_Model_Store $store
     * @return bool
     */
    public static function getStoreConfigFlag($path, $store = null)
    {
        $flag = self::getStoreConfig($path, $store);
        $flag = is_string($flag) ? strtolower($flag) : $flag;
        if (!empty($flag) && $flag !== 'false') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get base URL path by type
     *
     * @param Mage_Core_Model_Store::URL_TYPE_* $type
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
     * @param   null|string $route
     * @param   array $params
     * @return  string
     */
    public static function getUrl($route = '', $params = [])
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
     * @return Mage_Core_Model_Config|null
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    /**
     * Add observer to events object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $data
     * @param string $observerName
     * @param string $observerClass
     * @return Varien_Event_Collection
     */
    public static function addObserver($eventName, $callback, $data = [], $observerName = '', $observerClass = '')
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
     * @return Mage_Core_Model_App
     */
    public static function dispatchEvent($name, array $data = [])
    {
        Varien_Profiler::start('DISPATCH EVENT:' . $name);
        $result = self::app()->dispatchEvent($name, $data);
        Varien_Profiler::stop('DISPATCH EVENT:' . $name);
        return $result;
    }

    /**
     * Retrieve model object
     *
     * @link    Mage_Core_Model_Config::getModelInstance
     * @param   string $modelClass
     * @param   array|string|object $arguments
     * @return  Mage_Core_Model_Abstract|false
     */
    public static function getModel($modelClass = '', $arguments = [])
    {
        return self::getConfig()->getModelInstance($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param   string $modelClass
     * @return  Mage_Core_Model_Abstract|false
     */
    public static function getSingleton($modelClass = '', array $arguments = [])
    {
        $registryKey = '_singleton/' . $modelClass;
        if (!isset(self::$_registry[$registryKey])) {
            self::register($registryKey, self::getModel($modelClass, $arguments));
        }

        return self::$_registry[$registryKey];
    }

    /**
     * Retrieve object of resource model
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Mage_Core_Model_Resource_Db_Collection_Abstract|false
     */
    public static function getResourceModel($modelClass, $arguments = [])
    {
        return self::getConfig()->getResourceModelInstance($modelClass, $arguments);
    }

    /**
     * Retrieve Controller instance by ClassName
     *
     * @param string $class
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_Core_Controller_Front_Action
     */
    public static function getControllerInstance($class, $request, $response, array $invokeArgs = [])
    {
        return new $class($request, $response, $invokeArgs);
    }

    /**
     * Retrieve resource model object singleton
     *
     * @param   string $modelClass
     * @return  object
     */
    public static function getResourceSingleton($modelClass = '', array $arguments = [])
    {
        $registryKey = '_resource_singleton/' . $modelClass;
        if (!isset(self::$_registry[$registryKey])) {
            self::register($registryKey, self::getResourceModel($modelClass, $arguments));
        }

        return self::$_registry[$registryKey];
    }

    /**
     * Retrieve block object
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract|false
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
        if (!isset(self::$_registry[$registryKey])) {
            $helperClass = self::getConfig()->getHelperClassName($name);
            self::register($registryKey, new $helperClass());
        }

        return self::$_registry[$registryKey];
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
        if (!isset(self::$_registry[$registryKey])) {
            $helperClass = self::getConfig()->getResourceHelper($moduleName);
            self::register($registryKey, $helperClass);
        }

        return self::$_registry[$registryKey];
    }

    /**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param int $code
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
    public static function app($code = '', $type = 'store', $options = [])
    {
        if (self::$_app === null) {
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
    public static function init($code = '', $type = 'store', $options = [], $modules = [])
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
        } catch (Mage_Core_Model_Session_Exception) {
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
    public static function run($code = '', $type = 'store', $options = [])
    {
        try {
            Varien_Profiler::start('mage');
            self::setRoot();
            if (isset($options['edition'])) {
                self::$_currentEdition = $options['edition'];
            }

            self::$_app = new Mage_Core_Model_App();
            if (isset($options['request'])) {
                self::$_app->setRequest($options['request']);
            }

            if (isset($options['response'])) {
                self::$_app->setResponse($options['response']);
            }

            self::$_events = new Varien_Event_Collection();
            self::_setIsInstalled($options);
            self::_setConfigModel($options);
            self::$_app->run([
                'scope_code' => $code,
                'scope_type' => $type,
                'options'    => $options,
            ]);
            Varien_Profiler::stop('mage');
        } catch (Mage_Core_Model_Session_Exception) {
            header('Location: ' . self::getBaseUrl());
            die();
        } catch (Mage_Core_Model_Store_Exception $e) {
            require_once(self::getBaseDir() . DS . 'errors' . DS . '404.php');
            die();
        } catch (Exception $e) {
            if (self::isInstalled()) {
                self::dispatchEvent('mage_run_installed_exception', ['exception' => $e]);
                self::printException($e);
                exit();
            }

            try {
                self::dispatchEvent('mage_run_exception', ['exception' => $e]);
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
    private static function _setIsInstalled($options = [])
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
    private static function _setConfigModel($options = [])
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
    public static function isInstalled($options = [])
    {
        if (self::$_isInstalled === null) {
            self::setRoot();

            if (is_string($options)) {
                $options = ['etc_dir' => $options];
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
                if (($date = $localConfig->global->install->date) && strtotime((string) $date)) {
                    self::$_isInstalled = true;
                }
            }
        }

        return self::$_isInstalled;
    }

    /**
     * log facility (??)
     *
     * @param array|object|string $message
     * @param int $level
     * @param string|null $file
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
        } catch (Exception) {
            $logActive = true;
        }

        if (!self::$_isDeveloperMode && !$logActive && !$forceLog) {
            return;
        }

        static $loggers = [];

        try {
            $maxLogLevel = (int) self::getStoreConfig('dev/log/max_level');
        } catch (Throwable) {
            $maxLogLevel = Zend_Log::DEBUG;
        }

        $level  = is_null($level) ? Zend_Log::DEBUG : $level;

        if (!self::$_isDeveloperMode && $level > $maxLogLevel && !$forceLog) {
            return;
        }

        $file = empty($file) ?
            (string) self::getConfig()->getNode('dev/log/file', Mage_Core_Model_Store::DEFAULT_CODE) : basename($file);

        try {
            if (!isset($loggers[$file])) {
                // Validate file extension before save. Allowed file extensions: log, txt, html, csv
                $_allowedFileExtensions = explode(
                    ',',
                    (string) self::getConfig()->getNode('dev/log/allowedFileExtensions', Mage_Core_Model_Store::DEFAULT_CODE),
                );
                if (! ($extension = pathinfo($file, PATHINFO_EXTENSION)) || ! in_array($extension, $_allowedFileExtensions)) {
                    return;
                }

                $logDir = self::getBaseDir('var') . DS . 'log';
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
                $writerModel = (string) self::getConfig()->getNode('global/log/core/writer_model');
                if (!self::$_app || !$writerModel) {
                    $writer = new Zend_Log_Writer_Stream($logFile);
                } else {
                    $writer = new $writerModel($logFile);
                }

                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $message = addcslashes($message, '<?');
            $loggers[$file]->log($message, $level);
        } catch (Exception) {
        }
    }

    /**
     * Write exception to log
     */
    public static function logException(Throwable $e)
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
        self::$_isDeveloperMode = (bool) $mode;
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
     */
    public static function printException(Throwable $e, $extra = '')
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
            $reportData = [
                (!empty($extra) ? $extra . "\n\n" : '') . $e->getMessage(),
                $e->getTraceAsString(),
            ];

            // retrieve server data
            if (isset($_SERVER['REQUEST_URI'])) {
                $reportData['url'] = $_SERVER['REQUEST_URI'];
            }

            if (isset($_SERVER['SCRIPT_NAME'])) {
                $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
            }

            // attempt to specify store as a skin
            try {
                $storeCode = self::app()->getStore()->getCode();
                $reportData['skin'] = $storeCode;
            } catch (Exception $e) {
            }

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
        if (is_dir($runDir . '/' . $folder)) {
            $baseUrl = str_replace(DS, '/', $runDirUrl);
        } else {
            $runDirUrlArray = explode('/', $runDirUrl);
            $runDirArray    = explode('/', $runDir);
            $count          = count($runDirArray);

            for ($i = 0; $i < $count; $i++) {
                array_pop($runDirUrlArray);
                array_pop($runDirArray);
                $_runDir = implode('/', $runDirArray);
                if (!empty($_runDir)) {
                    $_runDir .= '/';
                }

                if (is_dir($_runDir . $folder)) {
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
}
