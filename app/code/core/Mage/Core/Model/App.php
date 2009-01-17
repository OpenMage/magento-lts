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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';

    const DISTRO_LOCALE_CODE = 'en_US';

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
     * Helpers array
     *
     * @var array
     */
    protected $_helpers = array();

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
     * Use session var instead of SID for session in URL
     *
     * @var bool
     */
    protected $_useSessionVar = false;

    /**
     * Constructor
     *
     */
    public function __construct() {}

    /**
     * Initialize application
     *
     * @param string|array $code
     * @param string $type
     * @param string $etcDir
     * @return Mage_Core_Model_App
     */
    public function init($code, $type=null, $options=array())
    {
        $this->setErrorHandler(self::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

        if (is_string($options)) {
            $options = array('etc_dir'=>$options);
        }

        $this->_config = Mage::getConfig();
        $this->_config->init($options);

        if (Mage::isInstalled($options)) {
            $this->_initStores();

            if (empty($code) && !is_null($this->_website)) {
                $code = $this->_website->getCode();
                $type = 'website';
            }
            switch ($type) {
                case 'store':
                    $this->_currentStore = $code;
                    break;
                case 'group':
                    $this->_currentStore = $this->_getStoreByGroup($code);
                    break;
                case 'website':
                    $this->_currentStore = $this->_getStoreByWebsite($code);
                    break;
                default:
                    $this->throwStoreException();
            }

            if (!empty($this->_currentStore)) {
                $this->_checkCookieStore($type);
                $this->_checkGetStore($type);
            }
            $this->getRequest()->setPathInfo();
        }
        return $this;
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
            $cookie = Mage::getSingleton('core/cookie');
            /* @var $cookie Mage_Core_Model_Cookie */
            $cookie->set('store', $this->_currentStore);
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
        if (empty($_COOKIE)) {
            return $this;
        }
        $cookie = Mage::getSingleton('core/cookie');
        /* @var $cookie Mage_Core_Model_Cookie */

        $store  = $cookie->get('store');
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

        $websiteCollection = Mage::getModel('core/website')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Website::CACHE_TAG))
            ->setLoadDefault(true);
        $groupCollection = Mage::getModel('core/store_group')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store_Group::CACHE_TAG))
            ->setLoadDefault(true);
        $storeCollection = Mage::getModel('core/store')->getCollection()
            ->initCache($this->getCache(), 'app', array(Mage_Core_Model_Store::CACHE_TAG))
            ->setLoadDefault(true);

        $this->_isSingleStore = $storeCollection->count() < 3;

        $websiteStores = array();
        $websiteGroups = array();
        $groupStores   = array();

        foreach ($storeCollection as $store) {
            /* @var $store Mage_Core_Model_Store */
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
        if (!Mage::isInstalled()) {
            return false;
        }
        return $this->_isSingleStore;
    }

    /**
     * Retrive store code or null by store group
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
     * Retrive store code or null by website
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
        $this->_frontController->init();
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
     * @return Mage_Core_Model_Store
     */
    public function getStore($id=null)
    {
        if (!Mage::isInstalled() || $this->getUpdateMode()) {
            return $this->_getDefaultStore();
        }

        if ($id === true && $this->isSingleStoreMode()) {
            return $this->_store;
        }

        if (is_null($id) || ''===$id || $id === true) {
            $id = $this->_currentStore;
        }
        if ($id instanceof Mage_Core_Model_Store) {
            return $id;
        }
        if (is_null($id)) {
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
                if ($_defaultStore = $this->getGroup($_website->getDefaultGroupId())->getDefaultStore()) {
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
        }
        if (empty($this->_websites[$id])) {
            $website = Mage::getModel('core/website');
            if (is_numeric($id)) {
                $website->load($id);
                if (!$website->hasWebsiteId()) {
                    throw Mage::exception('Mage_Core', 'Invalid website id requested.');
                }
            } elseif (is_string($id)) {
                $websiteConfig = Mage::getConfig()->getNode('websites/'.$id);
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
            $this->_layout = ($this->getFrontController()->getAction()
                                    ?  $this->getFrontController()->getAction()->getLayout()
                                    :  Mage::getSingleton('core/layout'));
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
     * @param   helper name $name
     * @return  Mage_Core_Helper_Abstract
     */
    public function getHelper($name)
    {
        if (strpos($name, '/') === false) {
            $name .= '/data';
        }
        if (!isset($this->_helpers[$name])) {
            $class = Mage::getConfig()->getHelperClassName($name);
            $this->_helpers[$name] = new $class();
        }
        return $this->_helpers[$name];
    }

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE, 0);
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
     * Generate cache id with application specific data
     *
     * @param   string $id
     * @return  string
     */
    protected function _getCacheId($id=null)
    {
        if ($id) {
            $id = strtoupper($id);
            $id = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $id);
        }
        return $id;
    }

    /**
     * Generate cache tags from cache id
     *
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
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $backend = strtolower((string)Mage::getConfig()->getNode('global/cache/backend'));
            if (extension_loaded('apc') && ini_get('apc.enabled') && $backend=='apc') {
                $backend = 'Apc';
                $backendAttributes = array(
                    'cache_prefix' => (string)Mage::getConfig()->getNode('global/cache/prefix')
                );
            } elseif ('memcached' == $backend && extension_loaded('memcache')) {
                $backend = 'Memcached';
                $memcachedConfig = Mage::getConfig()->getNode('global/cache/memcached');
                $backendAttributes = array(
                    'compression'               => (bool)$memcachedConfig->compression,
                    'cache_dir'                 => (string)$memcachedConfig->cache_dir,
                    'hashed_directory_level'    => (string)$memcachedConfig->hashed_directory_level,
                    'hashed_directory_umask'    => (string)$memcachedConfig->hashed_directory_umask,
                    'file_name_prefix'          => (string)$memcachedConfig->file_name_prefix,
                    'servers'                   => array(),
                );
                foreach ($memcachedConfig->servers->children() as $serverConfig) {
                    $backendAttributes['servers'][] = array(
                        'host'          => (string)$serverConfig->host,
                        'port'          => (string)$serverConfig->port,
                        'persistent'    => (string)$serverConfig->persistent,
                    );
                }
            } else {
                $backend = 'File';
                $backendAttributes = array(
                    'cache_dir'                 => Mage::getBaseDir('cache'),
                    'hashed_directory_level'    => 1,
                    'hashed_directory_umask'    => 0777,
                    'file_name_prefix'          => 'mage',
                );
            }
            $lifetime = Mage::getConfig()->getNode('global/cache/lifetime');
            if ($lifetime !== false) {
                $lifetime = (int) $lifetime;
            }
            else {
                $lifetime = 7200;
            }
            $this->_cache = Zend_Cache::factory('Core', $backend,
                array(
                    'caching'                   => true,
                    'lifetime'                  => $lifetime,
                    'automatic_cleaning_factor' => 0,
                ),
                $backendAttributes
            );
        }
        return $this->_cache;
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id)
    {
        return $this->getCache()->load($this->_getCacheId($id));
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
        $tags = $this->_getCacheTags($tags);
        $this->getCache()->save((string)$data, $this->_getCacheId($id), $tags, $lifeTime);
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
        $this->getCache()->remove($this->_getCacheId($id));
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
        if (!empty($tags)) {
            if (!is_array($tags)) {
                $tags = array($tags);
            }
            $tags = $this->_getCacheTags($tags);
            $this->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        } else {
            $this->getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
        }

        Mage::dispatchEvent('application_clean_cache', array('tags' => $tags));
        return $this;
    }

    public function getUseCacheFilename()
    {
        return Mage::getConfig()->getOptions()->getEtcDir().DS.'use_cache.ser';
    }

    /**
    * Check whether to use cache for specific component
    *
    * Components:
    * - config
    * - layout
    * - eav
    * - translate
    *
    * @return boolean
    */
    public function useCache($type=null)
    {
        if (is_null($this->_useCache)) {
            $filename = $this->getUseCacheFilename();
            if (is_readable($filename)) {
                $this->_useCache = unserialize(file_get_contents($filename));
            } else {
                $data = Mage::getConfig()->getNode('global/use_cache');
                if (!empty($data)) {
                    $this->_useCache = (array)$data;
                } else {
                    $this->_useCache = array();
                }
            }
        }
        if (empty($type)) {
            return $this->_useCache;
        } else {
            return isset($this->_useCache[$type]) ? (bool)$this->_useCache[$type] : false;
        }
    }

    public function saveUseCache($data)
    {
        //Mage::app()->saveCache(serialize($cacheData), 'use_cache', array(), null);

        $filename = $this->getUseCacheFilename();
        if (!$fp = @fopen($filename, 'w')) {
            Mage::throwException($filename.' is not writable, unable to save cache settings');
        }
        @fwrite($fp, serialize($data));
        @fclose($fp);
        @chmod($filename, 0666);
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

    public function addEventArea($area)
    {
        if (!isset($this->_events[$area])) {
            $this->_events[$area] = array();
        }
        return $this;
    }

    public function dispatchEvent($eventName, $args)
    {
        $event = new Varien_Event($args);
        $event->setName($eventName);

        $observer = new Varien_Event_Observer();

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
                        'type' => $obsConfig->type ? (string)$obsConfig->type : 'singleton',
                        'model' => $obsConfig->class ? (string)$obsConfig->class : $obsConfig->getClassName(),
                        'method' => (string)$obsConfig->method,
                        'args' => (array)$obsConfig->args,
                    );
                }
                $events[$eventName]['observers'] = $observers;
                $this->_events[$area][$eventName]['observers'] = $observers;
            }
            if (false===$events[$eventName]) {
                continue;
            }
            foreach ($events[$eventName]['observers'] as $obsName=>$obs) {

                $observer->setData(array('event'=>$event));
                Varien_Profiler::start('OBSERVER: '.$obsName);
                switch ($obs['type']) {
                    case 'singleton':
                        $method = $obs['method'];
                        $observer->addData($args);
                        $object = Mage::getSingleton($obs['model']);
                        $object->$method($observer);
                        break;

                    case 'object': case 'model':
                        $method = $obs['method'];
                        $observer->addData($args);
                        $object = Mage::getModel($obs['model']);
                        $object->$method($observer);
                        break;
                }
                Varien_Profiler::stop('OBSERVER: '.$obsName);
            }
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

}
