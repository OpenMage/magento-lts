<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Translate model
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Translate
{
    public const CSV_SEPARATOR     = ',';
    public const SCOPE_SEPARATOR   = '::';
    public const CACHE_TAG         = 'translate';

    public const CONFIG_KEY_AREA   = 'area';
    public const CONFIG_KEY_LOCALE = 'locale';
    public const CONFIG_KEY_STORE  = 'store';
    public const CONFIG_KEY_DESIGN_PACKAGE = 'package';
    public const CONFIG_KEY_DESIGN_THEME   = 'theme';

    /**
     * Default translation string
     */
    public const DEFAULT_STRING = 'Translate String';

    /**
     * Locale name
     *
     * @var string|null
     */
    protected $_locale;

    /**
     * Translation object
     *
     * @var Zend_Translate|null
     */
    protected $_translate;

    /**
     * Translator configuration array
     *
     * @var array
     */
    protected $_config;

    protected $_useCache = true;

    /**
     * Cache identifier
     *
     * @var string|null
     */
    protected $_cacheId;

    /**
     * Translation data
     *
     * @var array|null
     */
    protected $_data = [];

    /**
     * Translation data for data scope (per module)
     *
     * @var array
     */
    protected $_dataScope;

    /**
     * Configuration flag to enable inline translations
     *
     * @var bool
     */
    protected $_translateInline;

    /**
     * Configuration flag to local enable inline translations
     *
     * @var bool
     */
    protected $_canUseInline = true;

    public function __construct()
    {
    }

    /**
     * Initialization translation data
     *
     * @param string $area
     * @param bool $forceReload
     * @return $this
     */
    public function init($area, $forceReload = false)
    {
        $this->setConfig([self::CONFIG_KEY_AREA => $area]);

        $this->_translateInline = Mage::getSingleton('core/translate_inline')
            ->isAllowed($area == 'adminhtml' ? 'admin' : null);

        if (!$forceReload) {
            if ($this->_canUseCache()) {
                $this->_data = $this->_loadCache();
                if ($this->_data !== false) {
                    return $this;
                }
            }
            Mage::app()->removeCache($this->getCacheId());
        }

        $this->_data = [];

        foreach ($this->getModulesConfig() as $moduleName => $info) {
            $info = $info->asArray();
            $this->_loadModuleTranslation($moduleName, $info['files'], $forceReload);
        }

        $this->_loadThemeTranslation($forceReload);
        $this->_loadDbTranslation($forceReload);

        if (!$forceReload && $this->_canUseCache()) {
            $this->_saveCache();
        }

        return $this;
    }

    /**
     * Retrieve modules configuration by translation
     *
     * @return array|SimpleXMLElement
     */
    public function getModulesConfig()
    {
        if (!Mage::getConfig()->getNode($this->getConfig(self::CONFIG_KEY_AREA) . '/translate/modules')) {
            return [];
        }

        $config = Mage::getConfig()->getNode($this->getConfig(self::CONFIG_KEY_AREA) . '/translate/modules')->children();
        if (!$config) {
            return [];
        }
        return $config;
    }

    /**
     * Initialize configuration
     *
     * @param   array $config
     * @return  $this
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        if (!isset($this->_config[self::CONFIG_KEY_LOCALE])) {
            $this->_config[self::CONFIG_KEY_LOCALE] = $this->getLocale();
        }
        if (!isset($this->_config[self::CONFIG_KEY_STORE])) {
            $this->_config[self::CONFIG_KEY_STORE] = Mage::app()->getStore()->getId();
        }
        if (!isset($this->_config[self::CONFIG_KEY_DESIGN_PACKAGE])) {
            $this->_config[self::CONFIG_KEY_DESIGN_PACKAGE] = Mage::getDesign()->getPackageName();
        }
        if (!isset($this->_config[self::CONFIG_KEY_DESIGN_THEME])) {
            $this->_config[self::CONFIG_KEY_DESIGN_THEME] = Mage::getDesign()->getTheme('locale');
        }
        return $this;
    }

    /**
     * Retrieve config value by key
     *
     * @param   string $key
     * @return  mixed
     */
    public function getConfig($key)
    {
        return $this->_config[$key] ?? null;
    }

    /**
     * Loading data from module translation files
     *
     * @param string $moduleName
     * @param array $files
     * @param bool $forceReload
     * @return $this
     */
    protected function _loadModuleTranslation($moduleName, $files, $forceReload = false)
    {
        foreach ($files as $file) {
            $file = $this->_getModuleFilePath($moduleName, $file);
            $this->_addData($this->_getFileData($file), $moduleName, $forceReload);
        }
        return $this;
    }

    /**
     * Adding translation data
     *
     * @param array $data
     * @param string $scope
     * @param bool $forceReload
     * @return $this
     */
    protected function _addData($data, $scope, $forceReload = false)
    {
        foreach ($data as $key => $value) {
            if ($key === $value) {
                continue;
            }
            $key    = $this->_prepareDataString($key);
            $value  = $value === null ? '' : $this->_prepareDataString($value);
            if ($scope && isset($this->_dataScope[$key]) && !$forceReload) {
                /**
                 * Checking previous value
                 */
                $scopeKey = $this->_dataScope[$key] . self::SCOPE_SEPARATOR . $key;
                if (!isset($this->_data[$scopeKey])) {
                    if (isset($this->_data[$key])) {
                        $this->_data[$scopeKey] = $this->_data[$key];
                        /**
                         * Not allow use translation not related to module
                         */
                        if (Mage::getIsDeveloperMode()) {
                            unset($this->_data[$key]);
                        }
                    }
                }
                $scopeKey = $scope . self::SCOPE_SEPARATOR . $key;
                $this->_data[$scopeKey] = $value;
            } else {
                $this->_data[$key]     = $value;
                $this->_dataScope[$key] = $scope;
            }
        }
        return $this;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function _prepareDataString($string)
    {
        return str_replace('""', '"', $string);
    }

    /**
     * Loading current theme translation
     *
     * @param bool $forceReload
     * @return $this
     */
    protected function _loadThemeTranslation($forceReload = false)
    {
        $file = Mage::getDesign()->getLocaleFileName('translate.csv');
        $this->_addData($this->_getFileData($file), false, $forceReload);
        return $this;
    }

    /**
     * Loading current store translation from DB
     *
     * @param bool $forceReload
     * @return $this
     */
    protected function _loadDbTranslation($forceReload = false)
    {
        $arr = $this->getResource()->getTranslationArray(null, $this->getLocale());
        $this->_addData($arr, $this->getConfig(self::CONFIG_KEY_STORE), $forceReload);
        return $this;
    }

    /**
     * Retrieve translation file for module
     *
     * @param string $module
     * @param string $fileName
     * @return string
     */
    protected function _getModuleFilePath($module, $fileName)
    {
        //$file = Mage::getConfig()->getModuleDir('locale', $module);
        $file = Mage::getBaseDir('locale');
        $file .= DS . $this->getLocale() . DS . $fileName;
        return $file;
    }

    /**
     * Retrieve data from file
     *
     * @param   string $file
     * @return  array
     */
    protected function _getFileData($file)
    {
        $data = [];
        if (file_exists($file)) {
            $parser = new Varien_File_Csv();
            $parser->setDelimiter(self::CSV_SEPARATOR);
            $data = $parser->getDataPairs($file);
        }
        return $data;
    }

    /**
     * Retrieve translation data
     *
     * @return array
     */
    public function getData()
    {
        if (is_null($this->_data)) {
            return [];
            //Mage::throwException('Translation data is not initialized. Please contact developers.');
        }
        return $this->_data;
    }

    /**
     * Retrieve locale
     *
     * @return string
     */
    public function getLocale()
    {
        if (is_null($this->_locale)) {
            $this->_locale = Mage::app()->getLocale()->getLocaleCode();
        }
        return $this->_locale;
    }

    /**
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Retrieve DB resource model
     *
     * @return Mage_Core_Model_Resource_Translate
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('core/translate');
    }

    /**
     * Retrieve translation object
     *
     * @return Zend_Translate
     */
    public function getTranslate()
    {
        if (is_null($this->_translate)) {
            $this->_translate = new Zend_Translate('array', $this->getData(), $this->getLocale());
        }
        return $this->_translate;
    }

    /**
     * Translate
     *
     * @param   array $args
     * @return  string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function translate($args)
    {
        $text = array_shift($args);

        if (is_string($text) && $text == ''
            || is_null($text)
            || is_bool($text) && $text === false
            || is_object($text) && $text->getText() == ''
        ) {
            return '';
        }
        if ($text instanceof Mage_Core_Model_Translate_Expr) {
            $code = $text->getCode(self::SCOPE_SEPARATOR);
            $module = $text->getModule();
            $text = $text->getText();
            $translated = $this->_getTranslatedString($text, $code);
        } else {
            if (!empty($_REQUEST['theme'])) {
                $module = 'frontend/default/' . $_REQUEST['theme'];
            } else {
                $module = 'frontend/default/default';
            }
            $code = $module . self::SCOPE_SEPARATOR . $text;
            $translated = $this->_getTranslatedString($text, $code);
        }

        try {
            $result = !empty($args) ? vsprintf($translated, $args) : false;
        } catch (ValueError $e) {
            $result = false;
        }

        if ($result === false) {
            $result = $translated;
        }

        if ($this->_translateInline && $this->getTranslateInline()) {
            if (strpos($result, '{{{') === false || strpos($result, '}}}') === false || strpos($result, '}}{{') === false) {
                $result = '{{{' . $result . '}}{{' . $translated . '}}{{' . $text . '}}{{' . $module . '}}}';
            }
        }

        return $result;
    }

    /**
     * Set Translate inline mode
     *
     * @param bool $flag
     * @return $this
     */
    public function setTranslateInline($flag = null)
    {
        $this->_canUseInline = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve active translate mode
     *
     * @return bool
     */
    public function getTranslateInline()
    {
        return $this->_canUseInline;
    }

    /**
     * Retrieve translated template file
     *
     * @param string $file
     * @param string $type
     * @param string $localeCode
     * @return string
     */
    public function getTemplateFile($file, $type, $localeCode = null)
    {
        if (is_null($localeCode) || preg_match('/[^a-zA-Z_]/', $localeCode)) {
            $localeCode = $this->getLocale();
        }

        $filePath = Mage::getBaseDir('locale') . DS
                  . $localeCode . DS . 'template' . DS . $type . DS . $file;

        // If no template specified for this locale, use store default
        if (!file_exists($filePath)) {
            $filePath = Mage::getBaseDir('locale') . DS
                      . Mage::app()->getLocale()->getDefaultLocale()
                      . DS . 'template' . DS . $type . DS . $file;
        }

        // If no template specified as  store default locale, use en_US
        if (!file_exists($filePath)) {
            $filePath = Mage::getBaseDir('locale') . DS
                      . Mage_Core_Model_Locale::DEFAULT_LOCALE
                      . DS . 'template' . DS . $type . DS . $file;
        }

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(['path' => Mage::getBaseDir('locale')]);

        return (string) $ioAdapter->read($filePath);
    }

    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    public function getCacheId()
    {
        if (is_null($this->_cacheId)) {
            $this->_cacheId = 'translate';
            if (isset($this->_config[self::CONFIG_KEY_LOCALE])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_LOCALE];
            }
            if (isset($this->_config[self::CONFIG_KEY_AREA])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_AREA];
            }
            if (isset($this->_config[self::CONFIG_KEY_STORE])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_STORE];
            }
            if (isset($this->_config[self::CONFIG_KEY_DESIGN_PACKAGE])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_DESIGN_PACKAGE];
            }
            if (isset($this->_config[self::CONFIG_KEY_DESIGN_THEME])) {
                $this->_cacheId .= '_' . $this->_config[self::CONFIG_KEY_DESIGN_THEME];
            }
        }
        return $this->_cacheId;
    }

    /**
     * Loading data cache
     *
     * @return array|false
     */
    protected function _loadCache()
    {
        if (!$this->_canUseCache()) {
            return false;
        }
        $data = Mage::app()->loadCache($this->getCacheId());
        if (!$data) {
            return false;
        }
        $data = unserialize($data, ['allowed_classes' => false]);
        return $data;
    }

    /**
     * Saving data cache
     *
     * @return $this
     */
    protected function _saveCache()
    {
        if (!$this->_canUseCache()) {
            return $this;
        }
        Mage::app()->saveCache(serialize($this->getData()), $this->getCacheId(), [self::CACHE_TAG], null);
        return $this;
    }

    /**
     * Check cache usage availability
     *
     * @return false|array
     */
    protected function _canUseCache()
    {
        return Mage::app()->useCache('translate');
    }

    /**
     * Return translated string from text.
     *
     * @param string $text
     * @param string $code
     * @return string
     */
    protected function _getTranslatedString($text, $code)
    {
        if (array_key_exists($code, $this->getData())) {
            $translated = $this->_data[$code];
        } elseif (array_key_exists($text, $this->getData())) {
            $translated = $this->_data[$text];
        } else {
            $translated = $text;
        }
        return $translated;
    }
}
