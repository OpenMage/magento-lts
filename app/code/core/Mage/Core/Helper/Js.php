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
 * @copyright  Copyright (c) 2015-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * JavaScript helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Js extends Mage_Core_Helper_Abstract
{
    /**
     * Key for cache
     */
    public const JAVASCRIPT_TRANSLATE_CONFIG_KEY = 'javascript_translate_config';

    /**
     * Translate file name
     */
    public const JAVASCRIPT_TRANSLATE_CONFIG_FILENAME = 'jstranslator.xml';

    protected $_moduleName = 'Mage_Core';

    /**
     * Array of sentences of JS translations
     *
     * @var array
     */
    protected $_translateData = null;

    /**
     * Translate config
     *
     * @var Varien_Simplexml_Config|null
     */
    protected $_config = null;

    /**
     * Retrieve JSON of JS sentences translation
     *
     * @return string
     */
    public function getTranslateJson()
    {
        return Mage::helper('core')->jsonEncode($this->_getTranslateData());
    }

    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = 'var Translator = new Translate(' . $this->getTranslateJson() . ');';
        return $this->getScript($script);
    }

    /**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  string script
     */
    public function getScript($script)
    {
        return '<script type="text/javascript">//<![CDATA[
        ' . $script . '
        //]]></script>';
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script type="text/javascript" src="' . $this->getJsUrl($file) . '"></script>' . "\n";
    }

    /**
     * Retrieve
     *
     * @param   string $file
     * @return  string
     */
    public function includeSkinScript($file)
    {
        return '<script type="text/javascript" src="' . $this->getJsSkinUrl($file) . '"></script>';
    }

    /**
     * Retrieve JS file url
     *
     * @param   string $file
     * @return  string
     */
    public function getJsUrl($file)
    {
        return Mage::getBaseUrl('js') . $file;
    }

    /**
     * Retrieve skin JS file url
     *
     * @param   string $file
     * @return  string
     */
    public function getJsSkinUrl($file)
    {
        return Mage::getDesign()->getSkinUrl($file, []);
    }

    /**
     * Retrieve JS translation array
     *
     * @return array
     */
    protected function _getTranslateData()
    {
        if ($this->_translateData === null) {
            $this->_translateData = [];
            $messages = $this->_getXmlConfig()->getXpath('*/message');
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    $messageText = (string)$message;
                    $module = $message->getParent()->getAttribute('module');
                    $this->_translateData[$messageText] = Mage::helper(empty($module) ? 'core' : $module)->__($messageText);
                }
            }

            foreach ($this->_translateData as $key => $value) {
                if ($key == $value) {
                    unset($this->_translateData[$key]);
                }
            }
        }
        return $this->_translateData;
    }

    /**
     * Load config from files and try to cache it
     *
     * @return Varien_Simplexml_Config
     */
    protected function _getXmlConfig()
    {
        if (is_null($this->_config)) {
            $canUsaCache = Mage::app()->useCache('config');
            $cachedXml = Mage::app()->loadCache(self::JAVASCRIPT_TRANSLATE_CONFIG_KEY);
            if ($canUsaCache && $cachedXml) {
                $xmlConfig = new Varien_Simplexml_Config($cachedXml);
            } else {
                $xmlConfig = new Varien_Simplexml_Config();
                $xmlConfig->loadString('<?xml version="1.0"?><jstranslator></jstranslator>');
                Mage::getConfig()->loadModulesConfiguration(self::JAVASCRIPT_TRANSLATE_CONFIG_FILENAME, $xmlConfig);

                if ($canUsaCache) {
                    Mage::app()->saveCache(
                        $xmlConfig->getXmlString(),
                        self::JAVASCRIPT_TRANSLATE_CONFIG_KEY,
                        [Mage_Core_Model_Config::CACHE_TAG]
                    );
                }
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }

    /**
     * Helper for "onclick.deleteConfirm"
     *
     * @param string|null $message null for default message, do not use jsQuoteEscape() before
     * @uses Mage_Core_Helper_Abstract::jsQuoteEscape()
     */
    public function getDeleteConfirmJs(string $url, ?string $message = null): string
    {
        if (is_null($message)) {
            $message = Mage::helper('adminhtml')->__('Are you sure you want to do this?');
        }

        $message = Mage::helper('core')->jsQuoteEscape($message);
        return 'deleteConfirm(\'' . $message . '\', \'' . $url . '\')';
    }

    /**
     * Helper for "onclick.confirmSetLocation"
     *
     * @param string|null $message null for default message, do not use jsQuoteEscape() before
     * @uses Mage_Core_Helper_Abstract::jsQuoteEscape()
     */
    public function getConfirmSetLocationJs(string $url, ?string $message = null): string
    {
        if (is_null($message)) {
            $message = Mage::helper('adminhtml')->__('Are you sure you want to do this?');
        }

        $message = Mage::helper('core')->jsQuoteEscape($message);
        return "confirmSetLocation('{$message}', '{$url}')";
    }

    /**
     * Helper for "onclick.setLocation"
     */
    public function getSetLocationJs(string $url): string
    {
        return 'setLocation(\'' . $url . '\')';
    }

    /**
     * Helper for "onclick.saveAndContinueEdit"
     */
    public function getSaveAndContinueEditJs(string $url): string
    {
        return 'saveAndContinueEdit(\'' . $url . '\')';
    }
}
