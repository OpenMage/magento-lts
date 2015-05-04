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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect offline catalog helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_OfflineCatalog extends Mage_Core_Helper_Abstract
{
    /**
     * Result folder
     */
    const RESULT_FOLDER = 'offline_catalog';

    /**
     * Map file
     */
    const MAP_FILE = '_storage.dict';

    /**
     * Export url
     */
    const EXPORT_URL = 'xmlconnect/offlineCatalog/index/key/';

    /**
     * Varien file IO
     *
     * @var null|Varien_Io_File
     */
    protected $_fileIo;

    /**
     * Xml object
     *
     * @var null|Mage_XmlConnect_Model_Simplexml_Element
     */
    protected $_xmlObject;

    /**
     * Layout model
     *
     * @var null|Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Result dir
     *
     * @var null|string
     */
    protected $_resultDir;

    /**
     * Application model
     *
     * @var null|Mage_XmlConnect_Model_Application
     */
    protected $_appModel;

    /**
     * Prepare result directory
     *
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    public function prepareResultDirectory()
    {
        $fileModel = $this->_getFileIo();
        $resultDir = $this->_getResultDir();
        $fileModel->rmdirRecursive($resultDir);
        $fileModel->mkdir($resultDir);
        return $this;
    }

    /**
     * Get file IO
     *
     * @return Varien_Io_File
     */
    protected function _getFileIo()
    {
        if ($this->_fileIo === null) {
            $this->_fileIo = new Varien_Io_File();
        }
        return $this->_fileIo;
    }

    /**
     * Get handles
     *
     * @return array
     */
    protected function _getHandles()
    {
        return array('xmlconnect_configuration_index', 'xmlconnect_catalog_categorydetails',
            'xmlconnect_catalog_productview', 'xmlconnect_homebanners_index', 'xmlconnect_index_index',
            'xmlconnect_catalog_productgallery', 'xmlconnect_catalog_productreviews'
        );
    }

    /**
     * Add offline catalog data
     *
     * @param string $urlPath
     * @param string $output
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    public function addOfflineCatalogData($urlPath, $output)
    {
        $xmlObj = $this->_getXmlObject();
        $fileName = uniqid() . '-' . sha1($output) . '.cache';

        $this->saveCacheFile($fileName, $output);
        $xmlObj->addCustomChild('key', $urlPath);
        $xmlObj->addCustomChild('string', $fileName);
        return $this;
    }

    /**
     * Save cache file
     *
     * @param string $fileName
     * @param string $output
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    public function saveCacheFile($fileName, $output)
    {
        $filePath = $this->_getResultDir() . DS . $fileName;
        $this->_getFileIo()->open(array('path' => $this->_getResultDir()));
        $this->_getFileIo()->write($filePath, $output);
        return $this;
    }

    /**
     * Get result directory path
     *
     * @return string
     */
    protected function _getResultDir()
    {
        if ($this->_resultDir === null) {
            $this->_resultDir = Mage::getBaseDir('var') . DS . self::RESULT_FOLDER;
        }
        return $this->_resultDir;
    }

    /**
     * Get xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _getXmlObject()
    {
        if ($this->_xmlObject === null) {
            $this->_xmlObject = Mage::getModel('xmlconnect/simplexml_element', '<dict></dict>');
        }
        return $this->_xmlObject;
    }

    /**
     * Get block
     *
     * @param string $blockName
     * @return Mage_Core_Block_Abstract
     */
    public function getBlock($blockName)
    {
        /** @var $layout Mage_Core_Model_Layouts */
        $this->_prepareLayout();
        $this->_layout->getUpdate()->load($this->_getHandles());
        $this->_layout->generateXml()->generateBlocks();
        $block = $this->_layout->getBlock($blockName);
        $this->unsetLayout();
        return $block;
    }

    /**
     * Prepare layout
     *
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    protected function _prepareLayout()
    {
        $this->_layout = Mage::getModel('core/layout');
        return $this;
    }

    /**
     * Unset layout
     *
     * Only for PHP 5 >= 5.3.0
     *
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    protected function unsetLayout()
    {
        /** @TODO Add support for previous PHP version */
        unset($this->_layout);
        gc_collect_cycles();
        return $this;
    }

    /**
     * Get current device model
     *
     * @return Mage_XmlConnect_Model_Application|null
     */
    public function getCurrentDeviceModel()
    {
        return $this->_appModel;
    }

    /**
     * Set current device model
     *
     * @param string $key
     * @return bool
     */
    public function setCurrentDeviceModel($key)
    {
        $appCode = $this->base64UrlDecode($key);
        $appModel = Mage::getModel('xmlConnect/application')->loadByCode($appCode);
        if (!$appModel->getId()) {
            return false;
        }
        Mage::register('current_app', $appModel);
        $this->_appModel = $appModel;
        return true;
    }

    /**
     * Base64 url encode
     *
     * @param string $data
     * @return string
     */
    public function base64UrlEncode($data)
    {
        /** @var $decryptHelper Mage_Core_Helper_Data */
        $decryptHelper = Mage::helper('core/data');
        return strtr($decryptHelper->encrypt($data), '+/=', '-_,');
    }

    /**
     * Base64 url dencode
     *
     * @param string $data
     * @return string
     */
    public function base64UrlDecode($data)
    {
        /** @var $decryptHelper Mage_Core_Helper_Data */
        $decryptHelper = Mage::helper('core/data');
        return $decryptHelper->decrypt(strtr($data, '-_,', '+/='));
    }

    /**
     * Render xml object
     *
     * @return Mage_XmlConnect_Helper_OfflineCatalog
     */
    public function renderXmlObject()
    {
        /** @var $result Mage_XmlConnect_Model_Simplexml_Element */
        $result = Mage::getModel('xmlconnect/simplexml_element', '<plist  version="1.0"></plist>');
        $result->appendChild($this->_getXmlObject())->asNiceXml($this->_getResultDir() . DS . self::MAP_FILE);
        return $this;
    }

    /**
     * Get export url
     *
     * @return string
     */
    public function getExportUrl()
    {
        $app = Mage::helper('xmlconnect')->getApplication();
        $secretKey = array('key' => $this->base64UrlEncode($app->getCode()));
        $storeUrl = Mage::getModel('core/store')->load($app->getStoreId())->getUrl(self::EXPORT_URL, $secretKey);
        return $storeUrl;
    }
}
