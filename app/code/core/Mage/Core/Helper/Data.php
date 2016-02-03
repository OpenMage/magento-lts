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
 * Core data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_DEFAULT_COUNTRY              = 'general/country/default';
    const XML_PATH_PROTECTED_FILE_EXTENSIONS    = 'general/file/protected_extensions';
    const XML_PATH_PUBLIC_FILES_VALID_PATHS     = 'general/file/public_files_valid_paths';
    const XML_PATH_ENCRYPTION_MODEL             = 'global/helpers/core/encryption_model';
    const XML_PATH_DEV_ALLOW_IPS                = 'dev/restrict/allow_ips';
    const XML_PATH_CACHE_BETA_TYPES             = 'global/cache/betatypes';
    const XML_PATH_CONNECTION_TYPE              = 'global/resources/default_setup/connection/type';

    const CHARS_LOWERS                          = 'abcdefghijklmnopqrstuvwxyz';
    const CHARS_UPPERS                          = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const CHARS_DIGITS                          = '0123456789';
    const CHARS_SPECIALS                        = '!$*+-.=?@^_|~';
    const CHARS_PASSWORD_LOWERS                 = 'abcdefghjkmnpqrstuvwxyz';
    const CHARS_PASSWORD_UPPERS                 = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const CHARS_PASSWORD_DIGITS                 = '23456789';
    const CHARS_PASSWORD_SPECIALS               = '!$*-.=?@_';

    /**
     * Config pathes to merchant country code and merchant VAT number
     */
    const XML_PATH_MERCHANT_COUNTRY_CODE = 'general/store_information/merchant_country';
    const XML_PATH_MERCHANT_VAT_NUMBER = 'general/store_information/merchant_vat_number';
    const XML_PATH_EU_COUNTRIES_LIST = 'general/country/eu_countries';

    /**
     * Const for correct dividing decimal values
     */
    const DIVIDE_EPSILON = 10000;

    /**
     * @var Mage_Core_Model_Encryption
     */
    protected $_encryptor = null;

    protected $_allowedFormats = array(
        Mage_Core_Model_Locale::FORMAT_TYPE_FULL,
        Mage_Core_Model_Locale::FORMAT_TYPE_LONG,
        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
        Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
    );


    /**
     * @return Mage_Core_Model_Encryption
     */
    public function getEncryptor()
    {
        if ($this->_encryptor === null) {
            $encryptionModel = (string)Mage::getConfig()->getNode(self::XML_PATH_ENCRYPTION_MODEL);
            if ($encryptionModel) {
                $this->_encryptor = new $encryptionModel;
            } else {
                $this->_encryptor = Mage::getModel('core/encryption');
            }

            $this->_encryptor->setHelper($this);
        }
        return $this->_encryptor;
    }

    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  mixed
     */
    public static function currency($value, $format = true, $includeContainer = true)
    {
        return self::currencyByStore($value, null, $format, $includeContainer);
    }

    /**
     * Convert and format price value for specified store
     *
     * @param   float $value
     * @param   int|Mage_Core_Model_Store $store
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  mixed
     */
    public static function currencyByStore($value, $store = null, $format = true, $includeContainer = true)
    {
        try {
            if (!($store instanceof Mage_Core_Model_Store)) {
                $store = Mage::app()->getStore($store);
            }

            $value = $store->convertPrice($value, $format, $includeContainer);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }

        return $value;
    }

    /**
     * Format and convert currency using current store option
     *
     * @param   float $value
     * @param   bool $includeContainer
     * @return  string
     */
    public function formatCurrency($value, $includeContainer = true)
    {
        return $this->currency($value, true, $includeContainer);
    }

    /**
     * Formats price
     *
     * @param float $price
     * @param bool $includeContainer
     * @return string
     */
    public function formatPrice($price, $includeContainer = true)
    {
        return Mage::app()->getStore()->formatPrice($price, $includeContainer);
    }

    /**
     * Format date using current locale options and time zone.
     *
     * @param   date|Zend_Date|null $date
     * @param   string              $format   See Mage_Core_Model_Locale::FORMAT_TYPE_* constants
     * @param   bool                $showTime Whether to include time
     * @return  string
     */
    public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        if (!in_array($format, $this->_allowedFormats, true)) {
            return $date;
        }
        if (!($date instanceof Zend_Date) && $date && !strtotime($date)) {
            return '';
        }
        if (is_null($date)) {
            $date = Mage::app()->getLocale()->date(Mage::getSingleton('core/date')->gmtTimestamp(), null, null);
        } else if (!$date instanceof Zend_Date) {
            $date = Mage::app()->getLocale()->date(strtotime($date), null, null);
        }

        if ($showTime) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        } else {
            $format = Mage::app()->getLocale()->getDateFormat($format);
        }

        return $date->toString($format);
    }

    /**
     * Format time using current locale options
     *
     * @param   date|Zend_Date|null $time
     * @param   string              $format
     * @param   bool                $showDate
     * @return  string
     */
    public function formatTime($time = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showDate = false)
    {
        if (!in_array($format, $this->_allowedFormats, true)) {
            return $time;
        }

        if (is_null($time)) {
            $date = Mage::app()->getLocale()->date(time());
        } else if ($time instanceof Zend_Date) {
            $date = $time;
        } else {
            $date = Mage::app()->getLocale()->date(strtotime($time));
        }

        if ($showDate) {
            $format = Mage::app()->getLocale()->getDateTimeFormat($format);
        } else {
            $format = Mage::app()->getLocale()->getTimeFormat($format);
        }

        return $date->toString($format);
    }

    /**
     * Encrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if (!Mage::isInstalled()) {
            return $data;
        }
        return $this->getEncryptor()->encrypt($data);
    }

    /**
     * Decrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if (!Mage::isInstalled()) {
            return $data;
        }
        return $this->getEncryptor()->decrypt($data);
    }

    public function validateKey($key)
    {
        return $this->getEncryptor()->validateKey($key);
    }

    public function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = self::CHARS_LOWERS . self::CHARS_UPPERS . self::CHARS_DIGITS;
        }
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }

    /**
     * Generate salted hash from password
     *
     * @param string $password
     * @param string|integer|boolean $salt
     */
    public function getHash($password, $salt = false)
    {
        return $this->getEncryptor()->getHash($password, $salt);
    }

    public function validateHash($password, $hash)
    {
        return $this->getEncryptor()->validateHash($password, $hash);
    }

    /**
     * Retrieve store identifier
     *
     * @param   mixed $store
     * @return  int
     */
    public function getStoreId($store=null)
    {
        return Mage::app()->getStore($store)->getId();
    }

    public function removeAccents($string, $german=false)
    {
        static $replacements;

        if (empty($replacements[$german])) {
            $subst = array(
                // single ISO-8859-1 letters
                192=>'A', 193=>'A', 194=>'A', 195=>'A', 196=>'A', 197=>'A', 199=>'C',
                208=>'D', 200=>'E', 201=>'E', 202=>'E', 203=>'E', 204=>'I', 205=>'I',
                206=>'I', 207=>'I', 209=>'N', 210=>'O', 211=>'O', 212=>'O', 213=>'O',
                214=>'O', 216=>'O', 138=>'S', 217=>'U', 218=>'U', 219=>'U', 220=>'U',
                221=>'Y', 142=>'Z', 224=>'a', 225=>'a', 226=>'a', 227=>'a', 228=>'a',
                229=>'a', 231=>'c', 232=>'e', 233=>'e', 234=>'e', 235=>'e', 236=>'i',
                237=>'i', 238=>'i', 239=>'i', 241=>'n', 240=>'o', 242=>'o', 243=>'o',
                244=>'o', 245=>'o', 246=>'o', 248=>'o', 154=>'s', 249=>'u', 250=>'u',
                251=>'u', 252=>'u', 253=>'y', 255=>'y', 158=>'z',
                // HTML entities
                258=>'A', 260=>'A', 262=>'C', 268=>'C', 270=>'D', 272=>'D', 280=>'E',
                282=>'E', 286=>'G', 304=>'I', 313=>'L', 317=>'L', 321=>'L', 323=>'N',
                327=>'N', 336=>'O', 340=>'R', 344=>'R', 346=>'S', 350=>'S', 354=>'T',
                356=>'T', 366=>'U', 368=>'U', 377=>'Z', 379=>'Z', 259=>'a', 261=>'a',
                263=>'c', 269=>'c', 271=>'d', 273=>'d', 281=>'e', 283=>'e', 287=>'g',
                305=>'i', 322=>'l', 314=>'l', 318=>'l', 324=>'n', 328=>'n', 337=>'o',
                341=>'r', 345=>'r', 347=>'s', 351=>'s', 357=>'t', 355=>'t', 367=>'u',
                369=>'u', 378=>'z', 380=>'z',
                // ligatures
                198=>'Ae', 230=>'ae', 140=>'Oe', 156=>'oe', 223=>'ss',
            );

            if ($german) {
                // umlauts
                $subst = array_merge($subst, array(
                    196=>'Ae', 228=>'ae', 214=>'Oe', 246=>'oe', 220=>'Ue', 252=>'ue'
                ));
            }

            $replacements[$german] = array();
            foreach ($subst as $k=>$v) {
                $replacements[$german][$k<256 ? chr($k) : '&#'.$k.';'] = $v;
            }
        }

        // convert string from default database format (UTF-8)
        // to encoding which replacement arrays made with (ISO-8859-1)
        if ($s = @iconv('UTF-8', 'ISO-8859-1', $string)) {
            $string = $s;
        }

        // Replace
        $string = strtr($string, $replacements[$german]);

        return $string;
    }

    public function isDevAllowed($storeId=null)
    {
        $allow = true;

        $allowedIps = Mage::getStoreConfig(self::XML_PATH_DEV_ALLOW_IPS, $storeId);
        $remoteAddr = Mage::helper('core/http')->getRemoteAddr();
        if (!empty($allowedIps) && !empty($remoteAddr)) {
            $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
            if (array_search($remoteAddr, $allowedIps) === false
                && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {
                $allow = false;
            }
        }

        return $allow;
    }

    /**
     * Get information about available cache types
     *
     * @return array
     */
    public function getCacheTypes()
    {
        $types = array();
        $config = Mage::getConfig()->getNode(Mage_Core_Model_Cache::XML_PATH_TYPES);
        foreach ($config->children() as $type=>$node) {
            $types[$type] = (string)$node->label;
        }
        return $types;
    }

    /**
     * Get information about available cache beta types
     *
     * @return array
     */
    public function getCacheBetaTypes()
    {
        $types = array();
        $config = Mage::getConfig()->getNode(self::XML_PATH_CACHE_BETA_TYPES);
        if ($config) {
            foreach ($config->children() as $type=>$node) {
                $types[$type] = (string)$node->label;
            }
        }
        return $types;
    }

    /**
     * Copy data from object|array to object|array containing fields
     * from fieldset matching an aspect.
     *
     * Contents of $aspect are a field name in target object or array.
     * If '*' - will be used the same name as in the source object or array.
     *
     * @param string $fieldset
     * @param string $aspect
     * @param array|Varien_Object $source
     * @param array|Varien_Object $target
     * @param string $root
     * @return boolean
     */
    public function copyFieldset($fieldset, $aspect, $source, $target, $root='global')
    {
        if (!(is_array($source) || $source instanceof Varien_Object)
            || !(is_array($target) || $target instanceof Varien_Object)) {

            return false;
        }
        $fields = Mage::getConfig()->getFieldset($fieldset, $root);
        if (!$fields) {
            return false;
        }

        $sourceIsArray = is_array($source);
        $targetIsArray = is_array($target);

        $result = false;
        foreach ($fields as $code=>$node) {
            if (empty($node->$aspect)) {
                continue;
            }

            if ($sourceIsArray) {
                $value = isset($source[$code]) ? $source[$code] : null;
            } else {
                $value = $source->getDataUsingMethod($code);
            }

            $targetCode = (string)$node->$aspect;
            $targetCode = $targetCode == '*' ? $code : $targetCode;

            if ($targetIsArray) {
                $target[$targetCode] = $value;
            } else {
                $target->setDataUsingMethod($targetCode, $value);
            }

            $result = true;
        }

        $eventName = sprintf('core_copy_fieldset_%s_%s', $fieldset, $aspect);
        Mage::dispatchEvent($eventName, array(
            'target' => $target,
            'source' => $source,
            'root'   => $root
        ));

        return $result;
    }

    /**
     * Decorate a plain array of arrays or objects
     * The array actually can be an object with Iterator interface
     *
     * Keys with prefix_* will be set:
     * *_is_first - if the element is first
     * *_is_odd / *_is_even - for odd/even elements
     * *_is_last - if the element is last
     *
     * The respective key/attribute will be set to element, depending on object it is or array.
     * Varien_Object is supported.
     *
     * $forceSetAll true will cause to set all possible values for all elements.
     * When false (default), only non-empty values will be set.
     *
     * @param mixed $array
     * @param string $prefix
     * @param bool $forceSetAll
     * @return mixed
     */
    public function decorateArray($array, $prefix = 'decorated_', $forceSetAll = false)
    {
        // check if array or an object to be iterated given
        if (!(is_array($array) || is_object($array))) {
            return $array;
        }

        $keyIsFirst = "{$prefix}is_first";
        $keyIsOdd   = "{$prefix}is_odd";
        $keyIsEven  = "{$prefix}is_even";
        $keyIsLast  = "{$prefix}is_last";

        $count  = count($array); // this will force Iterator to load
        $i      = 0;
        $isEven = false;
        foreach ($array as $key => $element) {
            if (is_object($element)) {
                $this->_decorateArrayObject($element, $keyIsFirst, (0 === $i), $forceSetAll || (0 === $i));
                $this->_decorateArrayObject($element, $keyIsOdd, !$isEven, $forceSetAll || !$isEven);
                $this->_decorateArrayObject($element, $keyIsEven, $isEven, $forceSetAll || $isEven);
                $isEven = !$isEven;
                $i++;
                $this->_decorateArrayObject($element, $keyIsLast, ($i === $count), $forceSetAll || ($i === $count));
            }
            elseif (is_array($element)) {
                if ($forceSetAll || (0 === $i)) {
                    $array[$key][$keyIsFirst] = (0 === $i);
                }
                if ($forceSetAll || !$isEven) {
                    $array[$key][$keyIsOdd] = !$isEven;
                }
                if ($forceSetAll || $isEven) {
                    $array[$key][$keyIsEven] = $isEven;
                }
                $isEven = !$isEven;
                $i++;
                if ($forceSetAll || ($i === $count)) {
                    $array[$key][$keyIsLast] = ($i === $count);
                }
            }
        }

        return $array;
    }

    private function _decorateArrayObject($element, $key, $value, $dontSkip) {
        if ($dontSkip) {
            if ($element instanceof Varien_Object) {
                $element->setData($key, $value);
            }
            else {
                $element->$key = $value;
            }
        }
    }

    /**
     * Transform an assoc array to SimpleXMLElement object
     * Array has some limitations. Appropriate exceptions will be thrown
     *
     * @param array $array
     * @param string $rootName
     * @return SimpleXMLElement
     * @throws Exception
     */
    public function assocToXml(array $array, $rootName = '_')
    {
        if (empty($rootName) || is_numeric($rootName)) {
            throw new Exception('Root element must not be empty or numeric');
        }

        $xmlstr = <<<XML
<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<$rootName></$rootName>
XML;
        $xml = new SimpleXMLElement($xmlstr);
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                throw new Exception('Array root keys must not be numeric.');
            }
        }
        return self::_assocToXml($array, $rootName, $xml);
    }

    /**
     * Function, that actually recursively transforms array to xml
     *
     * @param array $array
     * @param string $rootName
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     * @throws Exception
     */
    private function _assocToXml(array $array, $rootName, SimpleXMLElement &$xml)
    {
        $hasNumericKey = false;
        $hasStringKey  = false;
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (is_string($key)) {
                    if ($key === $rootName) {
                        throw new Exception('Associative key must not be the same as its parent associative key.');
                    }
                    $hasStringKey = true;
                    $xml->$key = $value;
                }
                elseif (is_int($key)) {
                    $hasNumericKey = true;
                    $xml->{$rootName}[$key] = $value;
                }
            }
            else {
                self::_assocToXml($value, $key, $xml->$key);
            }
        }
        if ($hasNumericKey && $hasStringKey) {
            throw new Exception('Associative and numeric keys must not be mixed at one level.');
        }
        return $xml;
    }

    /**
     * Transform SimpleXMLElement to associative array
     * SimpleXMLElement must be conform structure, generated by assocToXml()
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    public function xmlToAssoc(SimpleXMLElement $xml)
    {
        $array = array();
        foreach ($xml as $key => $value) {
            if (isset($value->$key)) {
                $i = 0;
                foreach ($value->$key as $v) {
                    $array[$key][$i++] = (string)$v;
                }
            }
            else {
                // try to transform it into string value, trimming spaces between elements
                $array[$key] = trim((string)$value);
                if (empty($array[$key]) && !empty($value)) {
                    $array[$key] = self::xmlToAssoc($value);
                }
                // untrim strings values
                else {
                    $array[$key] = (string)$value;
                }
            }
        }
        return $array;
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
        /* @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getSingleton('core/translate_inline');
        if ($inline->isAllowed()) {
            $inline->setIsJson(true);
            $inline->processResponseBody($json);
            $inline->setIsJson(false);
        }

        return $json;
    }

    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue
     * @return mixed
     */
    public function jsonDecode($encodedValue, $objectDecodeType = Zend_Json::TYPE_ARRAY)
    {
        return Zend_Json::decode($encodedValue, $objectDecodeType);
    }

    /**
     * Generate a hash from unique ID
     * @param $prefix
     * @return string
     */
    public function uniqHash($prefix = '')
    {
        return $prefix . md5(uniqid(microtime().mt_rand(), true));
    }

    /**
     * Merge specified files into one
     *
     * By default will not merge, if there is already merged file exists and it
     * was modified after its components
     * If target file is specified, will attempt to write merged contents into it,
     * otherwise will return merged content
     * May apply callback to each file contents. Callback gets parameters:
     * (<existing system filename>, <file contents>)
     * May filter files by specified extension(s)
     * Returns false on error
     *
     * @param array $srcFiles
     * @param string|false $targetFile - file path to be written
     * @param bool $mustMerge
     * @param callback $beforeMergeCallback
     * @param array|string $extensionsFilter
     * @return bool|string
     */
    public function mergeFiles(array $srcFiles, $targetFile = false, $mustMerge = false,
        $beforeMergeCallback = null, $extensionsFilter = array())
    {
        try {
            // check whether merger is required
            $shouldMerge = $mustMerge || !$targetFile;
            if (!$shouldMerge) {
                if (!file_exists($targetFile)) {
                    $shouldMerge = true;
                } else {
                    $targetMtime = filemtime($targetFile);
                    foreach ($srcFiles as $file) {
                        if (!file_exists($file) || @filemtime($file) > $targetMtime) {
                            $shouldMerge = true;
                            break;
                        }
                    }
                }
            }

            // merge contents into the file
            if ($shouldMerge) {
                if ($targetFile && !is_writeable(dirname($targetFile))) {
                    // no translation intentionally
                    throw new Exception(sprintf('Path %s is not writeable.', dirname($targetFile)));
                }

                // filter by extensions
                if ($extensionsFilter) {
                    if (!is_array($extensionsFilter)) {
                        $extensionsFilter = array($extensionsFilter);
                    }
                    if (!empty($srcFiles)){
                        foreach ($srcFiles as $key => $file) {
                            $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (!in_array($fileExt, $extensionsFilter)) {
                                unset($srcFiles[$key]);
                            }
                        }
                    }
                }
                if (empty($srcFiles)) {
                    // no translation intentionally
                    throw new Exception('No files to compile.');
                }

                $data = '';
                foreach ($srcFiles as $file) {
                    if (!file_exists($file)) {
                        continue;
                    }
                    $contents = file_get_contents($file) . "\n";
                    if ($beforeMergeCallback && is_callable($beforeMergeCallback)) {
                        $contents = call_user_func($beforeMergeCallback, $file, $contents);
                    }
                    $data .= $contents;
                }
                if (!$data) {
                    // no translation intentionally
                    throw new Exception(sprintf("No content found in files:\n%s", implode("\n", $srcFiles)));
                }
                if ($targetFile) {
                    file_put_contents($targetFile, $data, LOCK_EX);
                } else {
                    return $data; // no need to write to file, just return data
                }
            }

            return true; // no need in merger or merged into file successfully
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * Return default country code
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return string
     */
    public function getDefaultCountry($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_COUNTRY, $store);
    }

    /**
     * Return list with protected file extensions
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return array
     */
    public function getProtectedFileExtensions($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PROTECTED_FILE_EXTENSIONS, $store);
    }

    /**
     * Return list with public files valid paths
     *
     * @return array
     */
    public function getPublicFilesValidPath()
    {
        return Mage::getStoreConfig(self::XML_PATH_PUBLIC_FILES_VALID_PATHS);
    }

    /**
     * Check LFI protection
     *
     * @throws Mage_Core_Exception
     * @param string $name
     * @return bool
     */
    public function checkLfiProtection($name)
    {
        if (preg_match('#\.\.[\\\/]#', $name)) {
            throw new Mage_Core_Exception($this->__('Requested file may not include parent directory traversal ("../", "..\\" notation)'));
        }
        return true;
    }

    /**
     * Check whether database compatible mode is used (configs enable it for MySQL by default).
     *
     * @return bool
     */
    public function useDbCompatibleMode()
    {
        $connType = (string) Mage::getConfig()->getNode(self::XML_PATH_CONNECTION_TYPE);
        $path = 'global/resource/connection/types/' . $connType . '/compatibleMode';
        $value = (string) Mage::getConfig()->getNode($path);
        return (bool) $value;
    }

    /**
     * Retrieve merchant country code
     *
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return string
     */
    public function getMerchantCountryCode($store = null)
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_MERCHANT_COUNTRY_CODE, $store);
    }

    /**
     * Retrieve merchant VAT number
     *
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return string
     */
    public function getMerchantVatNumber($store = null)
    {
        return (string) Mage::getStoreConfig(self::XML_PATH_MERCHANT_VAT_NUMBER, $store);
    }

    /**
     * Check whether specified country is in EU countries list
     *
     * @param string $countryCode
     * @param null|int $storeId
     * @return bool
     */
    public function isCountryInEU($countryCode, $storeId = null)
    {
        $euCountries = explode(',', Mage::getStoreConfig(self::XML_PATH_EU_COUNTRIES_LIST, $storeId));
        return in_array($countryCode, $euCountries);
    }

    /**
     * Returns the floating point remainder (modulo) of the division of the arguments
     *
     * @param float|int $dividend
     * @param float|int $divisor
     * @return float|int
     */
    public function getExactDivision($dividend, $divisor)
    {
        $epsilon = $divisor / self::DIVIDE_EPSILON;

        $remainder = fmod($dividend, $divisor);
        if (abs($remainder - $divisor) < $epsilon || abs($remainder) < $epsilon) {
            $remainder = 0;
        }

        return $remainder;
    }

    /**
     * Escaping CSV-data
     *
     * Security enchancement for CSV data processing by Excel-like applications.
     * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1054702
     *
     * @param $data
     * @return array
     */
    public function getEscapedCSVData(array $data)
    {
        if (Mage::getStoreConfigFlag(Mage_ImportExport_Model_Export_Adapter_Csv::CONFIG_ESCAPING_FLAG)) {
            foreach ($data as $key => $value) {
                $value = (string)$value;

                $firstLetter = substr($value, 0, 1);
                if ($firstLetter !== false and in_array($firstLetter, array("=", "+", "-"))) {
                    $data[$key] = ' ' . $value;
                }
            }
        }
        return $data;
    }

    /**
     * UnEscapes CSV data
     *
     * @param mixed $data
     * @return mixed array
     */
    public function unEscapeCSVData($data)
    {
        if (is_array($data) and Mage::getStoreConfigFlag(Mage_ImportExport_Model_Export_Adapter_Csv::CONFIG_ESCAPING_FLAG)) {

            foreach ($data as $key => $value) {
                $value = (string)$value;

                if (preg_match("/^ [=\-+]/", $value)) {
                    $data[$key] = ltrim($value);
                }
            }
        }
        return $data;
    }
}
