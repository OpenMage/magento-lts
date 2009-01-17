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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core configuration class
 *
 * Used to retrieve core configuration values
 *
 * @link       http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Model_Config extends Mage_Core_Model_Config_Base
{
    protected $_useCache;

    protected $_options;

    protected $_classNameCache = array();

    protected $_blockClassNameCache = array();

    protected $_baseDirCache = array();

    protected $_secureUrlCache = array();

    protected $_customEtcDir = null;

    protected $_distroServerVars;

    protected $_substServerVars;

    protected $_resourceModel;

    /**
     * Retrieve resource model
     *
     * @return Mage_Core_Store_Mysql4_Config
     */
    public function getResourceModel()
    {
        if (is_null($this->_resourceModel)) {
            $this->_resourceModel = Mage::getResourceModel('core/config');
        }
        return $this->_resourceModel;
    }

    /**
     * Flag cache for existing or already created directories
     *
     * @var unknown_type
     */
    protected $_dirExists = array();

    /**
     * Enter description here...
     *
     * @param mixed $sourceData
     */
    public function __construct($sourceData=null)
    {
        $this->setCacheId('config_global');
        parent::__construct($sourceData);
    }

    /**
     * Get configuration options
     *
     * @return Mage_Core_Model_Config_Options
     */
    public function getOptions()
    {
        if (!$this->_options) {
            $this->_options = new Mage_Core_Model_Config_Options();
        }
        return $this->_options;
    }

    /**
     * Initialization of core configuration
     *
     * @return Mage_Core_Model_Config
     */
    public function init($options=array())
    {
        $this->setCacheChecksum(null);
        $saveCache = true;

        $this->_options = new Mage_Core_Model_Config_Options($options);

        $etcDir = $this->getOptions()->getEtcDir();

        $this->_customEtcDir = $etcDir;

        $localConfigLoaded = $this->loadFile($etcDir.DS.'local.xml');

        // check if local modules are disabled
        $disableLocalModules = (string)$this->getNode('global/disable_local_modules');
        $disableLocalModules = !empty($disableLocalModules) && (('true' === $disableLocalModules) || ('1' === $disableLocalModules));
        if ($disableLocalModules) {
            /**
             * Reset include path
             */
            $codeDir = $this->getOptions()->getCodeDir();
            $libDir = $this->getOptions()->getLibDir();

            set_include_path(
                // excluded '/app/code/local'
                BP . DS . 'app' . DS . 'code' . DS . 'community' . PS .
                BP . DS . 'app' . DS . 'code' . DS . 'core' . PS .
                BP . DS . 'lib' . PS .
                /**
                 * Problem with concatenate BP . $codeDir
                 */
                /*BP . $codeDir . DS .'community' . PS .
                BP . $codeDir . DS .'core' . PS .
                BP . $libDir . PS .*/
                Mage::registry('original_include_path')
            );
        }

        if (Mage::app()->isInstalled()) {
            if (Mage::app()->useCache('config')) {
                Varien_Profiler::start('config/load-cache');
                $loaded = $this->loadCache();
                Varien_Profiler::stop('config/load-cache');
                if ($loaded) {
                    return $this;
                }
            }
        }

        Varien_Profiler::stop('config/load-cache');

        $mergeConfig = new Mage_Core_Model_Config_Base();

        /**
         * Load base configuration data
         */
        Varien_Profiler::start('config/load-base');

        $configFile = $etcDir.DS.'config.xml';
        $this->loadFile($configFile);

        Varien_Profiler::stop('config/load-base');

        $this->_loadDeclaredModules($mergeConfig);

        /**
         * Load modules configuration data
         */
        Varien_Profiler::start('config/load-modules');

        $modules = $this->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                    continue;
                }
                $configFile = $this->getModuleDir('etc', $modName).DS.'config.xml';
                if ($mergeConfig->loadFile($configFile)) {
                    $this->extend($mergeConfig, true);
                }
            }
        }

        Varien_Profiler::stop('config/load-modules');

        /**
         * Load local configuration data
         */
        Varien_Profiler::start('config/load-local');

        $configFile = $etcDir.DS.'local.xml';
        if (is_readable($configFile)) {
            $mergeConfig->loadFile($configFile);
            $this->extend($mergeConfig);
        }

        Varien_Profiler::stop('config/load-local');

        Varien_Profiler::start('config/apply-extends');
        $this->applyExtends();
        Varien_Profiler::stop('config/apply-extends');

        /**
         * Load configuration from DB
         */
        if ($localConfigLoaded) {
            Varien_Profiler::start('dbUpdates');
            Mage_Core_Model_Resource_Setup::applyAllUpdates();
            Varien_Profiler::stop('dbUpdates');

            Varien_Profiler::start('config/load-db');
            $dbConf = $this->getResourceModel();
            $dbConf->loadToXml($this);
            Varien_Profiler::stop('config/load-db');
        }

        if (Mage::app()->useCache('config')) {
            Varien_Profiler::start('config/save-cache');
            $this->saveCache(array('config'));
            Varien_Profiler::stop('config/save-cache');
        }

        return $this;
    }

    public function saveCache($tags=array())
    {


        parent::saveCache($tags);

        return $this;
    }

    protected function _loadDeclaredModules($mergeConfig)
    {
        $etcDir = $this->getOptions()->getEtcDir();
        $moduleFiles = glob($etcDir.DS.'modules'.DS.'*.xml');
        if (!$moduleFiles) {
            return;
        }
        Varien_Profiler::start('config/load-modules-declaration');

        $unsortedConfig = new Mage_Core_Model_Config_Base();
        $unsortedConfig->loadString('<config/>');
        $fileConfig = new Mage_Core_Model_Config_Base();

        // load modules declarations
        foreach ($moduleFiles as $file) {
            $fileConfig->loadFile($file);
            $unsortedConfig->extend($fileConfig);
        }

        $unsortedModules = array();
        $sortedModules = array();

        // prepare unsorted modules with links
        foreach ($unsortedConfig->getNode('modules')->children() as $moduleName=>$moduleConfig) {
            if (!isset($unsortedModules[$moduleName])) {
                $unsortedModules[$moduleName] = array();
            }
            if ($moduleConfig->depends) {
                foreach ($moduleConfig->depends->children() as $dependName=>$depend) {
                    $unsortedModules[$moduleName]['parents'][$dependName] = true;
                    if (!isset($unsortedModules[$dependName])) {
                        $unsortedModules[$dependName] = array();
                    }
                    $unsortedModules[$dependName]['children'][$moduleName] = true;
                }
            }
        }

        // sort modules by dependencies
        while (!empty($unsortedModules)) {
            foreach ($unsortedModules as $moduleName=>$module) {
                if (empty($module['parents'])) {
                    $sortedModules[$moduleName] = $unsortedConfig->getNode('modules/'.$moduleName);
                    unset($unsortedModules[$moduleName]);
                    if (!empty($module['children'])) {
                        foreach ($module['children'] as $childName=>$dummy) {
                            unset($unsortedModules[$childName]['parents'][$moduleName]);
                        }
                    }
                    break;
                }
            }
        }

        // add sorted modules to configuration xml
        $sortedConfig = new Mage_Core_Model_Config_Base();
        $sortedConfig->loadString('<config><modules/></config>');
        foreach ($unsortedConfig->getNode()->children() as $nodeName=>$node) {
            if ($nodeName!=='modules') {
                $sortedConfig->getNode()->appendChild($node);
            }
        }
        $modulesConfig = $sortedConfig->getNode('modules');
        foreach ($sortedModules as $moduleName=>$moduleConfig) {
            $modulesConfig->appendChild($moduleConfig);
        }
        $this->extend($sortedConfig);

        Varien_Profiler::stop('config/load-modules-declaration');
    }

    /**
     * Reinitialize configuration
     *
     * @param string $etcDir
     * @return Mage_Core_Model_Config
     */
    public function reinit($options = array())
    {
        $this->removeCache();
        return $this->init($options);
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    public function getCache()
    {
        return Mage::app()->getCache();
    }

    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    protected function _saveCache($data, $id, $tags=array(), $lifetime=false)
    {
        return Mage::app()->saveCache($data, $id, $tags, $lifetime);
    }

    protected function _removeCache($id)
    {
        return Mage::app()->removeCache($id);
    }

    /**
     * Retrieve temporary directory path
     *
     * @return string
     */
    public function getTempVarDir()
    {
        return $this->getOptions()->getVarDir();
//        $dir = dirname(Mage::getRoot()).DS.'var';
//        if (!is_writable($dir)) {
//            $dir = (!empty($_ENV['TMP']) ? $_ENV['TMP'] : DS.'tmp').DS.'magento'.DS.'var';
//        }
//        return $dir;
    }

    public function getDistroServerVars()
    {
        if (!$this->_distroServerVars) {

            if (isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['HTTP_HOST'])) {
                $secure = isset($_SERVER['HTTPS']) || $_SERVER['SERVER_PORT']=='443';
                $scheme = ($secure ? 'https' : 'http') . '://' ;

                $hostArr = explode(':', $_SERVER['HTTP_HOST']);
                $host = $hostArr[0];
                $port = isset($hostArr[1]) && (!$secure && $hostArr[1]!=80 || $secure && $hostArr[1]!=443) ? ':'.$hostArr[1] : '';
                $path = Mage::app()->getRequest()->getBasePath();

                $baseUrl = $scheme.$host.$port.rtrim($path, '/').'/';
            } else {
                $baseUrl = 'http://localhost/';
            }

            $options = $this->getOptions();
            $this->_distroServerVars = array(
                'root_dir'  => $options->getBaseDir(),
                'app_dir'   => $options->getAppDir(),
                'var_dir'   => $options->getVarDir(),
                'base_url'  => $baseUrl,
            );

            foreach ($this->_distroServerVars as $k=>$v) {
                $this->_substServerVars['{{'.$k.'}}'] = $v;
            }
        }
        return $this->_distroServerVars;
    }

    public function substDistroServerVars($data)
    {
        $this->getDistroServerVars();
        return str_replace(
            array_keys($this->_substServerVars),
            array_values($this->_substServerVars),
            $data
        );
    }

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Object
     */
    function getModuleConfig($moduleName='')
    {
        $modules = $this->getNode('modules');
        if (''===$moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
        }
    }

    /**
     * Get module setup class instance.
     *
     * Defaults to Mage_Core_Setup
     *
     * @param string|Varien_Simplexml_Object $module
     * @return object
     */
    function getModuleSetup($module='')
    {
        $className = 'Mage_Core_Setup';
        if (''!==$module) {
            if (is_string($module)) {
                $module = $this->getModuleConfig($module);
            }
            if (isset($module->setup)) {
                $moduleClassName = $module->setup->getClassName();
                if (!empty($moduleClassName)) {
                    $className = $moduleClassName;
                }
            }
        }
        return new $className($module);
    }

    /**
     * Get base filesystem directory. depends on $type
     *
     * If $moduleName is specified retrieves specific value for the module.
     *
     * @deprecated in favor of Mage_Core_Model_Config_Options
     * @todo get global dir config
     * @param string $type
     * @return string
     */
    public function getBaseDir($type='base')
    {
        return $this->getOptions()->getDir($type);
    }

    public function getVarDir($path=null, $type='var')
    {
        $dir = Mage::getBaseDir($type).($path!==null ? DS.$path : '');
        if (!$this->createDirIfNotExists($dir)) {
            return false;
        }
        return $dir;
    }

    public function createDirIfNotExists($dir)
    {
        if (!empty($this->_dirExists[$dir])) {
            return true;
        }
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                return false;
//                throw new Mage_Core_Exception($dir.' is not a directory');
            }
            if (!is_writable($dir)) {
                return false;
//                throw new Mage_Core_Exception($dir.' is not writable');
            }
        } else {
            if (!@mkdir($dir, 0777, true)) {
                return false;
//                throw new Mage_Core_Exception('Unable to create '.$dir);
            }
        }
        $this->_dirExists[$dir] = true;
        return true;
    }

    public function getModuleDir($type, $moduleName)
    {
        $codePool = (string)$this->getModuleConfig($moduleName)->codePool;
        $dir = $this->getOptions()->getCodeDir().DS.$codePool.DS.uc_words($moduleName, DS);

        switch ($type) {
            case 'etc':
                $dir .= DS.'etc';
                break;

            case 'controllers':
                $dir .= DS.'controllers';
                break;

            case 'sql':
                $dir .= DS.'sql';
                break;

            case 'locale':
                $dir .= DS.'locale';
                break;
        }

        $dir = str_replace('/', DS, $dir);

        return $dir;
    }

    /*public function getRouterInstance($routerName='', $singleton=true)
    {
        $routers = $this->getNode('front/routers');
        if (!empty($routerName)) {
            $routerConfig = $routers->$routerName;
        } else {
            foreach ($routers as $routerConfig) {
                if ($routerConfig->is('default')) {
                    break;
                }
            }
        }
        $className = $routerConfig->getClassName();
        $constructArgs = $routerConfig->args;
        if (!$className) {
            $className = 'Mage_Core_Controller_Front_Router';
        }
        if ($singleton) {
            $regKey = '_singleton_router/'.$routerName;
            if (!Mage::registry($regKey)) {
                Mage::register($regKey, new $className($constructArgs));
            }
            return Mage::registry($regKey);
        } else {
            return new $className($constructArgs);
        }
    }*/

    /**
     * Load event observers for an area (front, admin)
     *
     * @param   string $area
     * @return  boolean
     */
    public function loadEventObservers($area)
    {
        if ($events = $this->getNode("$area/events")) {
            $events = $events->children();
        }
        else {
            return false;
        }

        foreach ($events as $event) {
            $eventName = $event->getName();
            $observers = $event->observers->children();
            foreach ($observers as $observer) {
                switch ((string)$observer->type) {
                    case 'singleton':
                        $callback = array(
                            Mage::getSingleton((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    case 'object':
                    case 'model':
                        $callback = array(
                            Mage::getModel((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    default:
                        $callback = array($observer->getClassName(), (string)$observer->method);
                        break;
                }

                $args = (array)$observer->args;
                $observerClass = $observer->observer_class ? (string)$observer->observer_class : '';
                Mage::addObserver($eventName, $callback, $args, $observer->getName(), $observerClass);
            }
        }
        return true;
    }

    /**
     * Get standard path variables.
     *
     * To be used in blocks, templates, etc.
     *
     * @param array|string $args Module name if string
     * @return array
     */
    public function getPathVars($args=null)
    {
        $path = array();

        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSecureUrl'] = Mage::getBaseUrl('link', true);

        return $path;
    }

    /**
     * Retrieve class name by class group
     *
     * @param   string $groupType currently supported model, block, helper
     * @param   string $classId slash separated class identifier, ex. group/class
     * @param   string $groupRootNode optional config path for group config
     * @return  string
     */
    public function getGroupedClassName($groupType, $classId, $groupRootNode=null)
    {
        if (empty($groupRootNode)) {
            $groupRootNode = 'global/'.$groupType.'s';
        }

        $classArr = explode('/', trim($classId));
        $group = $classArr[0];
        $class = !empty($classArr[1]) ? $classArr[1] : null;

        if (isset($this->_classNameCache[$groupRootNode][$group][$class])) {
            return $this->_classNameCache[$groupRootNode][$group][$class];
        }

        //$config = $this->getNode($groupRootNode.'/'.$group);
        $config = $this->_xml->global->{$groupType.'s'}->{$group};

        if (isset($config->rewrite->$class)) {
            $className = (string)$config->rewrite->$class;
        } else {
            if (!empty($config)) {
                $className = $config->getClassName();
            }
            if (empty($className)) {
                $className = 'mage_'.$group.'_'.$groupType;
            }
            if (!empty($class)) {
                $className .= '_'.$class;
            }
            $className = uc_words($className);
        }

        $this->_classNameCache[$groupRootNode][$group][$class] = $className;

        return $className;
    }

    /**
     * Retrieve block class name
     *
     * @param   string $blockType
     * @return  string
     */
    public function getBlockClassName($blockType)
    {
        if (strpos($blockType, '/')===false) {
            return $blockType;
        }
        return $this->getGroupedClassName('block', $blockType);
    }

    /**
     * Retrieve helper class name
     *
     * @param   string $name
     * @return  string
     */
    public function getHelperClassName($helperName)
    {
        if (strpos($helperName, '/')===false) {
            $helperName .= '/data';
        }
        return $this->getGroupedClassName('helper', $helperName);
    }

    /**
     * Retrieve modele class name
     *
     * @param   sting $modelClass
     * @return  string
     */
    public function getModelClassName($modelClass)
    {
        $modelClass = trim($modelClass);
        if (strpos($modelClass, '/')===false) {
            return $modelClass;
        }
        return $this->getGroupedClassName('model', $modelClass);
    }

    /**
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog/product')
     *
     * Will instantiate Mage_Catalog_Model_Mysql4_Product
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract
     */
    public function getModelInstance($modelClass='', $constructArguments=array())
    {
        $className = $this->getModelClassName($modelClass);
        if (class_exists($className)) {
            return new $className($constructArguments);
        } else {
            #throw Mage::exception('Mage_Core', Mage::helper('core')->__('Model class does not exist: %s', $modelClass));
            return false;
        }
    }

    public function getNodeClassInstance($path)
    {
        $config = Mage::getConfig()->getNode($path);
        if (!$config) {
            return false;
        } else {
            $className = $config->getClassName();
            return new $className();
        }
    }

    public function getResourceModelInstance($modelClass='', $constructArguments=array())
    {
        $classArr = explode('/', $modelClass);

        $resourceModel = false;

        if (!isset($this->_xml->global->models->{$classArr[0]})) {
            return false;
        }

        $module = $this->_xml->global->models->{$classArr[0]};

        if ((count($classArr)==2)
            && isset($module->{$classArr[1]}->resourceModel)
            && $resourceInfo = $module->{$classArr[1]}->resourceModel) {
            $resourceModel = (string) $resourceInfo;
        }
        elseif (isset($module->resourceModel) && $resourceInfo = $module->resourceModel) {
            $resourceModel = (string) $resourceInfo;
        }

        if (!$resourceModel) {
            return false;
        }
        return $this->getModelInstance($resourceModel.'/'.$classArr[1], $constructArguments);
    }

    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        //return $this->getNode("global/resources/$name");
        return $this->_xml->global->resources->{$name};
    }

    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        if ($config) {
            $conn = $config->connection;
            if (!empty($conn->use)) {
                return $this->getResourceConnectionConfig((string)$conn->use);
            } else {
                return $conn;
            }
        }
        return false;
    }

    /**
     * Retrieve resource type configuration for resource name
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getResourceTypeConfig($type)
    {
        //return $this->getNode("global/resource/connection/types/$type");
        return $this->_xml->global->resource->connection->types->{$type};
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * if empty $allowValues then retrieve all stores values
     *
     * return array($storeId=>$pathValue)
     *
     * @param   string $path
     * @param   array  $allowValues
     * @return  array
     */
    public function getStoresConfigByPath($path, $allowValues = array(), $useAsKey = 'id')
    {
        $storeValues = array();
        $stores = $this->getNode('stores');
        foreach ($stores->children() as $code => $store) {
            switch ($useAsKey) {
                case 'id':
                    $key = (int) $store->descend('system/store/id');
                    break;

                case 'code':
                    $key = $code;
                    break;

                case 'name':
                    $key = (string) $store->descend('system/store/name');
            }
            if ($key === false) {
                continue;
            }

            $pathValue = (string) $store->descend($path);

            if (empty($allowValues)) {
                $storeValues[$key] = $pathValue;
            }
            elseif(in_array($pathValue, $allowValues)) {
                $storeValues[$key] = $pathValue;
            }
        }
        return $storeValues;
    }

    /**
     * Check security requirements for url
     *
     * @param   string $url
     * @return  bool
     */
    public function shouldUrlBeSecure($url)
    {
        if (!isset($this->_secureUrlCache[$url])) {
            $this->_secureUrlCache[$url] = false;
            $secureUrls = $this->getNode('frontend/secure_url');
            foreach ($secureUrls->children() as $match) {
                if (strpos($url, (string)$match)===0) {
                    $this->_secureUrlCache[$url] = true;
                    break;
                }
            }
        }

        return $this->_secureUrlCache[$url];
    }

    /**
     * Returns node found by the $path and scope info
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeCode
     * @return Mage_Core_Model_Config_Element
     */
    public function getNode($path=null, $scope='', $scopeCode=null)
    {
        if (!empty($scope)) {
            if (('store' === $scope) || ('website' === $scope)) {
                $scope .= 's';
            }
            if (('default' !== $scope) && is_int($scopeCode)) {
                if ('stores' == $scope) {
                    $scopeCode = Mage::app()->getStore($scopeCode)->getCode();
                } elseif ('websites' == $scope) {
                    $scopeCode = Mage::app()->getWebsite($scopeCode)->getCode();
                } else {
                    Mage::throwException(Mage::helper('core')->__('Unknown scope "%s"', $scope));
                }
            }
            $path = $scope . ($scopeCode ? '/' . $scopeCode : '' ) . (empty($path) ? '' : '/' . $path);
        }
        return parent::getNode($path);
    }

    public function getTablePrefix()
    {
        return $this->_xml->global->resources->db->table_prefix;
    }

    public function getEventConfig($area, $eventName)
    {
        return $this->_xml->{$area}->events->{$eventName};
    }

    /**
     * Save config value
     *
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     * @return Mage_Core_Store_Config
     */
    public function saveConfig($path, $value, $scope = 'default', $scopeId = 0)
    {
        $resource = $this->getResourceModel();
        $resource->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);

        return $this;
    }

    public function deleteConfig($path, $scope = 'default', $scopeId = 0)
    {
        $resource = $this->getResourceModel();
        $resource->deleteConfig(rtrim($path, '/'), $scope, $scopeId);

        return $this;
    }

    /**
     * Get fieldset from configuration
     *
     * @param string $name fieldset name
     * @param string $root fieldset area, could be 'admin'
     * @return null|array
     */
    public function getFieldset($name, $root = 'global')
    {
        if (!$rootNode = $this->getNode($root.'/fieldsets')) {
            return null;
        }
        return $rootNode->$name ? $rootNode->$name->children() : null;
    }
}