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
 * @package     Mage_Backup
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backup file item model
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Backup extends Varien_Object
{
    /* internal constants */
    const COMPRESS_RATE     = 9;

    /**
     * Type of backup file
     *
     * @var string
     */
    private $_type  = 'db';

    /**
     * Gz file pointer
     *
     * @var resource
     */
    protected $_handler = null;

    /**
     * Load backup file info
     *
     * @param string fileName
     * @param string filePath
     * @return $this
     */
    public function load($fileName, $filePath)
    {
        $backupData = Mage::helper('backup')->extractDataFromFilename($fileName);

        $this->addData(array(
            'id'   => $filePath . DS . $fileName,
            'time' => (int)$backupData->getTime(),
            'path' => $filePath,
            'extension' => Mage::helper('backup')->getExtensionByType($backupData->getType()),
            'display_name' => Mage::helper('backup')->nameToDisplayName($backupData->getName()),
            'name' => $backupData->getName(),
            'date_object' => new Zend_Date((int)$backupData->getTime(), Mage::app()->getLocale()->getLocaleCode())
        ));

        $this->setType($backupData->getType());
        return $this;
    }

    /**
     * Checks backup file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->getPath() . DS . $this->getFileName());
    }

    /**
     * Return file name of backup file
     *
     * @return string
     */
    public function getFileName()
    {
        $filename = $this->getTime() . "_" . $this->getType();
        $backupName = $this->getName();

        if (!empty($backupName)) {
            $filename .= '_' . $backupName;
        }

        $filename .= '.' . Mage::helper('backup')->getExtensionByType($this->getType());

        return $filename;
    }

    /**
     * Sets type of file
     *
     * @param string $value
     * @return $this
     */
    public function setType($value='db')
    {
        $possibleTypes = Mage::helper('backup')->getBackupTypesList();
        if(!in_array($value, $possibleTypes)) {
            $value = Mage::helper('backup')->getDefaultBackupType();
        }

        $this->_type = $value;
        $this->setData('type', $this->_type);

        return $this;
    }

    /**
     * Returns type of backup file
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set the backup file content
     *
     * @param string $content
     * @return $this
     * @throws Mage_Backup_Exception
     */
    public function setFile(&$content)
    {
        if (!$this->hasData('time') || !$this->hasData('type') || !$this->hasData('path')) {
            Mage::throwException(Mage::helper('backup')->__('Wrong order of creation for new backup.'));
        }

        $ioProxy = new Varien_Io_File();
        $ioProxy->setAllowCreateFolders(true);
        $ioProxy->open(array('path'=>$this->getPath()));

        $compress = 0;
        if (extension_loaded("zlib")) {
            $compress = 1;
        }

        $rawContent = '';
        if ( $compress ) {
            $rawContent = gzcompress( $content, self::COMPRESS_RATE );
        } else {
            $rawContent = $content;
        }

        $fileHeaders = pack("ll", $compress, strlen($rawContent));
        $ioProxy->write($this->getFileName(), $fileHeaders . $rawContent);
        return $this;
    }

    /**
     * Return content of backup file
     *
     * @todo rewrite to Varien_IO, but there no possibility read part of files.
     * @return string
     * @throws Mage_Backup_Exception
     */
    public function &getFile()
    {

        if (!$this->exists()) {
            Mage::throwException(Mage::helper('backup')->__("Backup file does not exist."));
        }

        $fResource = @fopen($this->getPath() . DS . $this->getFileName(), "rb");
        if (!$fResource) {
            Mage::throwException(Mage::helper('backup')->__("Cannot read backup file."));
        }

        $content = '';
        $compressed = 0;

        $info = unpack("lcompress/llength", fread($fResource, 8));
        if ($info['compress']) { // If file compressed by zlib
            $compressed = 1;
        }

        if ($compressed && !extension_loaded("zlib")) {
            fclose($fResource);
            Mage::throwException(Mage::helper('backup')->__('The file was compressed with Zlib, but this extension is not installed on server.'));
        }

        if ($compressed) {
            $content = gzuncompress(fread($fResource, $info['length']));
        } else {
            $content = fread($fResource, $info['length']);
        }

        fclose($fResource);

        return $content;
    }

    /**
     * Delete backup file
     *
     * @throws Mage_Backup_Exception
     * @return $this
     */
    public function deleteFile()
    {
        if (!$this->exists()) {
            Mage::throwException(Mage::helper('backup')->__("Backup file does not exist."));
        }

        $ioProxy = new Varien_Io_File();
        $ioProxy->open(array('path'=>$this->getPath()));
        $ioProxy->rm($this->getFileName());
        return $this;
    }

    /**
     * Open backup file (write or read mode)
     *
     * @param bool $write
     * @return $this
     */
    public function open($write = false)
    {
        if (is_null($this->getPath())) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('Backup file path was not specified.'));
        }

        $ioAdapter = new Varien_Io_File();
        try {
            $path = $ioAdapter->getCleanPath($this->getPath());
            $ioAdapter->checkAndCreateFolder($path);
            $filePath = $path . DS . $this->getFileName();
        }
        catch (Exception $e) {
            Mage::exception('Mage_Backup', $e->getMessage());
        }

        if ($write && $ioAdapter->fileExists($filePath)) {
            $ioAdapter->rm($filePath);
        }
        if (!$write && !$ioAdapter->fileExists($filePath)) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('Backup file "%s" does not exist.', $this->getFileName()));
        }

        $mode = $write ? 'wb' . self::COMPRESS_RATE : 'rb';

        $this->_handler = @gzopen($filePath, $mode);

        if (!$this->_handler) {
            throw new Mage_Backup_Exception_NotEnoughPermissions(
                Mage::helper('backup')->__('Backup file "%s" cannot be read from or written to.', $this->getFileName())
            );
        }

        return $this;
    }

    /**
     * Read backup uncomressed data
     *
     * @param int $length
     * @return string
     */
    public function read($length)
    {
        if (is_null($this->_handler)) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('Backup file handler was unspecified.'));
        }

        return gzread($this->_handler, $length);
    }

    public function eof()
    {
        if (is_null($this->_handler)) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('Backup file handler was unspecified.'));
        }

        return gzeof($this->_handler);
    }

    /**
     * Write to backup file
     *
     * @param string $string
     * @return $this
     */
    public function write($string)
    {
        if (is_null($this->_handler)) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('Backup file handler was unspecified.'));
        }

        try {
            gzwrite($this->_handler, $string);
        }
        catch (Exception $e) {
            Mage::exception('Mage_Backup', Mage::helper('backup')->__('An error occurred while writing to the backup file "%s".', $this->getFileName()));
        }

        return $this;
    }

    /**
     * Close open backup file
     *
     * @return $this
     */
    public function close()
    {
        @gzclose($this->_handler);
        $this->_handler = null;

        return $this;
    }

    /**
     * Print output
     *
     */
    public function output()
    {
        if (!$this->exists()) {
            return ;
        }

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array('path' => $this->getPath()));

        $ioAdapter->streamOpen($this->getFileName(), 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            echo $buffer;
        }
        $ioAdapter->streamClose();
    }

    public function getSize()
    {
        if (!is_null($this->getData('size'))) {
            return $this->getData('size');
        }

        if ($this->exists()) {
            $this->setData('size', filesize($this->getPath() . DS . $this->getFileName()));
            return $this->getData('size');
        }

        return 0;
    }

    /**
     * Validate user password
     *
     * @param string $password
     * @return bool
     */
    public function validateUserPassword($password)
    {
        $userPasswordHash = Mage::getModel('admin/session')->getUser()->getPassword();
        return Mage::helper('core')->validateHash($password, $userPasswordHash);
    }

    /**
     * Load backup by it's type and creation timestamp
     *
     * @param int $timestamp
     * @param string $type
     * @return $this
     */
    public function loadByTimeAndType($timestamp, $type)
    {
        $backupsCollection = Mage::getSingleton('backup/fs_collection');
        $backupId = $timestamp . '_' . $type;

        foreach ($backupsCollection as $backup) {
            if ($backup->getId() == $backupId) {
                $this->setType($backup->getType())
                    ->setTime($backup->getTime())
                    ->setName($backup->getName())
                    ->setPath($backup->getPath());
                break;
            }
        }

        return $this;
    }
}
