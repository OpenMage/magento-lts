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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TheFind feed import model
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Import extends Mage_Core_Model_Abstract
{
    const SEPARATOR = "\t";
    const LINE_END  = "\r\n";
    const ENCLOSURE = '"';
    const COLLECTION_PAGE_SIZE = 5000;

    const XML_PATH_SETTINGS_FTP_SERVER           = 'feed/settings/ftp_server';
    const XML_PATH_SETTINGS_FTP_USER             = 'feed/settings/ftp_user';
    const XML_PATH_SETTINGS_FTP_PASSWORD         = 'feed/settings/ftp_password';
    const XML_PATH_SETTINGS_FTP_PATH             = 'feed/settings/ftp_path';
    const XML_PATH_SETTINGS_FINDFEED_FILENAME    = 'feed/settings/findfeed_filename';

    const XML_NODE_FIND_FEED_ATTRIBUTES = 'find_feed_attributes';

    /**
     * Cron action
     */
    public function dispatch()
    {
        $this->processImport();
    }

    /**
     * TheFind feed process import
     */
    public function processImport()
    {
        $file = $this->_createFile();
        if ($file) {
            $this->_deleteFtpFiles();
            $this->_sendFile($file);
            if (!$this->_deleteFile($file)) {
                Mage::throwException(Mage::helper('find_feed')->__("FTP: Can't delete files"));
            }
        }
    }

    /**
     * Create temp csv file and write export
     *
     * @return mixed
     */
    protected function _createFile()
    {
        $dir      = $this->_getTmpDir();
        $fileName = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FINDFEED_FILENAME);
        if (!$dir || !$fileName) {
            return false;
        }

        if (!($attributes = $this->_getImportAttributes()) || count($attributes) <= 0) {
            return false;
        }

        $headers = array_keys($attributes);

        $file = new Varien_Io_File;
        $file->checkAndCreateFolder($dir);
        $file->cd($dir);
        $file->streamOpen($fileName, 'w+');
        $file->streamLock();
        $file->streamWriteCsv($headers, self::SEPARATOR, self::ENCLOSURE);

        $productCollectionPrototype = Mage::getResourceModel('catalog/product_collection');
        $productCollectionPrototype->setPageSize(self::COLLECTION_PAGE_SIZE);
        $pageNumbers = $productCollectionPrototype->getLastPageNumber();
        unset($productCollectionPrototype);

        for ($i = 1; $i <= $pageNumbers; $i++) {
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $productCollection->addAttributeToSelect($attributes);
            $productCollection->addAttributeToFilter('is_imported', 1);
            $productCollection->setPageSize(self::COLLECTION_PAGE_SIZE);
            $productCollection->setCurPage($i)->load();
            foreach ($productCollection as $product) {
                $attributesRow = array();
                foreach ($attributes as $key => $value) {
                    $attributesRow[$key] = $product->getData($value);
                }
                $file->streamWriteCsv($attributesRow, self::SEPARATOR, self::ENCLOSURE);
            }
            unset($productCollection);
        }

        $file->streamUnlock();
        $file->streamClose();

        if ($file->fileExists($fileName)) {
            return $fileName;
        }
        return false;
    }

    /**
     * List import codes (attribute map) model
     *
     * @return mixed
     */
    protected function _getImportAttributes()
    {
        $attributes = Mage::getResourceModel('find_feed/codes_collection')
          ->getImportAttributes();

        if (!Mage::helper('find_feed')->checkRequired($attributes)) {
            return false;
        }
        return $attributes;
    }

    /**
     * Send file to remote ftp server
     *
     * @param string $fileName
     */
    protected function _sendFile($fileName)
    {
        $dir         = $this->_getTmpDir();
        $ftpServer   = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_SERVER);
        $ftpUserName = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_USER);
        $ftpPass     = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_PASSWORD);
        $ftpPath     = trim(Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_PATH), '/');
        if ($ftpPath) {
            $ftpPath = $ftpPath.'/';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'ftp://'.$ftpUserName.':'.$ftpPass.'@'.$ftpServer.'/'.$ftpPath.$fileName);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, fopen($dir.$fileName, 'r'));
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($dir.$fileName));
        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Delete all files in current feed ftp directory
     *
     * @return bool
     */
    protected function _deleteFtpFiles()
    {
        if (is_callable('ftp_connect')) {
            $ftpServer   = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_SERVER);
            $ftpUserName = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_USER);
            $ftpPass     = Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_PASSWORD);
            $ftpPath     = trim(Mage::getStoreConfig(self::XML_PATH_SETTINGS_FTP_PATH), '/');
            if ($ftpPath) {
                $ftpPath = $ftpPath.'/';
            }

            try {
                $connId = ftp_connect($ftpServer);

                $loginResult = ftp_login($connId, $ftpUserName, $ftpPass);
                if (!$loginResult) {
                    return false;
                }
                ftp_pasv($connId, true);

                $ftpDir = $ftpPath?$ftpPath:'.';
                $nlist = ftp_nlist($connId, $ftpDir);
                if ($nlist === false) {
                    return false;
                }
                foreach ($nlist as $file) {
                    if (!preg_match('/\.[xX][mM][lL]$/', $file)) {
                        ftp_delete($connId, $file);
                    }
                }

                ftp_close($connId);
            } catch (Exception $e) {
                Mage::log($e->getMessage());
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Current tmp directory
     *
     * @return string
     */
    protected function _getTmpDir()
    {
        return Mage::getBaseDir('var') . DS . 'export' . DS . 'find_feed' . DS;
    }

    /**
     * Delete tmp file
     *
     * @param string $fileName
     * @return true
     */
    protected function _deleteFile($fileName)
    {
        $dir  = $this->_getTmpDir();
        $file = new Varien_Io_File;
        if ($file->fileExists($dir . $fileName, true)) {
            $file->cd($dir);
            $file->rm($fileName);
        }
        return true;
    }
}
