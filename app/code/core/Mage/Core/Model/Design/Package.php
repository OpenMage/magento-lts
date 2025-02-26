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
 * @copyright  Copyright (c) 2017-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Design_Package
{
    public const DEFAULT_AREA    = 'frontend';
    public const DEFAULT_PACKAGE = 'default';
    public const DEFAULT_THEME   = 'default';
    public const BASE_PACKAGE    = 'base';

    /**
     * @deprecated after 1.4.0.0-alpha3
     */
    public const FALLBACK_THEME  = 'default';

    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private static $_regexMatchCache      = [];

    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private static $_customThemeTypeCache = [];

    /**
     * Current Store for generation ofr base_dir and base_url
     *
     * @var string|integer|Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Package area
     *
     * @var string|null
     */
    protected $_area;

    /**
     * Package name
     *
     * @var string
     */
    protected $_name;

    /**
     * Package theme
     *
     * @var array
     */
    protected $_theme;

    /**
     * Package root directory
     *
     * @var string
     */
    protected $_rootDir;

    /**
     * Directory of the css file
     * Using only to transmit additional parameter in callback functions
     * @var string
     */
    protected $_callbackFileDir;

    /**
     * @var Mage_Core_Model_Design_Config|null
     */
    protected $_config = null;

    /**
     * @var Mage_Core_Model_Design_Fallback|null
     */
    protected $_fallback = null;

    /**
     * Whether theme/skin hierarchy should be checked via fallback mechanism
     * @TODO: implement setter for this value
     * @var bool
     */
    protected $_shouldFallback = true;

    public function __construct()
    {
        if (is_null($this->_config)) {
            $this->_config = Mage::getSingleton('core/design_config');
        }
        if (is_null($this->_fallback)) {
            $this->_fallback = Mage::getSingleton('core/design_fallback', [
                'config' => $this->_config,
            ]);
        }
    }

    /**
     * Set store
     *
     * @param  string|int|Mage_Core_Model_Store $store
     * @return $this
     */
    public function setStore($store)
    {
        if ($this->_fallback) {
            $this->_fallback->setStore($store);
        }
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store
     *
     * @return string|integer|Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store ?? Mage::app()->getStore();
    }

    /**
     * Set package area
     *
     * @param  string $area
     * @return $this
     */
    public function setArea($area)
    {
        $this->_area = $area;
        return $this;
    }

    /**
     * Retrieve package area
     *
     * @return string
     */
    public function getArea()
    {
        if (is_null($this->_area)) {
            $this->_area = self::DEFAULT_AREA;
        }
        return $this->_area;
    }

    /**
     * Set package name
     * In case of any problem, the default will be set.
     *
     * @param  string $name
     * @return $this
     */
    public function setPackageName($name = '')
    {
        if (empty($name)) {
            // see, if exceptions for user-agents defined in config
            $customPackage = $this->_checkUserAgentAgainstRegexps('design/package/ua_regexp');
            if ($customPackage) {
                $this->_name = $customPackage;
            } else {
                $this->_name = Mage::getStoreConfig('design/package/name', $this->getStore());
            }
        } else {
            $this->_name = $name;
        }
        // make sure not to crash, if wrong package specified
        if (!$this->designPackageExists($this->_name, $this->getArea())) {
            $this->_name = self::DEFAULT_PACKAGE;
        }
        return $this;
    }

    /**
     * Set store/package/area at once, and get respective values, that were before
     *
     * $storePackageArea must be assoc array. The keys may be:
     * 'store', 'package', 'area'
     *
     * @param array $storePackageArea
     * @return array
     */
    public function setAllGetOld($storePackageArea)
    {
        $oldValues = [];
        if (array_key_exists('store', $storePackageArea)) {
            $oldValues['store'] = $this->getStore();
            $this->setStore($storePackageArea['store']);
        }
        if (array_key_exists('area', $storePackageArea)) {
            $oldValues['area'] = $this->getArea();
            $this->setArea($storePackageArea['area']);
        }
        if (array_key_exists('package', $storePackageArea)) {
            $oldValues['package'] = $this->getPackageName();
            $this->setPackageName($storePackageArea['package']);
        }
        return $oldValues;
    }

    /**
     * Retrieve package name
     *
     * @return string
     */
    public function getPackageName()
    {
        if ($this->_name === null) {
            $this->setPackageName();
        }
        return $this->_name;
    }

    /**
     * @param string $packageName
     * @param string $area
     * @return bool
     */
    public function designPackageExists($packageName, $area = self::DEFAULT_AREA)
    {
        return is_dir(Mage::getBaseDir('design') . DS . $area . DS . $packageName);
    }

    /**
     * Declare design package theme params
     * Polymorph method:
     * 1) if 1 parameter specified, sets everything to this value
     * 2) if 2 parameters, treats 1st as key and 2nd as value
     *
     * @return $this
     */
    public function setTheme()
    {
        switch (func_num_args()) {
            case 1:
                foreach (['layout', 'template', 'skin', 'locale', 'default'] as $type) {
                    $this->_theme[$type] = func_get_arg(0);
                }
                break;

            case 2:
                $this->_theme[func_get_arg(0)] = func_get_arg(1);
                break;

            default:
                throw Mage::exception(Mage::helper('core')->__('Wrong number of arguments for %s', __METHOD__));
        }
        return $this;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getTheme($type)
    {
        if (empty($this->_theme[$type])) {
            $this->_theme[$type] = Mage::getStoreConfig('design/theme/' . $type, $this->getStore());
            if ($type !== 'default' && empty($this->_theme[$type])) {
                $this->_theme[$type] = $this->getTheme('default');
                if (empty($this->_theme[$type])) {
                    $this->_theme[$type] = self::DEFAULT_THEME;
                }
            }
        }

        // + "default", "skin"

        // set exception value for theme, if defined in config
        $customThemeType = $this->_checkUserAgentAgainstRegexps("design/theme/{$type}_ua_regexp");
        if ($customThemeType) {
            $this->_theme[$type] = $customThemeType;
        }

        return $this->_theme[$type];
    }

    /**
     * @return string
     */
    public function getDefaultTheme()
    {
        return self::DEFAULT_THEME;
    }

    /**
     * @return $this
     */
    public function updateParamDefaults(array &$params)
    {
        if ($this->getStore()) {
            $params['_store'] = $this->getStore();
        }
        if (empty($params['_area'])) {
            $params['_area'] = $this->getArea();
        }
        if (empty($params['_package'])) {
            $params['_package'] = $this->getPackageName();
        }
        if (empty($params['_theme'])) {
            $params['_theme'] = $this->getTheme($params['_type'] ?? '');
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseDir(array $params)
    {
        $this->updateParamDefaults($params);
        return (empty($params['_relative']) ? Mage::getBaseDir('design') . DS : '') .
            $params['_area'] . DS . $params['_package'] . DS . $params['_theme'] . DS . $params['_type'];
    }

    /**
     * @return string
     */
    public function getSkinBaseDir(array $params = [])
    {
        $params['_type'] = 'skin';
        $this->updateParamDefaults($params);
        return (empty($params['_relative']) ? Mage::getBaseDir('skin') . DS : '') .
            $params['_area'] . DS . $params['_package'] . DS . $params['_theme'];
    }

    /**
     * @return string
     */
    public function getLocaleBaseDir(array $params = [])
    {
        $params['_type'] = 'locale';
        $this->updateParamDefaults($params);
        return (empty($params['_relative']) ? Mage::getBaseDir('design') . DS : '') .
            $params['_area'] . DS . $params['_package'] . DS . $params['_theme'] . DS . 'locale' . DS .
            Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * @return string
     */
    public function getSkinBaseUrl(array $params = [])
    {
        $params['_type'] = 'skin';
        $this->updateParamDefaults($params);
        $urlPath = $params['_area'] . '/' . $params['_package'] . '/' . $params['_theme'] . '/';
        // Prevent XSS through malformed configuration
        $urlPath = htmlspecialchars($urlPath, ENT_HTML5 | ENT_QUOTES, 'UTF-8');
        return Mage::getBaseUrl('skin', isset($params['_secure']) ? (bool) $params['_secure'] : null) . $urlPath;
    }

    /**
     * Check whether requested file exists in specified theme params
     *
     * Possible params:
     * - _type: layout|template|skin|locale
     * - _package: design package, if not set = default
     * - _theme: if not set = default
     * - _file: path relative to theme root
     *
     * @see Mage_Core_Model_Config::getBaseDir
     * @param string $file
     * @return string|false
     */
    public function validateFile($file, array $params)
    {
        $fileName = $this->_renderFilename($file, $params);
        $testFile = (empty($params['_relative']) ? '' : Mage::getBaseDir('design') . DS) . $fileName;
        if (!file_exists($testFile)) {
            return false;
        }
        return $fileName;
    }

    /**
     * Get filename by specified theme parameters
     *
     * @param string $file
     * @return string
     */
    protected function _renderFilename($file, array $params)
    {
        switch ($params['_type']) {
            case 'skin':
                $dir = $this->getSkinBaseDir($params);
                break;

            case 'locale':
                $dir = $this->getLocaleBaseDir($params);
                break;

            default:
                $dir = $this->getBaseDir($params);
                break;
        }
        return $dir . DS . $file;
    }

    /**
     * Check for files existence by specified scheme
     *
     * If fallback enabled, the first found file will be returned. Otherwise the base package / default theme file,
     *   regardless of found or not.
     * If disabled, the lookup won't be performed to spare filesystem calls.
     *
     * @param string $file
     * @return string
     */
    protected function _fallback($file, array &$params, array $fallbackScheme = [[]])
    {
        if ($this->_shouldFallback) {
            foreach ($fallbackScheme as $try) {
                $params = array_merge($params, $try);
                $filename = $this->validateFile($file, $params);
                if ($filename) {
                    return $filename;
                }
            }
            $params['_package'] = self::BASE_PACKAGE;
            $params['_theme']   = self::DEFAULT_THEME;
        }
        return $this->_renderFilename($file, $params);
    }

    /**
     * Use this one to get existing file name with fallback to default
     *
     * $params['_type'] is required
     *
     * @param string $file
     * @return string
     * @throws Exception
     */
    public function getFilename($file, array $params)
    {
        Varien_Profiler::start(__METHOD__);

        // Prevent reading files outside of the proper directory while still allowing symlinked files
        if (str_contains($file, '..')) {
            Mage::log(sprintf('Invalid path requested: %s (params: %s)', $file, json_encode($params)), Zend_Log::ERR);
            throw new Exception('Invalid path requested.');
        }

        $this->updateParamDefaults($params);
        $result = $this->_fallback(
            $file,
            $params,
            $this->_fallback->getFallbackScheme(
                $params['_area'],
                $params['_package'],
                $params['_theme'],
            ),
        );
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getLayoutFilename($file, array $params = [])
    {
        $params['_type'] = 'layout';
        return $this->getFilename($file, $params);
    }

    /**
     * @param string $file
     * @return string
     */
    public function getTemplateFilename($file, array $params = [])
    {
        $params['_type'] = 'template';
        return $this->getFilename($file, $params);
    }

    /**
     * @param string $file
     * @return string
     */
    public function getLocaleFileName($file, array $params = [])
    {
        $params['_type'] = 'locale';
        return $this->getFilename($file, $params);
    }

    /**
     * Get skin file url
     *
     * @param string|null $file
     * @return string
     * @throws Exception
     */
    public function getSkinUrl($file = null, array $params = [])
    {
        Varien_Profiler::start(__METHOD__);

        // Prevent reading files outside of the proper directory while still allowing symlinked files
        if (str_contains((string) $file, '..')) {
            Mage::log(sprintf('Invalid path requested: %s (params: %s)', $file, json_encode($params)), Zend_Log::ERR);
            throw new Exception('Invalid path requested.');
        }

        if (empty($params['_type'])) {
            $params['_type'] = 'skin';
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        $this->updateParamDefaults($params);
        if (!empty($file)) {
            // This updates $params with the base package and default theme if the file is not found
            $this->_fallback(
                $file,
                $params,
                $this->_fallback->getFallbackScheme(
                    $params['_area'],
                    $params['_package'],
                    $params['_theme'],
                ),
            );
        }
        $result = $this->getSkinBaseUrl($params) . (empty($file) ? '' : $file);
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }

    /**
     * Design packages list getter
     * @return array
     */
    public function getPackageList()
    {
        $directory = Mage::getBaseDir('design') . DS . 'frontend';
        return $this->_listDirectories($directory);
    }

    /**
     * Design package (optional) themes list getter
     * @param string $package
     * @return array
     */
    public function getThemeList($package = null)
    {
        $result = [];

        if (is_null($package)) {
            foreach ($this->getPackageList() as $package) {
                $result[$package] = $this->getThemeList($package);
            }
        } else {
            $directory = Mage::getBaseDir('design') . DS . 'frontend' . DS . $package;
            $result = $this->_listDirectories($directory);
        }

        return $result;
    }

    /**
     * Directories lister utility method
     *
     * @param string $path
     * @param string|bool $fullPath
     * @return array
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private function _listDirectories($path, $fullPath = false)
    {
        $result = [];
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)) {
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DS . $entry;
                }
                $result[] = $entry;
            }
            unset($entry);
            closedir($dir);
        }

        return $result;
    }

    /**
     * Get regex rules from config and check user-agent against them
     *
     * Rules must be stored in config as a serialized array(['regexp']=>'...', ['value'] => '...')
     * Will return false or found string.
     *
     * @param string $regexpsConfigPath
     * @return false|string
     *
     * @SuppressWarnings("PHPMD.CamelCaseVariableName"))
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _checkUserAgentAgainstRegexps($regexpsConfigPath)
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        if (!empty(self::$_customThemeTypeCache[$regexpsConfigPath])) {
            return self::$_customThemeTypeCache[$regexpsConfigPath];
        }

        $configValueSerialized = Mage::getStoreConfig($regexpsConfigPath, $this->getStore());

        if (!$configValueSerialized) {
            return false;
        }

        try {
            $regexps = Mage::helper('core/unserializeArray')->unserialize($configValueSerialized);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        if (empty($regexps)) {
            return false;
        }

        return self::getPackageByUserAgent($regexps, $regexpsConfigPath);
    }

    /**
     * Return package name based on design exception rules
     *
     * @param array $rules - design exception rules
     * @param string $regexpsConfigPath
     * @return bool|string
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     * @SuppressWarnings("PHPMD.CamelCaseVariableName")
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    public static function getPackageByUserAgent(array $rules, $regexpsConfigPath = 'path_mock')
    {
        foreach ($rules as $rule) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (!empty(self::$_regexMatchCache[$rule['regexp']][$userAgent])) {
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }

            $regexp = '/' . trim($rule['regexp'], '/') . '/';

            if (@preg_match($regexp, $userAgent)) {
                self::$_regexMatchCache[$rule['regexp']][$userAgent] = true;
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }
        }

        return false;
    }

    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param array $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        $newestTimestamp = 0;
        foreach ($files as $file) {
            $filemtime = filemtime($file);
            if ($filemtime > $newestTimestamp) {
                $newestTimestamp = $filemtime;
            }
        }

        $targetFilename = md5(implode(',', $files) . "|{$newestTimestamp}") . '.js';
        $targetDir = $this->_initMergerDir('js');
        if (!$targetDir) {
            return '';
        }
        if ($this->_mergeFiles($files, $targetDir . DS . $targetFilename, false, null, 'js')) {
            return Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure()) . 'js/' . $targetFilename;
        }
        return '';
    }

    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param array $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'css_secure' : 'css';
        $targetDir = $this->_initMergerDir($mergerDir);
        if (!$targetDir) {
            return '';
        }

        // base hostname & port
        $baseMediaUrl = Mage::getBaseUrl('media', $isSecure);
        $hostname = parse_url($baseMediaUrl, PHP_URL_HOST);
        $port = parse_url($baseMediaUrl, PHP_URL_PORT);
        if ($port === false) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $newestTimestamp = 0;
        foreach ($files as $file) {
            $filemtime = filemtime($file);
            if ($filemtime > $newestTimestamp) {
                $newestTimestamp = $filemtime;
            }
        }

        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}|{$newestTimestamp}") . '.css';
        $mergeFilesResult = $this->_mergeFiles(
            $files,
            $targetDir . DS . $targetFilename,
            false,
            [$this, 'beforeMergeCss'],
            'css',
        );
        if ($mergeFilesResult) {
            return $baseMediaUrl . $mergerDir . '/' . $targetFilename;
        }
        return '';
    }

    /**
     * Merges files into one and saves it into DB (if DB file storage is on)
     *
     * @see Mage_Core_Helper_Data::mergeFiles()
     * @param string|bool $targetFile - file path to be written
     * @param bool $mustMerge
     * @param callable $beforeMergeCallback
     * @param array|string $extensionsFilter
     * @return bool|string
     */
    protected function _mergeFiles(
        array $srcFiles,
        $targetFile = false,
        $mustMerge = false,
        $beforeMergeCallback = null,
        $extensionsFilter = []
    ) {
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            if (!file_exists($targetFile)) {
                Mage::helper('core/file_storage_database')->saveFileToFilesystem($targetFile);
            }
            if (file_exists($targetFile)) {
                $filemtime = filemtime($targetFile);
            } else {
                $filemtime = null;
            }
            $result = Mage::helper('core')->mergeFiles(
                $srcFiles,
                $targetFile,
                $mustMerge,
                $beforeMergeCallback,
                $extensionsFilter,
            );
            if ($result && (filemtime($targetFile) > $filemtime)) {
                Mage::helper('core/file_storage_database')->saveFile($targetFile);
            }
            return $result;
        } else {
            return Mage::helper('core')->mergeFiles(
                $srcFiles,
                $targetFile,
                $mustMerge,
                $beforeMergeCallback,
                $extensionsFilter,
            );
        }
    }

    /**
     * Remove all merged js/css files
     *
     * @return  bool
     */
    public function cleanMergedJsCss()
    {
        $result = (bool) $this->_initMergerDir('js', true);
        $result = $this->_initMergerDir('css', true) && $result;
        return $this->_initMergerDir('css_secure', true) && $result;
    }

    /**
     * Make sure merger dir exists and writeable
     * Also can clean it up
     *
     * @param string $dirRelativeName
     * @param bool $cleanup
     * @return bool
     */
    protected function _initMergerDir($dirRelativeName, $cleanup = false)
    {
        try {
            $dir = Mage::getBaseDir('media') . DS . $dirRelativeName;
            if ($cleanup) {
                Varien_Io_File::rmdirRecursive($dir);
                Mage::helper('core/file_storage_database')->deleteFolder($dir);
            }
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            return is_writable($dir) ? $dir : false;
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * Before merge css callback function
     *
     * @param string $file
     * @param string $contents
     * @return string
     */
    public function beforeMergeCss($file, $contents)
    {
        $this->_setCallbackFileDir($file);

        $cssImport = '/@import\\s+([\'"])(.*?)[\'"]/';
        $contents = preg_replace_callback($cssImport, [$this, '_cssMergerImportCallback'], $contents);

        $cssUrl = '/url\\(\\s*(?![\\\'\\"]?data:)([^\\)\\s]+)\\s*\\)?/';

        return preg_replace_callback($cssUrl, [$this, '_cssMergerUrlCallback'], $contents);
    }

    /**
     * Set file dir for css file
     *
     * @param string $file
     */
    protected function _setCallbackFileDir($file)
    {
        $file = str_replace(Mage::getBaseDir() . DS, '', $file);
        $this->_callbackFileDir = dirname($file);
    }

    /**
     * Callback function replaces relative links for @import matches in css file
     *
     * @param array $match
     * @return string
     */
    protected function _cssMergerImportCallback($match)
    {
        $quote = $match[1];
        $uri = $this->_prepareUrl($match[2]);

        return "@import {$quote}{$uri}{$quote}";
    }

    /**
     * Callback function replaces relative links for url() matches in css file
     *
     * @param array $match
     * @return string
     */
    protected function _cssMergerUrlCallback($match)
    {
        $quote = ($match[1][0] == "'" || $match[1][0] == '"') ? $match[1][0] : '';
        $uri = ($quote == '') ? $match[1] : substr($match[1], 1, -1);
        $uri = $this->_prepareUrl($uri);

        return "url({$quote}{$uri}{$quote})";
    }

    /**
     * Prepare url for css replacement
     *
     * @param string $uri
     * @return string
     */
    protected function _prepareUrl($uri)
    {
        // check absolute or relative url
        if (!preg_match('/^https?:/i', $uri) && !preg_match('/^\//i', $uri)) {
            $fileDir = '';
            $pathParts = explode(DS, $uri);
            $fileDirParts = explode(DS, $this->_callbackFileDir);
            $store = $this->getStore();
            if (is_int($store)) {
                $store = Mage::app()->getStore($store);
            }
            if ($store->isAdmin()) {
                $secure = $store->isAdminUrlSecure();
            } else {
                $secure = $store->isFrontUrlSecure() && Mage::app()->getRequest()->isSecure();
            }

            if ($fileDirParts[0] == 'skin') {
                $baseUrl = Mage::getBaseUrl('skin', $secure);
                $fileDirParts = array_slice($fileDirParts, 1);
            } elseif ($fileDirParts[0] == 'media') {
                $baseUrl = Mage::getBaseUrl('media', $secure);
                $fileDirParts = array_slice($fileDirParts, 1);
            } else {
                $baseUrl = Mage::getBaseUrl('web', $secure);
            }

            foreach ($pathParts as $key => $part) {
                if ($part == '.' || $part == '..') {
                    unset($pathParts[$key]);
                }
                if ($part == '..' && count($fileDirParts)) {
                    $fileDirParts = array_slice($fileDirParts, 0, count($fileDirParts) - 1);
                }
            }

            if (count($fileDirParts)) {
                $fileDir = implode('/', $fileDirParts) . '/';
            }

            $uri = $baseUrl . $fileDir . implode('/', $pathParts);
        }
        return $uri;
    }

    /**
     * Default theme getter
     * @return string
     * @deprecated since 1.8.2.0
     */
    public function getFallbackTheme()
    {
        return Mage::getStoreConfig('design/theme/default', $this->getStore());
    }
}
