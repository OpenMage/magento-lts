<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Wysiwyg Images Helper
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Helper_Wysiwyg_Images extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Cms';

    /**
     * Current directory path
     * @var string|false
     */
    protected $_currentPath;

    /**
     * Current directory URL
     * @var string
     */
    protected $_currentUrl;

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId = null;

    /**
     * Image Storage root directory
     * @var string|false
     */
    protected $_storageRoot;

    /**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     * Images Storage root directory
     *
     * @return string
     */
    public function getStorageRoot()
    {
        if (!$this->_storageRoot) {
            $path = Mage::getConfig()->getOptions()->getMediaDir()
                . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY;
            $this->_storageRoot = realpath($path);
            if (!$this->_storageRoot) {
                $this->_storageRoot = $path;
            }
            $this->_storageRoot .= DS;
        }
        return $this->_storageRoot;
    }

    /**
     * Images Storage base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return Mage::getBaseUrl('media');
    }

    /**
     * Ext Tree node key name
     *
     * @return string
     */
    public function getTreeNodeName()
    {
        return 'node';
    }

    /**
     * Encode path to HTML element id
     *
     * @param string $path Path to file/directory
     * @return string
     */
    public function convertPathToId($path)
    {
        $storageRoot = realpath($this->getStorageRoot());
        $path = str_replace($storageRoot, '', $path);
        return $this->idEncode($path);
    }

    /**
     * Decode HTML element id
     *
     * @param string $id
     * @return string
     */
    public function convertIdToPath($id)
    {
        $path = $this->idDecode($id);
        $storageRoot = realpath($this->getStorageRoot());
        if (!strstr($path, (string) $storageRoot)) {
            $path = $storageRoot . DS . $path;
        }
        return $path;
    }

    /**
     * File system path correction
     *
     * @param string $path Original path
     * @param bool $trim Trim slashes or not
     * @return string
     */
    public function correctPath($path, $trim = true)
    {
        $path = strtr($path, "\\\/", DS . DS);
        if ($trim) {
            $path = trim($path, DS);
        }
        return $path;
    }

    /**
     * Return file system path as Url string
     *
     * @param string $path
     * @return string
     */
    public function convertPathToUrl($path)
    {
        return str_replace(DS, '/', $path);
    }

    /**
     * Check whether using static URLs is allowed
     *
     * @return bool
     */
    public function isUsingStaticUrlsAllowed()
    {
        $checkResult = new stdClass();
        $checkResult->isAllowed = false;
        Mage::dispatchEvent('cms_wysiwyg_images_static_urls_allowed', [
            'result'   => $checkResult,
            'store_id' => $this->_storeId,
        ]);
        return $checkResult->isAllowed;
    }

    /**
     * Prepare Image insertion declaration for Wysiwyg or textarea(as_is mode)
     *
     * @param string $filename Filename transferred via Ajax
     * @param bool $renderAsTag Leave image HTML as is or transform it to controller directive
     * @return string
     */
    public function getImageHtmlDeclaration($filename, $renderAsTag = false)
    {
        $fileurl = $this->getCurrentUrl() . $filename;
        $mediaPath = str_replace(Mage::getBaseUrl('media'), '', $fileurl);
        $directive = sprintf('{{media url="%s"}}', $mediaPath);
        if ($renderAsTag) {
            $html = sprintf('<img src="%s" alt="" />', $this->isUsingStaticUrlsAllowed() ? $fileurl : $directive);
        } elseif ($this->isUsingStaticUrlsAllowed()) {
            $html = $fileurl;
            // $mediaPath;
        } else {
            $directive = Mage::helper('core')->urlEncode($directive);
            $html = Mage::helper('adminhtml')->getUrl('*/cms_wysiwyg/directive', ['___directive' => $directive]);
        }
        return $html;
    }

    /**
     * Return path of the current selected directory or root directory for startup
     * Try to create target directory if it doesn't exist
     *
     * @throws Mage_Core_Exception
     * @return string|false
     */
    public function getCurrentPath()
    {
        if (!$this->_currentPath) {
            $currentPath = $this->getStorageRoot();
            $node = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($node) {
                $path = realpath($this->convertIdToPath($node));
                if ($path && is_dir($path) && stripos($path, $currentPath) !== false) {
                    $currentPath = $path;
                }
            }
            $io = new Varien_Io_File();
            if (!$io->isWriteable($currentPath) && !$io->mkdir($currentPath)) {
                $message = Mage::helper('cms')->__(
                    'The directory %s is not writable by server.',
                    $io->getFilteredPath($currentPath),
                );
                Mage::throwException($message);
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    /**
     * Return URL based on current selected directory or root directory for startup
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        if (!$this->_currentUrl) {
            $mediaPath = realpath(Mage::getConfig()->getOptions()->getMediaDir());
            $path = str_replace($mediaPath, '', $this->getCurrentPath());
            $path = trim($path, DS);
            $this->_currentUrl = Mage::app()->getStore($this->_storeId)->getBaseUrl('media') .
                                 $this->convertPathToUrl($path) . '/';
        }
        return $this->_currentUrl;
    }

    /**
     * Storage model singleton
     *
     * @return Mage_Cms_Model_Wysiwyg_Images_Storage
     */
    public function getStorage()
    {
        return Mage::getSingleton('cms/wysiwyg_images_storage');
    }

    /**
     * Encode string to valid HTML id element, based on base64 encoding
     *
     * @param string $string
     * @return string
     */
    public function idEncode($string)
    {
        return strtr(base64_encode($string), '+/=', ':_-');
    }

    /**
     * Revert operation to idEncode
     *
     * @param string $string
     * @return string|false
     */
    public function idDecode($string)
    {
        $string = strtr($string, ':_-', '+/=');
        return base64_decode($string);
    }

    /**
     * Reduce filename by replacing some characters with dots
     *
     * @param string $filename
     * @param int $maxLength Maximum filename
     * @return string Truncated filename
     */
    public function getShortFilename($filename, $maxLength = 20)
    {
        if (strlen($filename) <= $maxLength) {
            return $filename;
        }
        return substr($filename, 0, $maxLength) . '...';
    }
}
