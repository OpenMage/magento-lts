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
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    const THUMBS_DIRECTORY_NAME = '.thumbs';
    const THUMB_PLACEHOLDER_PATH_SUFFIX = 'images/placeholder/thumbnail.jpg';

    /**
     * Config object
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_config;

    /**
     * Config object as array
     *
     * @var array
     */
    protected $_configAsArray;

    /**
     * Return one-level child directories for specified path
     *
     * @param string $path Parent directory path
     * @return Varien_Data_Collection_Filesystem
     */
    public function getDirsCollection($path)
    {
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $subDirectories = Mage::getModel('core/file_storage_directory_database')->getSubdirectories($path);
            foreach ($subDirectories as $directory) {
                $fullPath = rtrim($path, DS) . DS . $directory['name'];
                  if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0777, true);
                }
            }
        }

        $conditions = array('reg_exp' => array(), 'plain' => array());

        foreach ($this->getConfig()->dirs->exclude->children() as $dir) {
            $conditions[$dir->getAttribute('regexp') ? 'reg_exp' : 'plain'][(string) $dir] = true;
        }
        // "include" section takes precedence and can revoke directory exclusion
        foreach ($this->getConfig()->dirs->include->children() as $dir) {
            unset($conditions['regexp'][(string) $dir], $conditions['plain'][(string) $dir]);
        }

        $regExp = $conditions['reg_exp'] ? ('~' . implode('|', array_keys($conditions['reg_exp'])) . '~i') : null;
        $collection = $this->getCollection($path)
            ->setCollectDirs(true)
            ->setCollectFiles(false)
            ->setCollectRecursively(false);
        $storageRootLength = strlen($this->getHelper()->getStorageRoot());

        foreach ($collection as $key => $value) {
            $rootChildParts = explode(DIRECTORY_SEPARATOR, substr($value->getFilename(), $storageRootLength));

            if (array_key_exists(end($rootChildParts), $conditions['plain'])
                || ($regExp && preg_match($regExp, $value->getFilename()))) {
                $collection->removeItemByKey($key);
            }
        }

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
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $files = Mage::getModel('core/file_storage_database')->getDirectoryFiles($path);

            $fileStorageModel = Mage::getModel('core/file_storage_file');
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }

        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', Varien_Data_Collection::SORT_ORDER_ASC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed). ')$/i');
        }

        $helper = $this->getHelper();

        // prepare items
        foreach ($collection as $item) {
            $item->setId($helper->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($helper->getShortFilename($item->getBasename()));
            $item->setUrl($helper->getCurrentUrl() . $item->getBasename());

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl($item->getFilename(), true);
                // generate thumbnail "on the fly" if it does not exists
                if(! $thumbUrl) {
                    $thumbUrl = Mage::getSingleton('adminhtml/url')->getUrl('*/*/thumbnail', array('file' => $item->getId()));
                }

                $size = @getimagesize($item->getFilename());

                if (is_array($size)) {
                    $item->setWidth($size[0]);
                    $item->setHeight($size[1]);
                }
            } else {
                $thumbUrl = Mage::getDesign()->getSkinBaseUrl() . self::THUMB_PLACEHOLDER_PATH_SUFFIX;
            }

            $item->setThumbUrl($thumbUrl);
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
            Mage::throwException(Mage::helper('cms')->__('Invalid folder name. Please, use alphanumeric characters, underscores and dashes.'));
        }
        if (!is_dir($path) || !is_writable($path)) {
            $path = $this->getHelper()->getStorageRoot();
        }

        $newPath = $path . DS . $name;

        if (file_exists($newPath)) {
            Mage::throwException(Mage::helper('cms')->__('A directory with the same name already exists. Please try another folder name.'));
        }

        $io = new Varien_Io_File();
        if ($io->mkdir($newPath)) {
            if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
                $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($newPath);
                Mage::getModel('core/file_storage_directory_database')->createRecursive($relativePath);
            }

            $result = array(
                'name'          => $name,
                'short_name'    => $this->getHelper()->getShortFilename($name),
                'path'          => $newPath,
                'id'            => $this->getHelper()->convertPathToId($newPath)
            );
            return $result;
        }
        Mage::throwException(Mage::helper('cms')->__('Cannot create new directory.'));
    }

    /**
     * Recursively delete directory from storage
     *
     * @param string $path Target dir
     * @return void
     */
    public function deleteDirectory($path)
    {
        // prevent accidental root directory deleting
        $rootCmp = rtrim($this->getHelper()->getStorageRoot(), DS);
        $pathCmp = rtrim($path, DS);

        $io = new Varien_Io_File();

        if ($rootCmp == $pathCmp) {
            Mage::throwException(Mage::helper('cms')->__('Cannot delete root directory %s.',
                $io->getFilteredPath($path)));
        }

        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            Mage::getModel('core/file_storage_directory_database')->deleteDirectory($path);
        }
        if (!$io->rmdir($path, true)) {
            Mage::throwException(Mage::helper('cms')->__('Cannot delete directory %s.', $io->getFilteredPath($path)));
        }

        if (strpos($pathCmp, $rootCmp) === 0) {
            $io->rmdir($this->getThumbnailRoot() . DS . ltrim(substr($pathCmp, strlen($rootCmp)), '\\/'), true);
        }
    }

    /**
     * Delete file (and its thumbnail if exists) from storage
     *
     * @param string $target File path to be deleted
     * @return Mage_Cms_Model_Wysiwyg_Images_Storage
     */
    public function deleteFile($target)
    {
        $io = new Varien_Io_File();
        $io->rm($target);
        Mage::helper('core/file_storage_database')->deleteFile($target);

        $thumb = $this->getThumbnailPath($target, true);
        if ($thumb) {
            $io->rm($thumb);
            Mage::helper('core/file_storage_database')->deleteFile($thumb);
        }
        return $this;
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
        $uploader = new Mage_Core_Model_File_Uploader('image');
        if ($allowed = $this->getAllowedExtensions($type)) {
            $uploader->setAllowedExtensions($allowed);
        }
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->addValidateCallback(
            Mage_Core_Model_File_Validator_Image::NAME,
            Mage::getModel('core/file_validator_image'),
            'validate'
        );
        $result = $uploader->save($targetPath);

        if (!$result) {
            Mage::throwException( Mage::helper('cms')->__('Cannot upload file.') );
        }

        // create thumbnail
        $this->resizeFile($targetPath . DS . $uploader->getUploadedFileName(), true);

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
     * Thumbnail path getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailPath($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->getHelper()->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbPath = $this->getThumbnailRoot() . DS . substr($filePath, strlen($mediaRootDir));

            if (! $checkFile || is_readable($thumbPath)) {
                return $thumbPath;
            }
        }

        return false;
    }

    /**
     * Thumbnail URL getter
     *
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */
    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        $mediaRootDir = $this->getHelper()->getStorageRoot();

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbSuffix = self::THUMBS_DIRECTORY_NAME . DS . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY
                . DS . substr($filePath, strlen($mediaRootDir));

            if (! $checkFile || is_readable($mediaRootDir . $thumbSuffix)) {
                $randomIndex = '?rand=' . time();
                $thumbUrl = $this->getHelper()->getBaseUrl() . Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY
                    . DS . $thumbSuffix;
                return str_replace('\\', '/', $thumbUrl) . $randomIndex;
            }
        }

        return false;
    }

    /**
     * Create thumbnail for image and save it to thumbnails directory
     *
     * @param string $source Image path to be resized
     * @param bool $keepRation Keep aspect ratio or not
     * @return bool|string Resized filepath or false if errors were occurred
     */
    public function resizeFile($source, $keepRation = true)
    {
        if (!is_file($source) || !is_readable($source)) {
            return false;
        }

        $targetDir = $this->getThumbsPath($source);
        $io = new Varien_Io_File();
        if (!$io->isWriteable($targetDir)) {
            $io->mkdir($targetDir);
        }
        if (!$io->isWriteable($targetDir)) {
            return false;
        }
        $image = Varien_Image_Adapter::factory('GD2');
        $image->open($source);
        $width = $this->getConfigData('resize_width');
        $height = $this->getConfigData('resize_height');
        $image->keepAspectRatio($keepRation);
        $image->resize($width, $height);
        $dest = $targetDir . DS . pathinfo($source, PATHINFO_BASENAME);
        $image->save($dest);
        if (is_file($dest)) {
            return $dest;
        }
        return false;
    }

    /**
     * Resize images on the fly in controller action
     *
     * @param string File basename
     * @return bool|string Thumbnail path or false for errors
     */
    public function resizeOnTheFly($filename)
    {
        $path = $this->getSession()->getCurrentPath();
        if (!$path) {
            $path = $this->getHelper()->getCurrentPath();
        }
        return $this->resizeFile($path . DS . $filename);
    }

    /**
     * Return thumbnails directory path for file/current directory
     *
     * @param string $filePath Path to the file
     * @return string
     */
    public function getThumbsPath($filePath = false)
    {
        $mediaRootDir = Mage::getConfig()->getOptions()->getMediaDir();
        $thumbnailDir = $this->getThumbnailRoot();

        if ($filePath && strpos($filePath, $mediaRootDir) === 0) {
            $thumbnailDir .= DS . dirname(substr($filePath, strlen($mediaRootDir)));
        }

        return $thumbnailDir;
    }

    /**
     * Media Storage Helper getter
     * @return Mage_Cms_Helper_Wysiwyg_Images
     */
    public function getHelper()
    {
        return Mage::helper('cms/wysiwyg_images');
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
     * Config object getter
     *
     * @return Mage_Core_Model_Config_Element
     */
    public function getConfig()
    {
        if (! $this->_config) {
            $this->_config = Mage::getConfig()->getNode('cms/browser', 'adminhtml');
        }

        return $this->_config;
    }

    /**
     * Config object as array getter
     *
     * @return array
     */
    public function getConfigAsArray()
    {
        if (! $this->_configAsArray) {
            $this->_configAsArray = $this->getConfig()->asCanonicalArray();
        }

        return $this->_configAsArray;
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
        $configArray = $this->getConfigAsArray();
        $key = (string) $key;

        return array_key_exists($key, $configArray) ? $configArray[$key] : $default;
    }

    /**
     * Prepare allowed_extensions config settings
     *
     * @param string $type Type of storage, e.g. image, media etc.
     * @return array Array of allowed file extensions
     */
    public function getAllowedExtensions($type = null)
    {
        $extensions = $this->getConfigData('extensions');

        if (is_string($type) && array_key_exists("{$type}_allowed", $extensions)) {
            $allowed = $extensions["{$type}_allowed"];
        } else {
            $allowed = $extensions['allowed'];
        }

        return array_keys(array_filter($allowed));
    }

    /**
     * Thumbnail root directory getter
     *
     * @return string
     */
    public function getThumbnailRoot()
    {
        return $this->getHelper()->getStorageRoot() . self::THUMBS_DIRECTORY_NAME;
    }

    /**
     * Simple way to check whether file is image or not based on extension
     *
     * @param string $filename
     * @return bool
     */
    public function isImage($filename)
    {
        if (!$this->hasData('_image_extensions')) {
            $this->setData('_image_extensions', $this->getAllowedExtensions('image'));
        }
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $this->_getData('_image_extensions'));
    }
}
