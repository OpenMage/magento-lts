<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Store model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Store _getResource()
 * @method Mage_Core_Model_Resource_Store getResource()
 * @method Mage_Core_Model_Resource_Store_Collection getCollection()
 * @method Mage_Core_Model_Resource_Store_Collection getResourceCollection()
 *
 * @method $this setCode(string $value)
 * @method $this setGroupId(int $value)
 * @method string getHomeUrl()
 * @method $this setHomeUrl(string $value)
 * @method $this setIsActive(int $value)
 * @method $this setLocaleCode(string $value)
 * @method string getLanguageCode()
 * @method string getLocaleCode()
 * @method $this setName(string $value)
 * @method $this setRootCategory(Mage_Catalog_Model_Category $value)
 * @method $this setRootCategoryPath(string $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method $this setWebsiteId(int $value)
 * @method string getRootCategoryPath()
 */
class Mage_Core_Model_Store extends Mage_Core_Model_Abstract
{
    /**
     * Entity name
     */
    public const ENTITY = 'core_store';

    /**
     * Configuration paths
     * @var string
     */
    public const XML_PATH_STORE_STORE_NAME          = 'general/store_information/name';

    public const XML_PATH_STORE_STORE_PHONE         = 'general/store_information/phone';

    public const XML_PATH_STORE_STORE_HOURS         = 'general/store_information/hours';

    public const XML_PATH_STORE_IN_URL              = 'web/url/use_store';

    public const XML_PATH_USE_REWRITES              = 'web/seo/use_rewrites';

    public const XML_PATH_UNSECURE_BASE_URL         = 'web/unsecure/base_url';

    public const XML_PATH_UNSECURE_BASE_JS_URL      = 'web/unsecure/base_js_url';

    public const XML_PATH_UNSECURE_BASE_LINK_URL    = 'web/unsecure/base_link_url';

    public const XML_PATH_UNSECURE_BASE_MEDIA_URL   = 'web/unsecure/base_media_url';

    public const XML_PATH_UNSECURE_BASE_SKIN_URL    = 'web/unsecure/base_skin_url';

    public const XML_PATH_SECURE_BASE_URL           = 'web/secure/base_url';

    public const XML_PATH_SECURE_BASE_JS_URL        = 'web/secure/base_js_url';

    public const XML_PATH_SECURE_BASE_LINK_URL      = 'web/secure/base_link_url';

    public const XML_PATH_SECURE_BASE_MEDIA_URL     = 'web/secure/base_media_url';

    public const XML_PATH_SECURE_BASE_SKIN_URL      = 'web/secure/base_skin_url';

    public const XML_PATH_SECURE_IN_FRONTEND        = 'web/secure/use_in_frontend';

    public const XML_PATH_SECURE_IN_ADMINHTML       = 'web/secure/use_in_adminhtml';

    public const XML_PATH_OFFLOADER_HEADER          = 'web/secure/offloader_header';

    public const XML_PATH_PRICE_SCOPE               = 'catalog/price/scope';

    /**
     * Price scope constants
     */
    public const PRICE_SCOPE_GLOBAL              = 0;

    /**
     *
     */
    public const PRICE_SCOPE_WEBSITE             = 1;

    /**
     * Possible URL types
     */
    public const URL_TYPE_LINK                   = 'link';

    /**
     *
     */
    public const URL_TYPE_DIRECT_LINK            = 'direct_link';

    /**
     *
     */
    public const URL_TYPE_WEB                    = 'web';

    /**
     *
     */
    public const URL_TYPE_SKIN                   = 'skin';

    /**
     *
     */
    public const URL_TYPE_JS                     = 'js';

    /**
     *
     */
    public const URL_TYPE_MEDIA                  = 'media';

    /**
     * Code constants
     */
    public const DEFAULT_CODE                    = 'default';

    /**
     *
     */
    public const ADMIN_CODE                      = 'admin';

    /**
     * Cache tag
     */
    public const CACHE_TAG                       = 'store';

    /**
     * Cookie name
     */
    public const COOKIE_NAME                     = 'store';

    /**
     * Cookie currency key
     */
    public const COOKIE_CURRENCY                 = 'currency';

    /**
     * Script name, which returns all the images
     */
    public const MEDIA_REWRITE_SCRIPT            = 'get.php/';

    /**
     * Cache flag
     *
     * @var bool
     */
    protected $_cacheTag    = true;

    /**
     * Event prefix for model events
     *
     * @var string
     */
    protected $_eventPrefix = 'store';

    /**
     * @var string
     */
    protected $_eventObject = 'store';

    /**
     * Price filter
     *
     * @var Mage_Directory_Model_Currency_Filter|Varien_Filter_Sprintf
     */
    protected $_priceFilter;

    /**
     * Website model
     *
     * @var Mage_Core_Model_Website|null
     */
    protected $_website;

    /**
     * Group model
     *
     * @var Mage_Core_Model_Store_Group|null
     */
    protected $_group;

    /**
     * Store configuration cache
     *
     * @var array|null
     */
    protected $_configCache = null;

    /**
     * Base nodes of store configuration cache
     *
     * @var array
     */
    protected $_configCacheBaseNodes = [];

    /**
     * Directory cache
     *
     * @var array
     */
    protected $_dirCache = [];

    /**
     * URL cache
     *
     * @var array
     */
    protected $_urlCache = [];

    /**
     * Base URL cache
     *
     * @var array
     */
    protected $_baseUrlCache = [];

    /**
     * Session entity
     *
     * @var Mage_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * Flag that shows that backend URLs are secure
     *
     * @var bool|null
     */
    protected $_isAdminSecure = null;

    /**
     * Flag that shows that frontend URLs are secure
     *
     * @var bool|null
     */
    protected $_isFrontSecure = null;

    /**
     * Store frontend name
     *
     * @var string|null
     */
    protected $_frontendName = null;

    /**
     * Readonly flag
     *
     * @var bool
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private $_isReadOnly = false;

    /**
     * Initialize object
     */
    protected function _construct()
    {
        $this->_init('core/store');
        $this->_configCacheBaseNodes = [
            self::XML_PATH_PRICE_SCOPE,
            self::XML_PATH_SECURE_BASE_URL,
            self::XML_PATH_SECURE_IN_ADMINHTML,
            self::XML_PATH_SECURE_IN_FRONTEND,
            self::XML_PATH_STORE_IN_URL,
            self::XML_PATH_UNSECURE_BASE_URL,
            self::XML_PATH_USE_REWRITES,
            self::XML_PATH_UNSECURE_BASE_LINK_URL,
            self::XML_PATH_SECURE_BASE_LINK_URL,
            'general/locale/code',
        ];
    }

    /**
     * Retrieve store session object
     *
     * @return Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        if (!$this->_session) {
            $this->_session = Mage::getModel('core/session')
                ->init('store_' . $this->getCode());
        }

        return $this->_session;
    }

    /**
     * @inheritDoc
     */
    public function load($id, $field = null)
    {
        if (!is_numeric($id) && is_null($field)) {
            $this->_getResource()->load($this, $id, 'code');
            return $this;
        }

        return parent::load($id, $field);
    }

    /**
     * Loading store configuration data
     *
     * @param   string $code
     * @return  $this
     */
    public function loadConfig($code)
    {
        if (is_numeric($code)) {
            foreach (Mage::getConfig()->getNode()->stores->children() as $storeCode => $store) {
                if ((int) $store->system->store->id == $code) {
                    $code = $storeCode;
                    break;
                }
            }
        } else {
            $store = Mage::getConfig()->getNode()->stores->{$code};
        }

        if (!empty($store)) {
            $this->setCode($code);
            $id = (int) $store->system->store->id;
            $this->setId($id)->setStoreId($id);
            $this->setWebsiteId((int) $store->system->website->id);
        }

        return $this;
    }

    /**
     * Retrieve Store code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_getData('code');
    }

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function getConfig($path)
    {
        if (isset($this->_configCache[$path])) {
            return $this->_configCache[$path];
        }

        $config = Mage::getConfig();

        $fullPath = 'stores/' . $this->getCode() . '/' . $path;
        $data = $config->getNode($fullPath);
        if (!$data && !Mage::isInstalled()) {
            $data = $config->getNode('default/' . $path);
        }

        if (!$data) {
            return null;
        }

        return $this->_processConfigValue($fullPath, $path, $data);
    }

    /**
     * Initialize base store configuration data
     *
     * Method provide cache configuration data without loading store config XML
     *
     * @return $this
     */
    public function initConfigCache()
    {
        /**
         * Functionality related with config separation
         */
        if ($this->_configCache === null) {
            $code = $this->getCode();
            if ($code) {
                if (Mage::app()->useCache('config')) {
                    $cacheId = 'store_' . $code . '_config_cache';
                    $data = Mage::app()->loadCache($cacheId);
                    if ($data) {
                        $data = unserialize($data, ['allowed_classes' => false]);
                    } else {
                        $data = [];
                        foreach ($this->_configCacheBaseNodes as $node) {
                            $data[$node] = $this->getConfig($node);
                        }

                        Mage::app()->saveCache(serialize($data), $cacheId, [
                            self::CACHE_TAG,
                            Mage_Core_Model_Config::CACHE_TAG,
                        ]);
                    }

                    $this->_configCache = $data;
                }
            }
        }

        return $this;
    }

    /**
     * Get the basic configuration nodes for this store view
     * @return array
     */
    public function getConfigCache()
    {
        $data = [];

        foreach ($this->_configCacheBaseNodes as $node) {
            $data[$node] = $this->getConfig($node);
        }

        return $data;
    }

    /**
     * Sets the internal configuration cache for this store view
     * @param array $data
     * @return $this
     */
    public function setConfigCache($data)
    {
        $this->_configCache = $data;
        return $this;
    }

    /**
     * Set config value for CURRENT model
     *
     * This value don't save in config
     *
     * @param string $path
     * @param mixed $value
     * @return $this
     */
    public function setConfig($path, $value)
    {
        if (isset($this->_configCache[$path])) {
            $this->_configCache[$path] = $value;
        }

        $fullPath = 'stores/' . $this->getCode() . '/' . $path;
        Mage::getConfig()->setNode($fullPath, $value);

        return $this;
    }

    /**
     * Set website model
     */
    public function setWebsite(Mage_Core_Model_Website $website)
    {
        $this->_website = $website;
    }

    /**
     * Retrieve store website
     *
     * @return Mage_Core_Model_Website|false
     */
    public function getWebsite()
    {
        if (is_null($this->getWebsiteId())) {
            return false;
        }

        if (is_null($this->_website)) {
            $this->_website = Mage::app()->getWebsite($this->getWebsiteId());
        }

        return $this->_website;
    }

    /**
     * Process config value
     *
     * @param string $fullPath
     * @param string $path
     * @param Varien_Simplexml_Element $node
     * @return array|string
     */
    protected function _processConfigValue($fullPath, $path, $node)
    {
        if (isset($this->_configCache[$path])) {
            return $this->_configCache[$path];
        }

        if ($node->hasChildren()) {
            $aValue = [];
            foreach ($node->children() as $k => $v) {
                $aValue[$k] = $this->_processConfigValue($fullPath . '/' . $k, $path . '/' . $k, $v);
            }

            $this->_configCache[$path] = $aValue;
            return $aValue;
        }

        $sValue = (string) $node;
        if (!empty($node['backend_model']) && !empty($sValue)) {
            $backend = Mage::getModel((string) $node['backend_model']);
            $backend->setPath($path)->setValue($sValue)->afterLoad();
            $sValue = $backend->getValue();
        }

        if (is_string($sValue) && str_contains($sValue, '{{')) {
            if (str_contains($sValue, '{{unsecure_base_url}}')) {
                $unsecureBaseUrl = $this->getConfig(self::XML_PATH_UNSECURE_BASE_URL);
                $sValue = str_replace('{{unsecure_base_url}}', $unsecureBaseUrl, $sValue);
            } elseif (str_contains($sValue, '{{secure_base_url}}')) {
                $secureBaseUrl = $this->getConfig(self::XML_PATH_SECURE_BASE_URL);
                $sValue = str_replace('{{secure_base_url}}', $secureBaseUrl, $sValue);
            } elseif (str_contains($sValue, '{{base_url}}')) {
                $sValue = Mage::getConfig()->substDistroServerVars($sValue);
            }
        }

        $this->_configCache[$path] = $sValue;

        return $sValue;
    }

    /**
     * Convert config values for url paths
     *
     * @deprecated after 1.4.2.0
     * @param string $value
     * @return string
     */
    public function processSubst($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (str_contains($value, '{{unsecure_base_url}}')) {
            $unsecureBaseUrl = $this->getConfig(self::XML_PATH_UNSECURE_BASE_URL);
            $value = str_replace('{{unsecure_base_url}}', $unsecureBaseUrl, $value);
        } elseif (str_contains($value, '{{secure_base_url}}')) {
            $secureBaseUrl = $this->getConfig(self::XML_PATH_SECURE_BASE_URL);
            $value = str_replace('{{secure_base_url}}', $secureBaseUrl, $value);
        } elseif (str_contains($value, '{{') && !str_contains($value, '{{base_url}}')) {
            $value = Mage::getConfig()->substDistroServerVars($value);
        }

        return $value;
    }

    /**
     * Retrieve default base path
     *
     * @return string
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public function getDefaultBasePath()
    {
        // phpcs:ignore Ecg.Security.Superglobal.SuperglobalUsageWarning
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            return '/';
        }

        return rtrim(Mage::app()->getRequest()->getBasePath() . '/') . '/';
    }

    /**
     * Retrieve url using store configuration specific
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        /** @var Mage_Core_Model_Url $url */
        $url = Mage::getModel('core/url')
            ->setStore($this);
        if (Mage::app()->getStore()->getId() != $this->getId()) {
            $params['_store_to_url'] = true;
        }

        return $url->getUrl($route, $params);
    }

    /**
     * Retrieve base URL
     *
     * @param self::URL_TYPE_* $type
     * @param bool|null $secure
     * @return string
     */
    public function getBaseUrl($type = self::URL_TYPE_LINK, $secure = null)
    {
        $cacheKey = $type . '/' . (is_null($secure) ? 'null' : ($secure ? 'true' : 'false'));
        if (!isset($this->_baseUrlCache[$cacheKey])) {
            switch ($type) {
                case self::URL_TYPE_WEB:
                    $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool) $secure;
                    $url = $this->getConfig('web/' . ($secure ? 'secure' : 'unsecure') . '/base_url');
                    break;

                case self::URL_TYPE_LINK:
                    $secure = (bool) $secure;
                    $url = $this->getConfig('web/' . ($secure ? 'secure' : 'unsecure') . '/base_link_url');
                    $url = $this->_updatePathUseRewrites($url);
                    $url = $this->_updatePathUseStoreView($url);
                    break;

                case self::URL_TYPE_DIRECT_LINK:
                    $secure = (bool) $secure;
                    $url = $this->getConfig('web/' . ($secure ? 'secure' : 'unsecure') . '/base_link_url');
                    $url = $this->_updatePathUseRewrites($url);
                    break;

                case self::URL_TYPE_SKIN:
                case self::URL_TYPE_JS:
                    $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool) $secure;
                    $url = $this->getConfig('web/' . ($secure ? 'secure' : 'unsecure') . '/base_' . $type . '_url');
                    break;

                case self::URL_TYPE_MEDIA:
                    $url = $this->_updateMediaPathUseRewrites($secure);
                    break;

                default:
                    throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid base url type'));
            }

            if (str_contains($url, '{{base_url}}')) {
                $baseUrl = Mage::getConfig()->substDistroServerVars('{{base_url}}');
                $url = str_replace('{{base_url}}', $baseUrl, $url);
            }

            $this->_baseUrlCache[$cacheKey] = rtrim($url, '/') . '/';
        }

        return $this->_baseUrlCache[$cacheKey];
    }

    /**
     * Remove script file name from url in case when server rewrites are enabled
     *
     * @param   string $url
     * @return  string
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _updatePathUseRewrites($url)
    {
        if ($this->isAdmin() || !$this->getConfig(self::XML_PATH_USE_REWRITES) || !Mage::isInstalled()) {
            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found,Ecg.Security.Superglobal.SuperglobalUsageWarning
            $indexFileName = $this->_isCustomEntryPoint() ? 'index.php' : basename($_SERVER['SCRIPT_FILENAME']);
            $url .= $indexFileName . '/';
        }

        return $url;
    }

    /**
     * Check if used entry point is custom
     *
     * @return bool
     */
    protected function _isCustomEntryPoint()
    {
        return (bool) Mage::registry('custom_entry_point');
    }

    /**
     * Retrieve URL for media catalog
     *
     * If we use Database file storage and server doesn't support rewrites (.htaccess in media folder)
     * we have to put name of fetching media script exactly into URL
     *
     * @param null|bool $secure
     * @param string $type
     * @return string
     */
    protected function _updateMediaPathUseRewrites($secure = null, $type = self::URL_TYPE_MEDIA)
    {
        $secure = is_null($secure) ? $this->isCurrentlySecure() : (bool) $secure;
        $secureStringFlag = $secure ? 'secure' : 'unsecure';
        $url = $this->getConfig('web/' . $secureStringFlag . '/base_' . $type . '_url');
        if (!$this->getConfig(self::XML_PATH_USE_REWRITES)
            && Mage::helper('core/file_storage_database')->checkDbUsage()
        ) {
            $urlStart = $this->getConfig('web/' . $secureStringFlag . '/base_url');
            $url = str_replace($urlStart, $urlStart . self::MEDIA_REWRITE_SCRIPT, $url);
        }

        return $url;
    }

    /**
     * Add store code to url in case if it is enabled in configuration
     *
     * @param   string $url
     * @return  string
     */
    protected function _updatePathUseStoreView($url)
    {
        if ($this->getStoreInUrl()) {
            $url .= $this->getCode() . '/';
        }

        return $url;
    }

    /**
     * Returns whether url forming scheme prepends url path with store view code
     *
     * @return bool
     */
    public function getStoreInUrl()
    {
        return Mage::isInstalled() && $this->getConfig(self::XML_PATH_STORE_IN_URL);
    }

    /**
     * Get store identifier
     *
     * @return int|null
     */
    public function getId()
    {
        $storeId = $this->_getData('store_id');
        return is_null($storeId) ? null : (int) $storeId;
    }

    /**
     * Check if store is admin store
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getId() == Mage_Core_Model_App::ADMIN_STORE_ID;
    }

    /**
     * Check if backend URLs should be secure
     *
     * @return bool
     */
    public function isAdminUrlSecure()
    {
        if ($this->_isAdminSecure === null) {
            $this->_isAdminSecure = (bool) (int) (string) Mage::getConfig()
                ->getNode(Mage_Core_Model_Url::XML_PATH_SECURE_IN_ADMIN);
        }

        return $this->_isAdminSecure;
    }

    /**
     * Check if frontend URLs should be secure
     *
     * @return bool
     */
    public function isFrontUrlSecure()
    {
        if ($this->_isFrontSecure === null) {
            $this->_isFrontSecure = Mage::getStoreConfigFlag(
                Mage_Core_Model_Url::XML_PATH_SECURE_IN_FRONT,
                $this->getId(),
            );
        }

        return $this->_isFrontSecure;
    }

    /**
     * Check if request was secure
     *
     * @deprecated
     * @return bool
     */
    public function isCurrentlySecure()
    {
        return Mage::app()->isCurrentlySecure();
    }

    /*************************************************************************************
     * Store currency interface
     */

    /**
     * Retrieve store base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        $configValue = $this->getConfig(self::XML_PATH_PRICE_SCOPE);
        if ($configValue == self::PRICE_SCOPE_GLOBAL) {
            return Mage::app()->getBaseCurrencyCode();
        } else {
            return $this->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        }
    }

    /**
     * Retrieve store base currency
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getBaseCurrency()
    {
        $currency = $this->getData('base_currency');
        if (is_null($currency)) {
            $currency = Mage::getModel('directory/currency')->load($this->getBaseCurrencyCode());
            $this->setData('base_currency', $currency);
        }

        return $currency;
    }

    /**
     * Get default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_DEFAULT);
    }

    /**
     * Retrieve store default currency
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getDefaultCurrency()
    {
        $currency = $this->getData('default_currency');
        if (is_null($currency)) {
            $currency = Mage::getModel('directory/currency')->load($this->getDefaultCurrencyCode());
            $this->setData('default_currency', $currency);
        }

        return $currency;
    }

    /**
     * Set current store currency code
     *
     * @param   string $code
     * @return  $this
     */
    public function setCurrentCurrencyCode($code)
    {
        $code = strtoupper($code);
        if (in_array($code, $this->getAvailableCurrencyCodes())) {
            $this->_getSession()->setCurrencyCode($code);
            if ($code == $this->getDefaultCurrency()) {
                Mage::app()->getCookie()->delete(self::COOKIE_CURRENCY, $code);
            } else {
                Mage::app()->getCookie()->set(self::COOKIE_CURRENCY, $code);
            }
        }

        return $this;
    }

    /**
     * Get current store currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        // try to get currently set code among allowed
        $code = $this->_getSession()->getCurrencyCode();
        if (empty($code)) {
            $code = $this->getDefaultCurrencyCode();
        }

        if (in_array($code, $this->getAvailableCurrencyCodes(true))) {
            return $code;
        }

        // take first one of allowed codes
        $codes = array_values($this->getAvailableCurrencyCodes(true));
        if (empty($codes)) {
            // return default code, if no codes specified at all
            return $this->getDefaultCurrencyCode();
        }

        return array_shift($codes);
    }

    /**
     * Get allowed store currency codes
     *
     * If base currency is not allowed in current website config scope,
     * then it can be disabled with $skipBaseNotAllowed
     *
     * @param bool $skipBaseNotAllowed
     * @return array
     */
    public function getAvailableCurrencyCodes($skipBaseNotAllowed = false)
    {
        $codes = $this->getData('available_currency_codes');
        if (is_null($codes)) {
            $codes = explode(',', $this->getConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_ALLOW));
            // add base currency, if it is not in allowed currencies
            $baseCurrencyCode = $this->getBaseCurrencyCode();
            if (!in_array($baseCurrencyCode, $codes)) {
                $codes[] = $baseCurrencyCode;

                // save base currency code index for further usage
                $disallowedBaseCodeIndex = array_keys($codes);
                $disallowedBaseCodeIndex = array_pop($disallowedBaseCodeIndex);
                $this->setData('disallowed_base_currency_code_index', $disallowedBaseCodeIndex);
            }

            $this->setData('available_currency_codes', $codes);
        }

        // remove base currency code, if it is not allowed by config (optional)
        if ($skipBaseNotAllowed) {
            $disallowedBaseCodeIndex = $this->getData('disallowed_base_currency_code_index');
            if ($disallowedBaseCodeIndex !== null) {
                unset($codes[$disallowedBaseCodeIndex]);
            }
        }

        return $codes;
    }

    /**
     * Retrieve store current currency
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getCurrentCurrency()
    {
        $currency = $this->getData('current_currency');

        if (is_null($currency)) {
            $currency     = Mage::getModel('directory/currency')->load($this->getCurrentCurrencyCode());
            $baseCurrency = $this->getBaseCurrency();

            if (!$baseCurrency->getRate($currency)) {
                $currency = $baseCurrency;
                $this->setCurrentCurrencyCode($baseCurrency->getCode());
            }

            $this->setData('current_currency', $currency);
        }

        return $currency;
    }

    /**
     * Retrieve current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        return $this->getBaseCurrency()->getRate($this->getCurrentCurrency());
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param   float $price
     * @param   bool $format             Format price to currency format
     * @param   bool $includeContainer   Enclose into <span class="price"><span>
     * @return  float
     */
    public function convertPrice($price, $format = false, $includeContainer = true)
    {
        if ($this->getCurrentCurrency() && $this->getBaseCurrency()) {
            $value = $this->getBaseCurrency()->convert($price, $this->getCurrentCurrency());
        } else {
            $value = $price;
        }

        if ($this->getCurrentCurrency() && $format) {
            $value = $this->formatPrice($value, $includeContainer);
        }

        return $value;
    }

    /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
        /** @var Mage_Catalog_Helper_Price $helper */
        $helper = Mage::helper('catalog/price');

        return round((float) $price, $helper->getRoundingPrecision());
    }

    /**
     * Format price with currency filter (taking rate into consideration)
     *
     * @param   float $price
     * @param   bool $includeContainer
     * @return  string|float
     */
    public function formatPrice($price, $includeContainer = true)
    {
        if ($this->getCurrentCurrency()) {
            return $this->getCurrentCurrency()->format($price, [], $includeContainer);
        }

        return $price;
    }

    /**
     * Get store price filter
     *
     * @return Mage_Directory_Model_Currency_Filter|Varien_Filter_Sprintf
     */
    public function getPriceFilter()
    {
        if (!$this->_priceFilter) {
            if ($this->getBaseCurrency() && $this->getCurrentCurrency()) {
                $this->_priceFilter = $this->getCurrentCurrency()->getFilter();
                $this->_priceFilter->setRate($this->getBaseCurrency()->getRate($this->getCurrentCurrency()));
            }
        } elseif ($this->getDefaultCurrency()) {
            $this->_priceFilter = $this->getDefaultCurrency()->getFilter();
        } else {
            $this->_priceFilter = new Varien_Filter_Sprintf('%s', 2);
        }

        return $this->_priceFilter;
    }

    /**
     * Retrieve root category identifier
     *
     * @return int
     */
    public function getRootCategoryId()
    {
        if (!$this->getGroup()) {
            return 0;
        }

        return $this->getGroup()->getRootCategoryId();
    }

    /**
     * Set group model for store
     *
     * @param Mage_Core_Model_Store_Group $group
     */
    public function setGroup($group)
    {
        $this->_group = $group;
    }

    /**
     * Retrieve group model
     *
     * @return Mage_Core_Model_Store_Group|false
     */
    public function getGroup()
    {
        if (is_null($this->getGroupId())) {
            return false;
        }

        if (is_null($this->_group)) {
            $this->_group = Mage::getModel('core/store_group')->load($this->getGroupId());
        }

        return $this->_group;
    }

    /**
     * Retrieve website identifier
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return (int) $this->_getData('website_id');
    }

    /**
     * Retrieve group identifier
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int) $this->_getData('group_id');
    }

    /**
     * Retrieve default group identifier
     *
     * @return int|string|null
     */
    public function getDefaultGroupId()
    {
        return $this->_getData('default_group_id');
    }

    /**
     * Check if store can be deleted
     *
     * @return bool
     */
    public function isCanDelete()
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getGroup()->getDefaultStoreId() != $this->getId();
    }

    /**
     * Retrieve current url for store
     *
     * @param bool|string $fromStore
     * @return string
     */
    public function getCurrentUrl($fromStore = true)
    {
        $sidQueryParam = $this->_getSession()->getSessionIdQueryParam();
        $requestString = Mage::getSingleton('core/url')->escape(
            ltrim(Mage::app()->getRequest()->getRequestString(), '/'),
        );

        $storeUrl = Mage::app()->getStore()->isCurrentlySecure()
            ? $this->getUrl('', ['_secure' => true])
            : $this->getUrl('');
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        $storeParsedUrl = parse_url($storeUrl);

        $storeParsedQuery = [];
        if (isset($storeParsedUrl['query'])) {
            // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
            parse_str($storeParsedUrl['query'], $storeParsedQuery);
        }

        $currQuery = Mage::app()->getRequest()->getQuery();
        if (isset($currQuery[$sidQueryParam]) && !empty($currQuery[$sidQueryParam])
            && $this->_getSession()->getSessionIdForHost($storeUrl) != $currQuery[$sidQueryParam]
        ) {
            unset($currQuery[$sidQueryParam]);
        }

        foreach ($currQuery as $k => $v) {
            $storeParsedQuery[$k] = $v;
        }

        if (!Mage::getStoreConfigFlag(self::XML_PATH_STORE_IN_URL, $this->getCode())) {
            $storeParsedQuery['___store'] = $this->getCode();
        }

        if ($fromStore !== false) {
            $storeParsedQuery['___from_store'] = $fromStore === true ? Mage::app()->getStore()->getCode() : $fromStore;
        }

        return $storeParsedUrl['scheme'] . '://' . $storeParsedUrl['host']
            . (isset($storeParsedUrl['port']) ? ':' . $storeParsedUrl['port'] : '')
            . $storeParsedUrl['path'] . $requestString
            . ($storeParsedQuery ? '?' . http_build_query($storeParsedQuery, '', '&amp;') : '');
    }

    /**
     * Check if store is active
     *
     * @return bool|null
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * Retrieve store name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Protect delete from non admin area
     *
     * Register indexing event before delete store
     *
     * {@inheritDoc}
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        Mage::getSingleton('index/indexer')->logEvent($this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
        return parent::_beforeDelete();
    }

    /**
     * rewrite in order to clear configuration cache
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        parent::_afterDelete();
        Mage::getConfig()->removeCache();
        return $this;
    }

    /**
     * Init indexing process after store delete commit
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('index/indexer')->indexEvents(self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
        return $this;
    }

    /**
     * Reinit and reset Config Data
     *
     * @return $this
     */
    public function resetConfig()
    {
        Mage::getConfig()->reinit();
        $this->_dirCache        = [];
        $this->_configCache     = [];
        $this->_baseUrlCache    = [];
        $this->_urlCache        = [];

        return $this;
    }

    /**
     * Get/Set isReadOnly flag
     *
     * @param bool $value
     * @return bool
     */
    public function isReadOnly($value = null)
    {
        if ($value !== null) {
            $this->_isReadOnly = (bool) $value;
        }

        return $this->_isReadOnly;
    }

    /**
     * Retrieve storegroup name
     *
     * @return string
     */
    public function getFrontendName()
    {
        if (is_null($this->_frontendName)) {
            $storeGroupName = (string) Mage::getStoreConfig(self::XML_PATH_STORE_STORE_NAME, $this);
            $this->_frontendName = (!empty($storeGroupName)) ? $storeGroupName : $this->getGroup()->getName();
        }

        return $this->_frontendName;
    }
}
