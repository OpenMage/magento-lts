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
 * Abstract helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Layout model object
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Retrieve request object
     *
     * @return Zend_Controller_Request_Http
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
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _saveCache($data, $id, $tags=array(), $lifeTime=false)
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
    protected function _cleanCache($tags=array())
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
            $class = get_class($this);
            $this->_moduleName = substr($class, 0, strpos($class, '_Helper'));
        }
        return $this->_moduleName;
    }

    /**
     * Check whether or not the module output is enabled in Configuration
     *
     * @param string $moduleName Full module name
     * @return boolean
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!$this->isModuleEnabled($moduleName)) {
            return false;
        }

        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName)) {
            return false;
        }
        return true;
    }

    /**
     * Check is module exists and enabled in global config.
     *
     * @param string $moduleName the full module name, example Mage_Core
     * @return boolean
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }

    /**
     * Translate
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * @deprecated after 1.4.0.0-rc1
     * @see self::escapeHtml()
     */
    public function htmlEscape($data, $allowedTags = null)
    {
        return $this->escapeHtml($data, $allowedTags);
    }

    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     * @return  mixed
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = $this->escapeHtml($item);
            }
        } else {
            // process single item
            if (strlen($data)) {
                if (is_array($allowedTags) and !empty($allowedTags)) {
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
            $html
        );
        $html =  strip_tags($html);
        return htmlspecialchars_decode($html);
    }

    /**
     * Wrapper for standart strip_tags() function with extra functionality for html entities
     *
     * @param string $data
     * @param string $allowableTags
     * @param bool $escape
     * @return string
     */
    public function stripTags($data, $allowableTags = null, $escape = false)
    {
        $result = strip_tags($data, $allowableTags);
        return $escape ? $this->escapeHtml($result, $allowableTags) : $result;
    }

    /**
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
        return htmlspecialchars($data);
    }

    /**
     * Escape quotes in java script
     *
     * @param moxed $data
     * @param string $quote
     * @return mixed
     */
    public function jsQuoteEscape($data, $quote='\'')
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = str_replace($quote, '\\'.$quote, $item);
            }
            return $result;
        }
        return str_replace($quote, '\\'.$quote, $data);
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
    protected function _getUrl($route, $params = array())
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
     *  base64_dencode() for URLs dencoding
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
        $quote = array ('\'', '"');
        $replace = array('%27', '%22');
        $url = str_replace($quote, $replace, $url);
        return $url;
    }

    /**
     *   Translate array
     *
     *  @param    array $arr
     *  @return   array
     */
    public function translateArray($arr = array())
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
}
