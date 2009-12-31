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
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wysiwyg Images model
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cms_Model_Wysiwyg_Images_Storage extends Varien_Object
{
    const DIRECTORY_NAME_REGEXP = '/^[a-z0-9\-\_]+$/si';

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     * @return Varien_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setDirsFilter(self::DIRECTORY_NAME_REGEXP)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        return $collection;
    }

    /**
     * Return files
     *
     * @param string $path Parent directory path
     * @param string $type Type of storage, e.g. image, media etc.
     * @return Varien_Data_Collection_Filesystem
     */
    public function getFilesCollection($path, $type = null)
    {
        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', Varien_Data_Collection::SORT_ORDER_ASC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed). ')$/i');
        }

        return $collection;
    }

    /**
     * Storage collection
     *
     * @param string $path Path to the directory
     * @return Varien_Data_Collection_Filesystem
     */
    public function getCollection($path = null)
    {
        $collection = Mage::getModel('cms/wysiwyg_images_storage_collection');
        if ($path !== null) {
            $collection->addTargetDir($path);
        }
        return $collection;
    }

    /**
     * Create new directory in storage
     *
     * @param string $name New directory name
     * @param string $path Parent directory path
     * @throws Mage_Core_Exception
     * @return array New directory info
     */
    public function createDirectory($name, $path)
    {
        if (!preg_match(self::DIRECTORY_NAME_REGEXP, $name)) {
            Mage::throwException(Mage::helper('cms')->__('Invalid folder name. Please, use alphanumeric characters'));
        }
        if (!is_dir($path) || !is_writable($path)) {
            $path = Mage::helper('cms/wysiwyg_images')->getStorageRoot();
        }

        $newPath = $path . DS . $name;

        if (file_exists($newPath)) {
            Mage::throwException(Mage::helper('cms')->__('Such directory already exists. Try another folder name'));
        }

        $io = new Varien_Io_File();
        if ($io->mkdir($newPath)) {
            $result = array(
                'name'  => $name,
                'path'  => $newPath,
                'id'    => Mage::helper('cms/wysiwyg_images')->convertPathToId($newPath)
            );
            return $result;
        }
        Mage::throwException(Mage::helper('cms')->__('Cannot create new directory'));
    }

    /**
     * Recursively delete directory from storage
     *
     * @param string $path Target dir
     * @return void
     */
    public function deleteDirectory($path)
    {
        $io = new Varien_Io_File();
        if (!$io->rmdir($path, true)) {
            Mage::throwException(Mage::helper('cms')->__('Cannot delete directory %s', $path));
        }
    }

    /**
     * Upload and resize new file
     *
     * @param string $targetPath Target directory
     * @param string $type Type of storage, e.g. image, media etc.
     * @throws Mage_Core_Exception
     * @return array File info Array
     */
    public function uploadFile($targetPath, $type = null)
    {
        $uploader = new Varien_File_Uploader('image');
        if ($allowed = $this->getAllowedExtensions($type)) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException( Mage::helper('cms')->__('Cannot upload file') );
        }

        // create thumbnail
        $thumbsPath = $targetPath . DS . '.thumbs';
        $io = new Varien_Io_File();
        if ($io->isWriteable($thumbsPath)) {
            $io->mkdir($thumbsPath);
        }
        $image = Varien_Image_Adapter::factory('GD2');
        $image->open($targetPath . DS . $uploader->getUploadedFileName());
        $width = $this->getConfigData('browser_resize_width');
        $height = $this->getConfigData('browser_resize_height');
        $image->resize($width, $height);
        $image->save($thumbsPath . DS . $uploader->getUploadedFileName());

        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->getSession()->getSessionId(),
            'lifetime' => $this->getSession()->getCookieLifetime(),
            'path'     => $this->getSession()->getCookiePath(),
            'domain'   => $this->getSession()->getCookieDomain()
        );

        return $result;
    }

    /**
     * Storage session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Wysiwyg Config reader
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            $value = Mage::getStoreConfig('cms/wysiwyg/'.$key);
            if (is_null($value) || false===$value) {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->_getData($key);
    }

    /**
     * Prepare allowed_extensions config settings
     *
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array Array of allowed file extensions
     */
    public function getAllowedExtensions($type = null)
    {
        $configKey = is_null($type) ? 'browser_allowed_extensions' : 'browser_'.$type.'_allowed_extensions';
        if (preg_match_all('/[a-z0-9]+/si', strtolower($this->getConfigData($configKey)), $matches)) {
            return $matches[0];
        }
        return array();
    }

}
