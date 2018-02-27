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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Design_Package
{
    const DEFAULT_AREA    = 'frontend';
    const DEFAULT_PACKAGE = 'default';
    const DEFAULT_THEME   = 'default';
    const BASE_PACKAGE    = 'base';

    /**
     * @deprecated after 1.4.0.0-alpha3
     */
    const FALLBACK_THEME  = 'default';

    private static $_regexMatchCache      = array();
    private static $_customThemeTypeCache = array();

    /**
     * Current Store for generation ofr base_dir and base_url
     *
     * @var string|integer|Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Package area
     *
     * @var string
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
     * @var string
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
     * Using only to transmit additional parametr in callback functions
     * @var string
     */
    protected $_callbackFileDir;

    /**
     * @var Mage_Core_Model_Design_Config
     */
    protected $_config = null;

    /**
     * @var Mage_Core_Model_Design_Fallback
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
            $this->_fallback = Mage::getSingleton('core/design_fallback', array(
                'config' => $this->_config,
            ));
        }
    }

    /**
     * Set store
     *
     * @param  string|integer|Mage_Core_Model_Store $store
     * @return Mage_Core_Model_Design_Package
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
        if ($this->_store === null) {
            return Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Set package area
     *
     * @param  string $area
     * @return Mage_Core_Model_Design_Package
     */
    public function setArea($area)
    {
        $this->_area = $area;
        return $this;
    }

    /**
     * Retrieve package area
     *
     * @return unknown
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
     * @return Mage_Core_Model_Design_Package
     */
    public function setPackageName($name = '')
    {
        if (empty($name)) {
            // see, if exceptions for user-agents defined in config
            $customPackage = $this->_checkUserAgentAgainstRegexps('design/package/ua_regexp');
            if ($customPackage) {
                $this->_name = $customPackage;
            }
            else {
                $this->_name = Mage::getStoreConfig('design/package/name', $this->getStore());
            }
        }
        else {
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
        $oldValues = array();
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
        if (null === $this->_name) {
            $this->setPackageName();
        }
        return $this->_name;
    }

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
     * @return Mage_Core_Model_Design_Package
     */
    public function setTheme()
    {
        switch (func_num_args()) {
            case 1:
                foreach (array('layout', 'template', 'skin', 'locale') as $type) {
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

    public function getTheme($type)
    {
        if (empty($this->_theme[$type])) {
            $this->_theme[$type] = Mage::getStoreConfig('design/theme/'.$type, $this->getStore());
            if ($type!=='default' && empty($this->_theme[$type])) {
                $this->_theme[$type] = $this->getTheme('default');
                if (empty($this->_theme[$type])) {
                    $this->_theme[$type] = self::DEFAULT_THEME;
                }

                // "locale", "layout", "template"
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

    public function getDefaultTheme()
    {
        return self::DEFAULT_THEME;
    }

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
            $params['_theme'] = $this->getTheme( (isset($params['_type'])) ? $params['_type'] : '' );
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        return $this;
    }

    public function getBaseDir(array $params)
    {
        $this->updateParamDefaults($params);
        $baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
            $params['_area'].DS.$params['_package'].DS.$params['_theme'].DS.$params['_type'];
        return $baseDir;
    }

    public function getSkinBaseDir(array $params=array())
    {
        $params['_type'] = 'skin';
        $this->updateParamDefaults($params);
        $baseDir = (empty($params['_relative']) ? Mage::getBaseDir('skin').DS : '').
            $params['_area'].DS.$params['_package'].DS.$params['_theme'];
        return $baseDir;
    }

    public function getLocaleBaseDir(array $params=array())
    {
        $params['_type'] = 'locale';
        $this->updateParamDefaults($params);
        $baseDir = (empty($params['_relative']) ? Mage::getBaseDir('design').DS : '').
            $params['_area'].DS.$params['_package'].DS.$params['_theme'] . DS . 'locale' . DS .
            Mage::app()->getLocale()->getLocaleCode();
        return $baseDir;
    }

    public function getSkinBaseUrl(array $params=array())
    {
        $params['_type'] = 'skin';
        $this->updateParamDefaults($params);
        $baseUrl = Mage::getBaseUrl('skin', isset($params['_secure'])?(bool)$params['_secure']:null)
            .$params['_area'].'/'.$params['_package'].'/'.$params['_theme'].'/';
        return $baseUrl;
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
     * @param array $params
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
     * @param array $file
     * @param $params
     * @return string
     */
    protected function _renderFilename($file, array $params)
    {
        switch ($params['_type']) {
            case 'skin':
                $dir = $this->getSkinBaseDir($params);
                break;

            case 'locale':
                $dir = $this->getLocaleBasedir($params);
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
     * @param array &$params
     * @param array $fallbackScheme
     * @return string
     */
    protected function _fallback($file, array &$params, array $fallbackScheme = array(array()))
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
     * @param array $params
     * @return string
     */
    public function getFilename($file, array $params)
    {
        Varien_Profiler::start(__METHOD__);
        $this->updateParamDefaults($params);
        $result = $this->_fallback(
            $file,
            $params,
            $this->_fallback->getFallbackScheme(
                $params['_area'],
                $params['_package'],
                $params['_theme']
            )
        );
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }

    public function getLayoutFilename($file, array $params=array())
    {
        $params['_type'] = 'layout';
        return $this->getFilename($file, $params);
    }

    public function getTemplateFilename($file, array $params=array())
    {
        $params['_type'] = 'template';
        return $this->getFilename($file, $params);
    }

    public function getLocaleFileName($file, array $params=array())
    {
        $params['_type'] = 'locale';
        return $this->getFilename($file, $params);
    }

    /**
     * Get skin file url
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getSkinUrl($file = null, array $params = array())
    {
        Varien_Profiler::start(__METHOD__);
        if (empty($params['_type'])) {
            $params['_type'] = 'skin';
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        $this->updateParamDefaults($params);
        if (!empty($file)) {
            $result = $this->_fallback(
                $file,
                $params,
                $this->_fallback->getFallbackScheme(
                    $params['_area'],
                    $params['_package'],
                    $params['_theme']
                )
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
     * @return string
     */
    public function getThemeList($package = null)
    {
        $result = array();

        if (is_null($package)){
            foreach ($this->getPackageList() as $package){
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
    private function _listDirectories($path, $fullPath = false)
    {
        $result = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {
                if (substr($entry, 0, 1) == '.' || !is_dir($path . DS . $entry)){
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
     * @return mixed
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

        $regexps = @unserialize($configValueSerialized);

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
     */
    public static function getPackageByUserAgent(array $rules, $regexpsConfigPath = 'path_mock')
    {
        foreach ($rules as $rule) {
            if (!empty(self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']])) {
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }

            $regexp = '/' . trim($rule['regexp'], '/') . '/';

            if (@preg_match($regexp, $_SERVER['HTTP_USER_AGENT'])) {
                self::$_regexMatchCache[$rule['regexp']][$_SERVER['HTTP_USER_AGENT']] = true;
                self::$_customThemeTypeCache[$regexpsConfigPath] = $rule['value'];
                return $rule['value'];
            }
        }

        return false;
    }

    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        $targetFilename = md5(implode(',', $files)) . '.js';
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
     * @param $files
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
        if (false === $port) {
            $port = $isSecure ? 443 : 80;
        }

        // merge into target file
        $targetFilename = md5(implode(',', $files) . "|{$hostname}|{$port}") . '.css';
        $mergeFilesResult = $this->_mergeFiles(
            $files, $targetDir . DS . $targetFilename,
            false,
            array($this, 'beforeMergeCss'),
            'css'
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
     * @param array $srcFiles
     * @param string|bool $targetFile - file path to be written
     * @param bool $mustMerge
     * @param callback $beforeMergeCallback
     * @param array|string $extensionsFilter
     * @return bool|string
     */
    protected function _mergeFiles(array $srcFiles, $targetFile = false,
        $mustMerge = false, $beforeMergeCallback = null, $extensionsFilter = array())
    {
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
                $extensionsFilter
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
                $extensionsFilter
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
        $result = (bool)$this->_initMergerDir('js', true);
        $result = (bool)$this->_initMergerDir('css', true) && $result;
        return (bool)$this->_initMergerDir('css_secure', true) && $result;
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
        $mediaDir = Mage::getBaseDir('media');
        try {
            $dir = Mage::getBaseDir('media') . DS . $dirRelativeName;
            if ($cleanup) {
                Varien_Io_File::rmdirRecursive($dir);
                Mage::helper('core/file_storage_database')->deleteFolder($dir);
            }
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            return is_writeable($dir) ? $dir : false;
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
       $contents = preg_replace_callback($cssImport, array($this, '_cssMergerImportCallback'), $contents);

       $cssUrl = '/url\\(\\s*(?!data:)([^\\)\\s]+)\\s*\\)?/';
       $contents = preg_replace_callback($cssUrl, array($this, '_cssMergerUrlCallback'), $contents);

       return $contents;
    }

    /**
     * Set file dir for css file
     *
     * @param string $file
     */
    protected function _setCallbackFileDir($file)
    {
       $file = str_replace(Mage::getBaseDir().DS, '', $file);
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
        $uri = ($quote == '') ? $match[1] : substr($match[1], 1, strlen($match[1]) - 2);
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

            if ('skin' == $fileDirParts[0]) {
                $baseUrl = Mage::getBaseUrl('skin', $secure);
                $fileDirParts = array_slice($fileDirParts, 1);
            } elseif ('media' == $fileDirParts[0]) {
                $baseUrl = Mage::getBaseUrl('media', $secure);
                $fileDirParts = array_slice($fileDirParts, 1);
            } else {
                $baseUrl = Mage::getBaseUrl('web', $secure);
            }

            foreach ($pathParts as $key=>$part) {
                if ($part == '.' || $part == '..') {
                    unset($pathParts[$key]);
                }
                if ($part == '..' && count($fileDirParts)) {
                    $fileDirParts = array_slice($fileDirParts, 0, count($fileDirParts) - 1);
                }
            }

            if (count($fileDirParts)) {
                $fileDir = implode('/', $fileDirParts).'/';
            }

            $uri = $baseUrl.$fileDir.implode('/', $pathParts);
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
