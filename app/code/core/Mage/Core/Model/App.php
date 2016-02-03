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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application model
 *
 * Application should have: areas, store, locale, translator, design package
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_App
{

    const XML_PATH_INSTALL_DATE = 'global/install/date';

    const XML_PATH_SKIP_PROCESS_MODULES_UPDATES = 'global/skip_process_modules_updates';

    /**
     * if this node set to true, we will ignore Developer Mode for applying updates
     */
    const XML_PATH_IGNORE_DEV_MODE = 'global/skip_process_modules_updates_ignore_dev_mode';

    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';

    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Cache tag for all cache data exclude config cache
     *
     */
    const CACHE_TAG = 'MAGE';

    /**
     * Default store Id (for install)
     */
    const DISTRO_STORE_ID       = 1;

    /**
     * Default store code (for install)
     *
     */
    const DISTRO_STORE_CODE     = 'default';

    /**
     * Admin store Id
     *
     */
    const ADMIN_STORE_ID = 0;

    /**
     * Application loaded areas array
     *
     * @var array
     */
    protected $_areas = array();

    /**
     * Application store object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Application website object
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Application location object
     *
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Application translate object
     *
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Application design package object
     *
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * Application layout object
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Application configuration object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Application front controller
     *
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_frontController;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
    * Use Cache
    *
    * @var array
    */
    protected $_useCache;

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites = array();

    /**
     * Groups cache
     *
     * @var array
     */
    protected $_groups = array();

    /**
     * Stores cache
     *
     * @var array
     */
    protected $_stores = array();

    /**
     * is a single store mode
     *
     * @var bool
     */
    protected $_isSingleStore;

    /**
     * @var bool
     */
    protected $_isSingleStoreAllowed = true;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore;

    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;


    /**
     * Events cache
     *
     * @var array
     */
    protected $_events = array();

    /**
     * Update process run flag
     *
     * @var bool
     */
    protected $_updateMode = false;

    /**
     * Use session in URL flag
     *
     * @see Mage_Core_Model_Url
     * @var bool
     */
    protected $_useSessionInUrl = true;

    /**
     * Use session var instead of SID for session in URL
     *
     * @var bool
     */
    protected $_useSessionVar = false;

    /**
     * Cache locked flag
     *
     * @var null|bool
     */
    protected $_isCacheLocked = null;

    /**
     * Flag for Magento installation status
     *
     * @var null|bool
     */
    protected $_isInstalled = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Initialize application without request processing
     *
     * @param  string|array $code
     * @param  string $type
     * @param  string|array $options
     * @return Mage_Core_Model_App
     */
    public function init($code, $type = null, $options = array())
    {
        $this->_initEnvironment();
        if (is_string($options)) {
            $options = array('etc_dir'=>$options);
        }

        Varien_Profiler::start('mage::app::init::config');
        $this->_config = Mage::getConfig();
        $this->_config->setOptions($options);
        $this->_initBaseConfig();
        $this->_initCache();
        $this->_config->init($options);
        Varien_Profiler::stop('mage::app::init::config');

        if ($this->_isInstalled === null) {
            $this->_isInstalled = Mage::isInstalled($options);
        }

        if ($this->_isInstalled) {
            $this->_initCurrentStore($code, $type);
            $this->_initRequest();
        }
        return $this;
    }

    /**
     * Common logic for all run types
     *
     * @param  string|array $options
     * @return Mage_Core_Model_App
     */
    public function baseInit($options)
    {
        $this->_initEnvironment();

        $this->_config = Mage::getConfig();
        $this->_config->setOptions($options);

        $this->_initBaseConfig();
        $cacheInitOptions = is_array($options) && array_key_exists('cache', $options) ? $options['cache'] : array();
        $this->_initCache($cacheInitOptions);

        return $this;
    }

    /**
     * Run light version of application with specified modules support
     *
     * @see Mage_Core_Model_App->run()
     *
     * @param  string|array $scopeCode
     * @param  string $scopeType
     * @param  string|array $options
     * @param  string|array $modules
     * @return Mage_Core_Model_App
     */
    public function initSpecified($scopeCode, $scopeType = null, $options = array(), $modules = array())
    {
        $this->baseInit($options);

        if (!empty($modules)) {
            $this->_config->addAllowedModules($modules);
        }
        $this->_initModules();
        $this->_initCurrentStore($scopeCode, $scopeType);

        return $this;
    }

    /**
     * Run application. Run process responsible for request processing and sending response.
     * List of supported parameters:
     *  scope_code - code of default scope (website/store_group/store code)
     *  scope_type - type of default scope (website/group/store)
     *  options    - configuration options
     *
     * @param  array $params application run parameters
     * @return Mage_Core_Model_App
     */
    public function run($params)
    {
        $options = isset($params['options']) ? $params['options'] : array();
        $this->baseInit($options);
        Mage::register('application_params', $params);

        if ($this->_cache->processRequest()) {
            $this->getResponse()->sendResponse();
        } else {
            $this->_initModules();
            $this->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);

            if ($this->_config->isLocalConfigLoaded()) {
                $scopeCode = isset($params['scope_code']) ? $params['scope_code'] : '';
                $scopeType = isset($params['scope_type']) ? $params['scope_type'] : 'store';
                $this->_initCurrentStore($scopeCode, $scopeType);
                $this->_initRequest();
                Mage_Core_Model_Resource_Setup::applyAllDataUpdates();
            }

            $this->getFrontController()->dispatch();
        }
        return $this;
    }

    /**
     * Initialize PHP environment
     *
     * @return Mage_Core_Model_App
     */
    protected function _initEnvironment()
    {
        $this->setErrorHandler(self::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
        return $this;
    }

    /**
     * Initialize base system configuration (local.xml and config.xml files).
     * Base configuration provide ability initialize DB connection and cache backend
     *
     * @return Mage_Core_Model_App
     */
    protected function _initBaseConfig()
    {
        Varien_Profiler::start('mage::app::init::system_config');
        $this->_config->loadBase();
        Varien_Profiler::stop('mage::app::init::system_config');
        return $this;
    }

    /**
     * Initialize application cache instance
     *
     * @param array $cacheInitOptions
     * @return Mage_Core_Model_App
     */
    protected function _initCache(array $cacheInitOptions = array())
    {
        $this->_isCacheLocked = true;
        $options = $this->_config->getNode('global/cache');
        if ($options) {
            $options = $options->asArray();
        } else {
            $options = array();
        }
        $options = array_merge($options, $cacheInitOptions);
        $this->_cache = Mage::getModel('core/cache', $options);
        $this->_isCacheLocked = false;
        return $this;
    }

    /**
     * Initialize active modules configuration and data
     *
     * @return Mage_Core_Model_App
     */
    protected function _initModules()
    {
        if (!$this->_config->loadModulesCache()) {
            $this->_config->loadModules();
            if ($this->_config->isLocalConfigLoaded() && !$this->_shouldSkipProcessModulesUpdates()) {
                Varien_Profiler::start('mage::app::init::apply_db_schema_updates');
                Mage_Core_Model_Resource_Setup::applyAllUpdates();
                Varien_Profiler::stop('mage::app::init::apply_db_schema_updates');
            }
            $this->_config->loadDb();
            $this->_config->saveCache();
        }
        return $this;
    }

    /**
     * Check whether modules updates processing should be skipped
     *
     * @return bool
     */
    protected function _shouldSkipProcessModulesUpdates()
    {
        if (!Mage::isInstalled()) {
            return false;
        }

        $ignoreDevelopmentMode = (bool)(string)$this->_config->getNode(self::XML_PATH_IGNORE_DEV_MODE);
        if (Mage::getIsDeveloperMode() && !$ignoreDevelopmentMode) {
            return false;
        }

        return (bool)(string)$this->_config->getNode(self::XML_PATH_SKIP_PROCESS_MODULES_UPDATES);
    }

    /**
     * Init request object
     *
     * @return Mage_Core_Model_App
     */
    protected function _initRequest()
    {
        $this->getRequest()->setPathInfo();
        return $this;
    }

    /**
     * Initialize currently ran store
     *
     * @param string $scopeCode code of default scope (website/store_group/store code)
     * @param string $scopeType type of default scope (website/group/store)
     * @return unknown_type
     */
    protected function _initCurrentStore($scopeCode, $scopeType)
    {
        Varien_Profiler::start('mage::app::init::stores');
        $this->_initStores();
        Varien_Profiler::stop('mage::app::init::stores');

        if (empty($scopeCode) && !is_null($this->_website)) {
            $scopeCode = $this->_website->getCode();
            $scopeType = 'website';
        }
        switch ($scopeType) {
            case 'store':
                $this->_currentStore = $scopeCode;
                break;
            case 'group':
                $this->_currentStore = $this->_getStoreByGroup($scopeCode);
                break;
            case 'website':
                $this->_currentStore = $this->_getStoreByWebsite($scopeCode);
                break;
            default:
                $this->throwStoreException();
        }

        if (!empty($this->_currentStore)) {
            $this->_checkCookieStore($scopeType);
            $this->_checkGetStore($scopeType);
        }
        $this->_useSessionInUrl = $this->getStore()->getConfig(
            Mage_Core_Model_Session_Abstract::XML_PATH_USE_FRONTEND_SID);
        return $this;
    }

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }

    /**
     * Check get store
     *
     * @return Mage_Core_Model_App
     */
    protected function _checkGetStore($type)
    {
        if (empty($_GET)) {
            return $this;
        }

        /**
         * @todo check XML_PATH_STORE_IN_URL
         */
        if (!isset($_GET['___store'])) {
            return $this;
        }

        $store = $_GET['___store'];
        if (!isset($this->_stores[$store])) {
            return $this;
        }

        $storeObj = $this->_stores[$store];
        if (!$storeObj->getId() || !$storeObj->getIsActive()) {
            return $this;
        }

        /**
         * prevent running a store from another website or store group,
         * if website or store group was specified explicitly in Mage::run()
         */
        $curStoreObj = $this->_stores[$this->_currentStore];
        if ($type == 'website' && $storeObj->getWebsiteId() == $curStoreObj->getWebsiteId()) {
            $this->_currentStore = $store;
        }
        elseif ($type == 'group' && $storeObj->getGroupId() == $curStoreObj->getGroupId()) {
            $this->_currentStore = $store;
        }
        elseif ($type == 'store') {
            $this->_currentStore = $store;
        }

        if ($this->_currentStore == $store) {
            $store = $this->getStore($store);
            if ($store->getWebsite()->getDefaultStore()->getId() == $store->getId()) {
                $this->getCookie()->delete(Mage_Core_Model_Store::COOKIE_NAME);
            } else {
                $this->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $this->_currentStore, true);
            }
        }
        return $this;
    }

    /**
     * Check cookie store
     *
     * @param string $type
     * @return Mage_Core_Model_App
     */
    protected function _checkCookieStore($type)
    {
        if (!$this->getCookie()->get()) {
            return $this;
        }

        $store = $this->getCookie()->get(Mage_Core_Model_Store::COOKIE_NAME);
        if ($store && isset($this->_stores[$store])
            && $this->_stores[$store]->getId()
            && $this->_stores[$store]->getIsActive()) {
            if ($type == 'website'
                && $this->_stores[$store]->getWebsiteId() == $this->_stores[$this->_currentStore]->getWebsiteId()) {
                $this->_currentStore = $store;
            }
            if ($type == 'group'
                && $this->_stores[$store]->getGroupId() == $this->_stores[$this->_currentStore]->getGroupId()) {
                $this->_currentStore = $store;
            }
            if ($type == 'store') {
                $this->_currentStore = $store;
            }
        }
        return $this;
    }

    public function reinitStores()
    {
        return $this->_initStores();
    }

    /**
     * Init store, group and website collections
     *
     */
    protected function _initStores()
    {
        $this->_stores   = array();
        $this->_groups   = array();
        $this->_website  = null;
        $this->_websites = array();

        /** @var $websiteCollection Mage_Core_Model_Website */
        $websiteCollection = Mage::getModel('core/website')->getCollection()
                ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG))
                ->setLoadDefault(true);

        /** @var $websiteCollection Mage_Core_Model_Store_Group */
        $groupCollection = Mage::getModel('core/store_group')->getCollection()
                ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store_Group::CACHE_TAG))
                ->setLoadDefault(true);

        /** @var $websiteCollection Mage_Core_Model_Store */
        $storeCollection = Mage::getModel('core/store')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store::CACHE_TAG))
            ->setLoadDefault(true);

        $this->_isSingleStore = false;
        if ($this->_isSingleStoreAllowed) {
            $this->_isSingleStore = $storeCollection->count() < 3;
        }

        $websiteStores = array();
        $websiteGroups = array();
        $groupStores   = array();

        foreach ($storeCollection as $store) {
            /** @var $store Mage_Core_Model_Store */
            $store->initConfigCache();
            $store->setWebsite($websiteCollection->getItemById($store->getWebsiteId()));
            $store->setGroup($groupCollection->getItemById($store->getGroupId()));

            $this->_stores[$store->getId()] = $store;
            $this->_stores[$store->getCode()] = $store;

            $websiteStores[$store->getWebsiteId()][$store->getId()] = $store;
            $groupStores[$store->getGroupId()][$store->getId()] = $store;

            if (is_null($this->_store) && $store->getId()) {
                $this->_store = $store;
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Mage_Core_Model_Store_Group */
            if (!isset($groupStores[$group->getId()])) {
                $groupStores[$group->getId()] = array();
            }
            $group->setStores($groupStores[$group->getId()]);
            $group->setWebsite($websiteCollection->getItemById($group->getWebsiteId()));

            $websiteGroups[$group->getWebsiteId()][$group->getId()] = $group;

            $this->_groups[$group->getId()] = $group;
        }

        foreach ($websiteCollection as $website) {
            /* @var $website Mage_Core_Model_Website */
            if (!isset($websiteGroups[$website->getId()])) {
                $websiteGroups[$website->getId()] = array();
            }
            if (!isset($websiteStores[$website->getId()])) {
                $websiteStores[$website->getId()] = array();
            }
            if ($website->getIsDefault()) {
                $this->_website = $website;
            }
            $website->setGroups($websiteGroups[$website->getId()]);
            $website->setStores($websiteStores[$website->getId()]);

            $this->_websites[$website->getId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
    }

    /**
     * Is single Store mode (only one store without default)
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if ($this->_isInstalled === null) {
            $this->_isInstalled = Mage::isInstalled();
        }

        if (!$this->_isInstalled) {
            return false;
        }
        return $this->_isSingleStore;
    }

    /**
     * Retrieve store code or null by store group
     *
     * @param int $group
     * @return string|null
     */
    protected function _getStoreByGroup($group)
    {
        if (!isset($this->_groups[$group])) {
            return null;
        }
        if (!$this->_groups[$group]->getDefaultStoreId()) {
            return null;
        }
        return $this->_stores[$this->_groups[$group]->getDefaultStoreId()]->getCode();
    }

    /**
     * Retrieve store code or null by website
     *
     * @param int|string $website
     * @return string|null
     */
    protected function _getStoreByWebsite($website)
    {
        if (!isset($this->_websites[$website])) {
            return null;
        }
        if (!$this->_websites[$website]->getDefaultGroupId()) {
            return null;
        }
        return $this->_getStoreByGroup($this->_websites[$website]->getDefaultGroupId());
    }

    /**
     * Set current default store
     *
     * @param string $store
     * @return Mage_Core_Model_App
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
        return $this;
    }

    /**
     * Initialize application front controller
     *
     * @return Mage_Core_Model_App
     */
    protected function _initFrontController()
    {
        $this->_frontController = new Mage_Core_Controller_Varien_Front();
        Mage::register('controller', $this->_frontController);
        Varien_Profiler::start('mage::app::init_front_controller');
        $this->_frontController->init();
        Varien_Profiler::stop('mage::app::init_front_controller');
        return $this;
    }

    /**
     * Redeclare custom error handler
     *
     * @param   string $handler
     * @return  Mage_Core_Model_App
     */
    public function setErrorHandler($handler)
    {
        set_error_handler($handler);
        return $this;
    }

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App
     */
    public function loadArea($code)
    {
        $this->getArea($code)->load();
        return $this;
    }

    /**
     * Loding part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Mage_Core_Model_App
     */
    public function loadAreaPart($area, $part)
    {
        $this->getArea($area)->load($part);
        return $this;
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App_Area
     */
    public function getArea($code)
    {
        if (!isset($this->_areas[$code])) {
            $this->_areas[$code] = new Mage_Core_Model_App_Area($code, $this);
        }
        return $this->_areas[$code];
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStore($id = null)
    {
        if ($this->_isInstalled === null) {
            $this->_isInstalled = Mage::isInstalled();
        }

        if (!$this->_isInstalled || $this->getUpdateMode()) {
            return $this->_getDefaultStore();
        }

        if ($id === true && $this->isSingleStoreMode()) {
            return $this->_store;
        }

        if (!isset($id) || ''===$id || $id === true) {
            $id = $this->_currentStore;
        }
        if ($id instanceof Mage_Core_Model_Store) {
            return $id;
        }
        if (!isset($id)) {
            $this->throwStoreException();
        }

        if (empty($this->_stores[$id])) {
            $store = Mage::getModel('core/store');
            /* @var $store Mage_Core_Model_Store */
            if (is_numeric($id)) {
                $store->load($id);
            } elseif (is_string($id)) {
                $store->load($id, 'code');
            }

            if (!$store->getCode()) {
                $this->throwStoreException();
            }
            $this->_stores[$store->getStoreId()] = $store;
            $this->_stores[$store->getCode()] = $store;
        }
        return $this->_stores[$id];
    }

    /**
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|Mage_Core_Model_Store $id
     * @return Mage_Core_Model_Store
     */
    public function getSafeStore($id = null)
    {
        try {
            return $this->getStore($id);
        }
        catch (Exception $e) {
            if ($this->_currentStore) {
                $this->getRequest()->setActionName('noRoute');
                return new Varien_Object();
            }
            else {
                Mage::throwException(Mage::helper('core')->__('Requested invalid store "%s"', $id));
            }
        }
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        $stores = array();
        foreach ($this->_stores as $store) {
            if (!$withDefault && $store->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $stores[$store->getCode()] = $store;
            }
            else {
                $stores[$store->getId()] = $store;
            }
        }

        return $stores;
    }

    protected function _getDefaultStore()
    {
        if (empty($this->_store)) {
            $this->_store = Mage::getModel('core/store')
                ->setId(self::DISTRO_STORE_ID)
                ->setCode(self::DISTRO_STORE_CODE);
        }
        return $this->_store;
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStoreView()
    {
        foreach ($this->getWebsites() as $_website) {
            if ($_website->getIsDefault()) {
                $_defaultStore = $this->getGroup($_website->getDefaultGroupId())->getDefaultStore();
                if ($_defaultStore) {
                    return $_defaultStore;
                }
            }
        }
        return null;
    }

    public function getDistroLocaleCode()
    {
        return self::DISTRO_LOCALE_CODE;
    }

    /**
     * Retrieve application website object
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite($id=null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getWebsiteId();
        } elseif ($id instanceof Mage_Core_Model_Website) {
            return $id;
        } elseif ($id === true) {
            return $this->_website;
        }

        if (empty($this->_websites[$id])) {
            $website = Mage::getModel('core/website');
            if (is_numeric($id)) {
                $website->load($id);
                if (!$website->hasWebsiteId()) {
                    throw Mage::exception('Mage_Core', 'Invalid website id requested.');
                }
            } elseif (is_string($id)) {
                $websiteConfig = $this->_config->getNode('websites/'.$id);
                if (!$websiteConfig) {
                    throw Mage::exception('Mage_Core', 'Invalid website code requested: '.$id);
                }
                $website->loadConfig($id);
            }
            $this->_websites[$website->getWebsiteId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
        return $this->_websites[$id];
    }

    public function getWebsites($withDefault = false, $codeKey = false)
    {
        $websites = array();
        if (is_array($this->_websites)) {
            foreach ($this->_websites as $website) {
                if (!$withDefault && $website->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $websites[$website->getCode()] = $website;
                }
                else {
                    $websites[$website->getId()] = $website;
                }
            }
        }

        return $websites;
    }

    /**
     * Retrieve application store group object
     *
     * @return Mage_Core_Model_Store_Group
     */
    public function getGroup($id=null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getGroup()->getId();
        } elseif ($id instanceof Mage_Core_Model_Store_Group) {
            return $id;
        }
        if (empty($this->_groups[$id])) {
            $group = Mage::getModel('core/store_group');
            if (is_numeric($id)) {
                $group->load($id);
                if (!$group->hasGroupId()) {
                    throw Mage::exception('Mage_Core', 'Invalid store group id requested.');
                }
            }
            $this->_groups[$group->getGroupId()] = $group;
        }
        return $this->_groups[$id];
    }

    /**
     * Retrieve application locale object
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::getSingleton('core/locale');
        }
        return $this->_locale;
    }

    /**
     * Retrive layout object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        if (!$this->_layout) {
            if ($this->getFrontController()->getAction()) {
                $this->_layout = $this->getFrontController()->getAction()->getLayout();
            } else {
                $this->_layout = Mage::getSingleton('core/layout');
            }
        }
        return $this->_layout;
    }

    /**
     * Retrieve translate object
     *
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        if (!$this->_translator) {
            $this->_translator = Mage::getSingleton('core/translate');
        }
        return $this->_translator;
    }

    /**
     * Retrieve helper object
     *
     * @param string $name
     * @return Mage_Core_Helper_Abstract
     */
    public function getHelper($name)
    {
        return Mage::helper($name);
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        //return Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, 0);
        return (string) Mage::app()->getConfig()
            ->getNode('default/' . Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
    }

    /**
     * Retrieve configuration object
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve front controller object
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        if (!$this->_frontController) {
            $this->_initFrontController();
        }

        return $this->_frontController;
    }

    /**
     * Get core cache model
     *
     * @return Mage_Core_Model_Cache
     */
    public function getCacheInstance()
    {
        if (!$this->_cache) {
            $this->_initCache();
        }
        return $this->_cache;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_initCache();
        }
        return $this->_cache->getFrontend();
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id)
    {
        return $this->_cache->load($id);
    }

    /**
     * Saving cache data
     *
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function saveCache($data, $id, $tags=array(), $lifeTime=false)
    {
        $this->_cache->save($data, $id, $tags, $lifeTime);
        return $this;
    }

    /**
     * Remove cache
     *
     * @param   string $id
     * @return  Mage_Core_Model_App
     */
    public function removeCache($id)
    {
        $this->_cache->remove($id);
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function cleanCache($tags=array())
    {
        $this->_cache->clean($tags);
        Mage::dispatchEvent('application_clean_cache', array('tags' => $tags));
        return $this;
    }

    /**
    * Check whether to use cache for specific component
    *
    * @return boolean
    */
    public function useCache($type=null)
    {
        return $this->_cache->canUse($type);
    }

    /**
     * Save cache usage settings
     *
     * @param array $data
     * @return Mage_Core_Model_App
     */
    public function saveUseCache($data)
    {
        $this->_cache->saveOptions($data);
        return $this;
    }

    /**
     * Deletes all session files
     *
     */
    public function cleanAllSessions()
    {
        if (session_module_name()=='files') {
            $dir = session_save_path();
            mageDelTree($dir);
        }
        return $this;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->_request = new Mage_Core_Controller_Request_Http();
        }
        return $this->_request;
    }

    /**
     * Request setter
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_Core_Model_App
     */
    public function setRequest(Mage_Core_Controller_Request_Http $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (empty($this->_response)) {
            $this->_response = new Mage_Core_Controller_Response_Http();
            $this->_response->headersSentThrowsException = Mage::$headersSentThrowsException;
            $this->_response->setHeader("Content-Type", "text/html; charset=UTF-8");
        }
        return $this->_response;
    }

    /**
     * Response setter
     *
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_Core_Model_App
     */
    public function setResponse(Mage_Core_Controller_Response_Http $response)
    {
        $this->_response = $response;
        return $this;
    }

    public function addEventArea($area)
    {
        if (!isset($this->_events[$area])) {
            $this->_events[$area] = array();
        }
        return $this;
    }

    public function dispatchEvent($eventName, $args)
    {
        $eventName = strtolower($eventName);
        foreach ($this->_events as $area=>$events) {
            if (!isset($events[$eventName])) {
                $eventConfig = $this->getConfig()->getEventConfig($area, $eventName);
                if (!$eventConfig) {
                    $this->_events[$area][$eventName] = false;
                    continue;
                }
                $observers = array();
                foreach ($eventConfig->observers->children() as $obsName=>$obsConfig) {
                    $observers[$obsName] = array(
                        'type'  => (string)$obsConfig->type,
                        'model' => $obsConfig->class ? (string)$obsConfig->class : $obsConfig->getClassName(),
                        'method'=> (string)$obsConfig->method,
                        'args'  => (array)$obsConfig->args,
                    );
                }
                $events[$eventName]['observers'] = $observers;
                $this->_events[$area][$eventName]['observers'] = $observers;
            }
            if (false===$events[$eventName]) {
                continue;
            } else {
                $event = new Varien_Event($args);
                $event->setName($eventName);
                $observer = new Varien_Event_Observer();
            }

            foreach ($events[$eventName]['observers'] as $obsName=>$obs) {
                $observer->setData(array('event'=>$event));
                Varien_Profiler::start('OBSERVER: '.$obsName);
                switch ($obs['type']) {
                    case 'disabled':
                        break;
                    case 'object':
                    case 'model':
                        $method = $obs['method'];
                        $observer->addData($args);
                        $object = Mage::getModel($obs['model']);
                        $this->_callObserverMethod($object, $method, $observer);
                        break;
                    default:
                        $method = $obs['method'];
                        $observer->addData($args);
                        $object = Mage::getSingleton($obs['model']);
                        $this->_callObserverMethod($object, $method, $observer);
                        break;
                }
                Varien_Profiler::stop('OBSERVER: '.$obsName);
            }
        }
        return $this;
    }

    /**
     * Performs non-existent observer method calls protection
     *
     * @param object $object
     * @param string $method
     * @param Varien_Event_Observer $observer
     * @return Mage_Core_Model_App
     * @throws Mage_Core_Exception
     */
    protected function _callObserverMethod($object, $method, $observer)
    {
        if (method_exists($object, $method)) {
            $object->$method($observer);
        } elseif (Mage::getIsDeveloperMode()) {
            Mage::throwException('Method "'.$method.'" is not defined in "'.get_class($object).'"');
        }
        return $this;
    }

    public function setUpdateMode($value)
    {
        $this->_updateMode = $value;
    }

    public function getUpdateMode()
    {
        return $this->_updateMode;
    }

    public function throwStoreException()
    {
        throw new Mage_Core_Model_Store_Exception('');
    }

    /**
     * Set use session var instead of SID for URL
     *
     * @param bool $var
     * @return Mage_Core_Model_App
     */
    public function setUseSessionVar($var)
    {
        $this->_useSessionVar = (bool)$var;
        return $this;
    }

    /**
     * Retrieve use flag session var instead of SID for URL
     *
     * @return bool
     */
    public function getUseSessionVar()
    {
        return $this->_useSessionVar;
    }

    /**
     * Get either default or any store view
     *
     * @return Mage_Core_Model_Store
     */
    public function getAnyStoreView()
    {
        $store = $this->getDefaultStoreView();
        if ($store) {
            return $store;
        }
        foreach ($this->getStores() as $store) {
            return $store;
        }
    }

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_App
     */
    public function setUseSessionInUrl($flag = true)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve use session in URL flag
     *
     * @return bool
     */
    public function getUseSessionInUrl()
    {
        return $this->_useSessionInUrl;
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return Mage_Core_Model_App
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_isSingleStoreAllowed = (bool)$value;
        return $this;
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return array
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        $groups = array();
        if (is_array($this->_groups)) {
            foreach ($this->_groups as $group) {
                if (!$withDefault && $group->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $groups[$group->getCode()] = $group;
                }
                else {
                    $groups[$group->getId()] = $group;
                }
            }
        }

        return $groups;
    }

    /**
     * Retrieve application installation flag
     *
     * @deprecated since 1.2
     * @return bool
     */
    public function isInstalled()
    {
        return Mage::isInstalled();
    }

    /**
     * Generate cache tags from cache id
     *
     * @deprecated after 1.4.0.0-alpha3, functionality implemented in Mage_Core_Model_Cache
     * @param   string $id
     * @param   array $tags
     * @return  array
     */
    protected function _getCacheTags($tags=array())
    {
        foreach ($tags as $index=>$value) {
            $tags[$index] = $this->_getCacheId($value);
        }
        return $tags;
    }

    /**
     * Get file name with cache configuration settings
     *
     * @deprecated after 1.4.0.0-alpha3, functionality implemented in Mage_Core_Model_Cache
     * @return string
     */
    public function getUseCacheFilename()
    {
        return $this->_config->getOptions()->getEtcDir().DS.'use_cache.ser';
    }

    /**
     * Generate cache id with application specific data
     *
     * @deprecated after 1.4.0.0-alpha3, functionality implemented in Mage_Core_Model_Cache
     * @param   string $id
     * @return  string
     */
    protected function _getCacheId($id=null)
    {
        if ($id) {
            $id = $this->prepareCacheId($id);
        }
        return $id;
    }

    /**
     * Prepare identifier which can be used as cache id or cache tag
     *
     * @deprecated after 1.4.0.0-alpha3, functionality implemented in Mage_Core_Model_Cache
     * @param   string $id
     * @return  string
     */
    public function prepareCacheId($id)
    {
        $id = strtoupper($id);
        $id = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $id);
        return $id;
    }

    /**
     * Get is cache locked
     *
     * @return bool
     */
    public function getIsCacheLocked()
    {
        return (bool)$this->_isCacheLocked;
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Mage_Core_Model_Website $id
     * @return void
     */
    public function clearWebsiteCache($id = null)
    {
        if (is_null($id)) {
            $id = $this->getStore()->getWebsiteId();
        } elseif ($id instanceof Mage_Core_Model_Website) {
            $id = $id->getId();
        } elseif ($id === true) {
            $id = $this->_website->getId();
        }

        if (!empty($this->_websites[$id])) {
            $website = $this->_websites[$id];

            unset($this->_websites[$website->getWebsiteId()]);
            unset($this->_websites[$website->getCode()]);
        }
    }
}
