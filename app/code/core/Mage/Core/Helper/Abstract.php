<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract helper
 *
 * @package    Mage_Core
 */
abstract class Mage_Core_Helper_Abstract
{
    /**
     * Helper module name
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Request object
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Layout model object
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    protected array $modulesDisabled = [];

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        if (!$this->_request) {
            $this->_request = Mage::app()->getRequest();
        }
        return $this->_request;
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    protected function _loadCache($id)
    {
        return Mage::app()->loadCache($id);
    }

    /**
     * Saving cache
     *
     * @param mixed $data
     * @param string $id
     * @param array $tags
     * @param null|false|int $lifeTime
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _saveCache($data, $id, $tags = [], $lifeTime = false)
    {
        Mage::app()->saveCache($data, $id, $tags, $lifeTime);
        return $this;
    }

    /**
     * Removing cache
     *
     * @param   string $id
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _removeCache($id)
    {
        Mage::app()->removeCache($id);
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _cleanCache($tags = [])
    {
        Mage::app()->cleanCache($tags);
        return $this;
    }

    /**
     * Retrieve helper module name
     *
     * @return string
     */
    protected function _getModuleName()
    {
        if (!$this->_moduleName) {
            $class = static::class;
            $this->_moduleName = implode('_', array_slice(explode('_', $class), 0, 2));
        }
        return $this->_moduleName;
    }

    /**
     * Check whether the module output is enabled in Configuration
     *
     * @param string $moduleName Full module name
     * @return bool
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!$this->isModuleEnabled($moduleName)) {
            return false;
        }

        return !Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
    }

    /**
     * Check is module exists and enabled in global config.
     *
     * @param string $moduleName the full module name, example Mage_Core
     * @return bool
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (array_key_exists($moduleName, $this->modulesDisabled)) {
            return $this->modulesDisabled[$moduleName];
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return $this->modulesDisabled[$moduleName] = false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string) $isActive, ['true', '1'])) {
            return $this->modulesDisabled[$moduleName] = false;
        }
        return $this->modulesDisabled[$moduleName] = true;
    }

    /**
     * Translate
     *
     * @return string
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * @param string|string[] $data
     * @param array|null $allowedTags
     * @return null|string|string[]
     *
     * @see self::escapeHtml()
     * @deprecated after 1.4.0.0-rc1
     */
    public function htmlEscape($data, $allowedTags = null)
    {
        return $this->escapeHtml($data, $allowedTags);
    }

    /**
     * Escape html entities
     *
     * @param string|string[] $data
     * @param array|null $allowedTags
     * @return null|string|string[]
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } elseif (is_string($data) && strlen($data)) {
            // process single item
            if (is_array($allowedTags) && !empty($allowedTags)) {
                $allowed = implode('|', $allowedTags);
                $result = preg_replace('/<([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)>/si', '##$1$2$3##', $data);
                $result = htmlspecialchars($result, ENT_COMPAT, 'UTF-8', false);
                $result = preg_replace('/##([\/\s\r\n]*)(' . $allowed . ')([\/\s\r\n]*)##/si', '<$1$2$3>', $result);
            } else {
                $result = htmlspecialchars($data, ENT_COMPAT, 'UTF-8', false);
            }
        } else {
            $result = $data;
        }
        return $result;
    }

    /**
     * Remove html tags, but leave "<" and ">" signs
     *
     * @param   string $html
     * @return  string
     */
    public function removeTags($html)
    {
        $html = preg_replace_callback(
            "# <(?![/a-z]) | (?<=\s)>(?![a-z]) #xi",
            function ($matches) {
                return htmlentities($matches[0]);
            },
            $html,
        );
        $html =  strip_tags($html);
        return htmlspecialchars_decode($html);
    }

    /**
     * Wrapper for standard strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param null|string|string[] $allowableTags
     * @param bool $escape
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        if ($data === null) {
            return '';
        }
        $result = strip_tags($data, $allowableTags);
        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }

    /**
     * @param string $data
     * @return string
     * @deprecated after 1.4.0.0-rc1
     * @see self::escapeHtml()
     */
    public function urlEscape($data)
    {
        return $this->escapeUrl($data);
    }

    /**
     * Escape html entities in url
     *
     * @param string $data
     * @return string
     */
    public function escapeUrl($data)
    {
        return htmlspecialchars(
            $this->escapeScriptIdentifiers((string) $data),
            ENT_COMPAT | ENT_HTML5 | ENT_HTML401,
            'UTF-8',
        );
    }

    /**
     * Remove `\t`,`\n`,`\r`,`\0`,`\x0B:` symbols from the string.
     *
     * @param string $data
     * @return string
     */
    public function escapeSpecial($data)
    {
        $specialSymbolsFiltrationPattern = '/[\t\n\r\0\x0B]+/';

        return (string) preg_replace($specialSymbolsFiltrationPattern, '', $data);
    }

    /**
     * Remove `javascript:`, `vbscript:`, `data:` words from the string.
     *
     * @param string $data
     * @return string
     */
    public function escapeScriptIdentifiers($data)
    {
        $scripIdentifiersFiltrationPattern = '/((javascript(\\\\x3a|:|%3A))|(data(\\\\x3a|:|%3A))|(vbscript:))|'
            . '((\\\\x6A\\\\x61\\\\x76\\\\x61\\\\x73\\\\x63\\\\x72\\\\x69\\\\x70\\\\x74(\\\\x3a|:|%3A))|'
            . '(\\\\x64\\\\x61\\\\x74\\\\x61(\\\\x3a|:|%3A)))/i';

        $preFilteredData = $this->escapeSpecial($data);
        $filteredData = preg_replace($scripIdentifiersFiltrationPattern, ':', $preFilteredData) ?: '';
        if (preg_match($scripIdentifiersFiltrationPattern, $filteredData)) {
            $filteredData = $this->escapeScriptIdentifiers($filteredData);
        }

        return $filteredData;
    }

    /**
     * Escape quotes in java script
     *
     * @param string|string[] $data
     * @param string $quote
     * @return string|string[]
     */
    public function jsQuoteEscape($data, $quote = '\'')
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = str_replace($quote, '\\' . $quote, $item);
            }
            return $result;
        }
        return str_replace($quote, '\\' . $quote, $data);
    }

    /**
     * Escape quotes inside html attributes
     * Use $addSlashes = false for escaping js that inside html attribute (onClick, onSubmit etc)
     *
     * @param string $data
     * @param bool $addSlashes
     * @return string
     */
    public function quoteEscape($data, $addSlashes = false)
    {
        if (!$data) {
            return $data;
        }
        if ($addSlashes === true) {
            $data = addslashes($data);
        }
        return htmlspecialchars($data, ENT_QUOTES, null, false);
    }

    /**
     * Retrieve url
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function _getUrl($route, $params = [])
    {
        return Mage::getUrl($route, $params);
    }

    /**
     * Declare layout
     *
     * @param   Mage_Core_Model_Layout $layout
     * @return  Mage_Core_Helper_Abstract
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Retrieve layout model object
     *
     * @return Mage_Core_Model_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     *  base64_encode() for URLs encoding
     *
     *  @param    string $url
     *  @return   string
     */
    public function urlEncode($url)
    {
        return strtr(base64_encode($url), '+/=', '-_,');
    }

    /**
     *  base64_decode() for URLs decoding
     *
     *  @param    string $url
     *  @return   string
     */
    public function urlDecode($url)
    {
        $url = base64_decode(strtr($url, '-_,', '+/='));
        return Mage::getSingleton('core/url')->sessionUrlVar($url);
    }

    /**
     *  base64_decode() and escape quotes in url
     *
     *  @param    string $url
     *  @return   string
     */
    public function urlDecodeAndEscape($url)
    {
        $url = $this->urlDecode($url);
        $quote = ['\'', '"'];
        $replace = ['%27', '%22'];
        return str_replace($quote, $replace, $url);
    }

    /**
     *   Translate array
     *
     *  @param    array $arr
     *  @return   array
     */
    public function translateArray($arr = [])
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $v = self::translateArray($v);
            } elseif ($k === 'label') {
                $v = self::__($v);
            }
            $arr[$k] = $v;
        }
        return $arr;
    }

    /**
     * Check for tags in multidimensional arrays
     *
     * @param string|array $data
     * @param array $arrayKeys keys of the array being checked that are excluded and included in the check
     * @param bool $skipTags skip transferred array keys, if false then check only them
     * @return bool
     */
    public function hasTags($data, array $arrayKeys = [], $skipTags = true)
    {
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                if ($skipTags && in_array($key, $arrayKeys)) {
                    continue;
                }
                if (is_array($item)) {
                    if ($this->hasTags($item, $arrayKeys, $skipTags)) {
                        return true;
                    }
                } elseif ((bool) strcmp($item, $this->removeTags($item))
                    || (bool) strcmp($key, $this->removeTags($key))
                ) {
                    if (!$skipTags && !in_array($key, $arrayKeys)) {
                        continue;
                    }
                    return true;
                }
            }
            return false;
        } elseif (is_string($data)) {
            if ((bool) strcmp($data, $this->removeTags($data))) {
                return true;
            }
        }
        return false;
    }
}
