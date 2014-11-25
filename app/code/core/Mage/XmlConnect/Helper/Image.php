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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect image helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * Xml connect glue
     *
     * @deprecated will delete in the next version
     */
    const XMLCONNECT_GLUE = Mage_XmlConnect_Model_ImageLimits::SCREEN_SIZE_UPDATE_TYPE_GLUE;

    /**
     * Image limits for content
     *
     * @deprecated will delete in the next version
     * @var array|null
     */
    protected $_content = null;

    /**
     * Image limits for interface
     *
     * @deprecated will delete in the next version
     * @var array|null
     */
    protected $_interface = null;

    /**
     * Array of interface image paths in xmlConfig
     *
     * @deprecated will delete in the next version
     * @var array
     */
    protected $_interfacePath = array();

    /**
     * Image limits array
     *
     * @deprecated will delete in the next version
     * @var array
     */
    protected $_imageLimits = array();

    /**
     * Images paths in the config
     *
     * @deprecated will delete in the next version
     * @var array|null
     */
    protected $_confPaths = null;

    /**
     * Process uploaded file
     * setup file names to the configuration
     *
     * @deprecated will delete in the next version
     * @param string $field
     * @return string
     */
    public function handleUpload($field)
    {
        $uploadedFilename = '';
        $uploadDir = $this->getOriginalSizeUploadDir();

        try {
            $this->_forcedConvertPng($field);

            /** @var $uploader Mage_Core_Model_File_Uploader */
            $uploader = Mage::getModel('core/file_uploader', $field);
            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->save($uploadDir);
            $uploadedFilename = $uploader->getUploadedFileName();
            $uploadedFilename = $this->_getResizedFilename($field, $uploadedFilename, true);
        } catch (Exception $e) {
            /**
             * Hard coded exception catch
             */
            if (!strlen($_FILES[$field]['tmp_name'])) {
                Mage::throwException(Mage::helper('xmlconnect')->__('File can\'t be uploaded.'));
            } elseif ($e->getMessage() == 'Disallowed file type.') {
                $filename = $_FILES[$field]['name'];
                Mage::throwException(
                    Mage::helper('xmlconnect')->__('Error while uploading file "%s". Disallowed file type. Only "jpg", "jpeg", "gif", "png" are allowed.', $filename)
                );
            } else {
                Mage::logException($e);
            }
        }
        return basename($uploadedFilename);
    }

    /**
     * Return current screen_size parameter
     *
     * @deprecated will delete in the next version
     * @return string
     */
    protected function _getScreenSize()
    {
        return $this->filterScreenSize(Mage::helper('xmlconnect')->getApplication()->getScreenSize());
    }

    /**
     * Return correct system filename for current screenSize
     *
     * @deprecated will delete in the next version
     * @throws Mage_Core_Exception
     * @param string $fieldPath
     * @param string $fileName
     * @param bool $default
     * @return string
     */
    protected function _getResizedFilename($fieldPath, $fileName, $default = false)
    {
        $fileName = basename($fileName);
        if ($default) {
            $dir = $this->getDefaultSizeUploadDir();
        } else {
            $dir = $this->getCustomSizeUploadDir($this->_getScreenSize());
        }
        $customSizeFileName =  $dir . DS . $fileName;
        $originalSizeFileName = $this->getOriginalSizeUploadDir(). DS . $fileName;

        /**
         * Compatibility with old versions of XmlConnect
         */
        if (!file_exists($originalSizeFileName)) {
            $oldFileName = $this->getOldUploadDir() . DS . $fileName;
            if (file_exists($oldFileName)) {
                if (!(copy($oldFileName, $originalSizeFileName) && (is_readable($customSizeFileName)
                    || chmod($customSizeFileName, 0644))
                )) {
                    Mage::throwException(
                        Mage::helper('xmlconnect')->__('Error while processing file "%s".', $fileName)
                    );
                }
            } else {
                Mage::throwException(Mage::helper('xmlconnect')->__('No such file "%s".', $fileName));
            }
        }

        $isCopied = copy($originalSizeFileName, $customSizeFileName);
        clearstatcache();
        if ($isCopied && (is_readable($customSizeFileName) || chmod($customSizeFileName, 0644))) {
            $this->_handleResize($fieldPath, $customSizeFileName);
        } else {
            $fileName = '';
            if (isset($_FILES[$fieldPath]) && is_array($_FILES[$fieldPath]) && isset($_FILES[$fieldPath]['name'])) {
                $fileName = $_FILES[$fieldPath]['name'];
            }
            Mage::throwException(Mage::helper('xmlconnect')->__('Error while uploading file "%s".', $fileName));
        }
        return $customSizeFileName;
    }

    /**
     * Resize uploaded file
     *
     * @deprecated will delete in the next version
     * @param string $fieldPath
     * @param string $file
     * @return null
     */
    protected function _handleResize($fieldPath, $file)
    {
        $nameParts = explode('/', $fieldPath);
        array_shift($nameParts);
        $conf = $this->getInterfaceImageLimits();
        while (count($nameParts)) {
            $next = array_shift($nameParts);
            if (isset($conf[$next])) {
                $conf = $conf[$next];
            } else {
                /**
                 * No config data - nothing to resize
                 */
                return;
            }
        }

        $image = new Varien_Image($file);
        $width = $image->getOriginalWidth();
        $height = $image->getOriginalHeight();

        if (isset($conf['widthMax']) && ($conf['widthMax'] < $width)) {
            $width = $conf['widthMax'];
        } elseif (isset($conf['width'])) {
            $width = $conf['width'];
        }

        if (isset($conf['heightMax']) && ($conf['heightMax'] < $height)) {
            $height = $conf['heightMax'];
        } elseif (isset($conf['height'])) {
            $height = $conf['height'];
        }

        if (($width != $image->getOriginalWidth()) || ($height != $image->getOriginalHeight())) {
            $image->keepTransparency(true);
            $image->keepFrame(true);
            $image->keepAspectRatio(true);
            $image->backgroundColor(array(255, 255, 255));
            $image->resize($width, $height);
            $image->save(null, basename($file));
        }
    }

    /**
     * Convert uploaded file to PNG
     *
     * @deprecated will delete in the next version
     * @param string $field
     */
    protected function _forcedConvertPng($field)
    {
        $file =& $_FILES[$field];

        $dotPosition = strrpos($file['name'], '.');
        if ($dotPosition !== false) {
            $file['name'] = substr($file['name'], 0 , $dotPosition);
        }
        $file['name'] .= '.png';

//      We can't use exif extension, because magento doesn't require it.
//      $fileType = exif_imagetype($file['tmp_name']);
        list($unnecessaryVar, $unnecessaryVar, $fileType) = getimagesize($file['tmp_name']);
        unset($unnecessaryVar);

        if ($fileType != IMAGETYPE_PNG) {
            switch ($fileType) {
                case IMAGETYPE_GIF:
                    $img = imagecreatefromgif($file['tmp_name']);
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    break;
                case IMAGETYPE_JPEG:
                    $img = imagecreatefromjpeg($file['tmp_name']);
                    break;
                case IMAGETYPE_WBMP:
                    $img = imagecreatefromwbmp($file['tmp_name']);
                    break;
                case IMAGETYPE_XBM:
                    $img = imagecreatefromxbm($file['tmp_name']);
                    break;
                default:
                    return;
            }
            imagepng($img, $file['tmp_name']);
            imagedestroy($img);
        }
    }

    /**
     * Retrieve xmlconnect images skin url
     *
     * @param string $name
     * @return string
     */
    public function getSkinImagesUrl($name = null)
    {
        return Mage::getDesign()->getSkinUrl('images/xmlconnect/' . $name);
    }

    /**
     * Return CustomSizeDirPrefix
     *
     * @deprecated will delete in the next version
     * @return string
     */
    public function getCustomSizeDirPrefix()
    {
        return $this->_getScreenSize() . DS . 'custom';
    }

    /**
     * Return FileDefaultSizeSuffixAsUrl
     *
     * @todo get rid of this method
     * @deprecated will delete in the next version
     * @param string $fileName
     * @return string
     */
    public function getFileDefaultSizeSuffixAsUrl($fileName)
    {
        return 'custom' . '/' . $this->_getScreenSize() . '/' . basename($fileName);
    }

    /**
     * Return getFileCustomDirSuffixAsUrl
     *
     * @deprecated will delete in the next version
     * @param string $confPath
     * @param string $fileName
     * @return string
     */
    public function getFileCustomDirSuffixAsUrl($confPath, $fileName)
    {
        return 'custom' . '/' . $this->_getScreenSize() . '/'
            . basename($this->_getResizedFilename($confPath, $fileName));
    }

    /**
     * Return correct size for given $imageName and device screen_size
     *
     * @deprecated will delete in the next version
     * @param string $imageName
     * @return int
     */
    public function getImageSizeForContent($imageName)
    {
        if (!isset($this->_content)) {
            $imageLimits = $this->getImageLimits($this->_getScreenSize());
            if (($imageLimits['content']) && is_array($imageLimits['content'])) {
                $this->_content = $imageLimits['content'];
            } else {
                $this->_content = array();
            }
        }
        $size = isset($this->_content[$imageName]) ? (int) $this->_content[$imageName] : 0;
        return $size;
    }

    /**
     * Return setting for interface images (image size limits)
     *
     * @deprecated will delete in the next version
     * @return array
     */
    public function getInterfaceImageLimits()
    {
        if (!isset($this->_interface)) {
            $imageLimits = $this->getImageLimits($this->_getScreenSize());
            $this->_interface = $imageLimits['interface'];
        }
        return $this->_interface;
    }

    /**
     * Return correct size for given $imageName and device screen_size
     *
     * @deprecated will delete in the next version
     * @param string $imagePath
     * @return int
     */
    public function getImageSizeForInterface($imagePath)
    {
        if (!isset($this->_interfacePath[$imagePath])) {
            /** @var $app Mage_XmlConnect_Model_Application */
            $app = Mage::helper('xmlconnect')->getApplication();
            if (!$app) {
                return 0;
            } else {
                $imageLimits = $this->getImageLimits($this->_getScreenSize());
                $size = $this->findPath($imageLimits, $imagePath);
                $this->_interfacePath[$imagePath] = $size;
            }
        }
        $size = isset($this->_interfacePath[$imagePath]) ? (int) $this->_interfacePath[$imagePath] : 0;
        return $size;
    }

    /**
     * Return the filesystem path to XmlConnect media files
     *
     * @param string $path Right part of the path
     * @return string
     */
    public function getMediaPath($path = '')
    {
        $path = trim($path);
        $result = Mage::getBaseDir('media') . DS . 'xmlconnect';

        if ($path) {
            if (strpos($path, DS) !== 0) {
                $path = DS . $path;
            }
            $result .= $path;
        }
        return $result;
    }

    /**
     * Return Url for media image
     *
     * @param string $image
     * @return string
     */
    public function getMediaUrl($image = '')
    {
        $image = trim($image);
        $result = Mage::getBaseUrl('media') . 'xmlconnect';

        if ($image) {
            if (strpos($image, '/') !== 0) {
                $image = '/' . $image;
            }
            $result .= $image;
        }
        return $result;
    }

    /**
     * Return URL for default design image
     *
     * @param string $image
     * @return string
     */
    public function getDefaultDesignUrl($image = '')
    {
        return $this->getSkinImagesUrl($this->getDefaultDesignSuffixAsUrl($image));
    }

    /**
     * Return suffix as URL for default design image
     *
     * @param string $image
     * @return string
     */
    public function getDefaultDesignSuffixAsUrl($image = '')
    {
        return 'design_default/' . trim(ltrim($image, '/'));
    }

    /**
     * Retrieve custom size image url
     *
     *
     * @param string $imageUrl
     * @param int $width
     * @param int $height
     * @return string|null
     */
    public function getCustomSizeImageUrl($imageUrl, $width = 100, $height = 100)
    {
        $screenSize = $width . 'x' . $height;
        $customDir = $this->getMediaPath('custom' . DS . $screenSize);
        $this->_verifyDirExist($customDir);
        $imageUrl = explode('/', $imageUrl);
        $file = array_pop($imageUrl);
        $filePath = Mage_XmlConnect_Model_Images::getBasePath() . DS . $file;
        if (!file_exists($customDir . DS . $file)) {
            $image = new Varien_Image($filePath);
            $widthOriginal = $image->getOriginalWidth();
            $heightOriginal = $image->getOriginalHeight();

            if ($width != $widthOriginal) {
                $widthOriginal = $width;
            }

            if ($height != $heightOriginal) {
                $heightOriginal = $height;
            }

            if (($widthOriginal != $image->getOriginalWidth()) ||
                ($heightOriginal != $image->getOriginalHeight()) ) {
                $image->keepTransparency(true);
                $image->keepFrame(true);
                $image->keepAspectRatio(true);
                $image->backgroundColor(array(255, 255, 255));
                $image->resize($widthOriginal, $heightOriginal);
                $image->save($customDir, basename($file));
            }
        }
        return $this->getMediaUrl("custom/{$screenSize}/" . basename($file));
    }

    /**
     * Ensure correct $screenSize value
     *
     * @deprecated will delete in the next version
     * @param string $screenSize
     * @return string
     */
    public function filterScreenSize($screenSize)
    {
        $screenSize = preg_replace('/[^0-9A-z_]/', '', $screenSize);
        if (isset($this->_imageLimits[$screenSize])) {
            return $screenSize;
        }
        $screenSizeExplodeArray = explode(self::XMLCONNECT_GLUE, $screenSize);
        $version = '';
        switch (count($screenSizeExplodeArray)) {
            case 2:
                $version = $screenSizeExplodeArray[1];
            case 1:
                $resolution = $screenSizeExplodeArray[0];
                break;
            default:
                $resolution = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_DEFAULT;
                break;
        }

        $sourcePath = empty($version) ? Mage_XmlConnect_Model_Application::APP_SCREEN_SOURCE_DEFAULT : $version;
        $xmlPath = 'screen_size/' . self::XMLCONNECT_GLUE . $resolution . '/' . $sourcePath . '/source';

        $source = Mage::getStoreConfig($xmlPath);
        if (!empty($source)) {
            $screenSize = $resolution . (empty($version) ? '' : self::XMLCONNECT_GLUE . $version);
        } else {
            $screenSize = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_DEFAULT;
        }
        return $screenSize;
    }

    /**
     * Return correct size array for given device screen_size(320x480/640x960_a)
     *
     * @deprecated will delete in the next version
     * @param string $screenSize
     * @return array
     */
    public function getImageLimits($screenSize = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_DEFAULT)
    {
        $defaultScreenSize = Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_DEFAULT;
        $defaultScreenSource = Mage_XmlConnect_Model_Application::APP_SCREEN_SOURCE_DEFAULT;

        $screenSize = preg_replace('/[^0-9A-z_]/', '', $screenSize);
        if (isset($this->_imageLimits[$screenSize])) {
            return $this->_imageLimits[$screenSize];
        }

        $screenSizeExplodeArray = explode(self::XMLCONNECT_GLUE, $screenSize);
        $version = '';
        switch (count($screenSizeExplodeArray)) {
            case 2:
                $version = $screenSizeExplodeArray[1];
            case 1:
                $resolution = $screenSizeExplodeArray[0];
                break;
            default:
                $resolution = $defaultScreenSize;
                break;
        }

        $sourcePath = empty($version) ? $defaultScreenSource : $version;
        $xmlPath = 'screen_size/' . self::XMLCONNECT_GLUE . $resolution . '/' . $sourcePath;

        $root = Mage::getStoreConfig($xmlPath);
        $updates = array();

        if (!empty($root)) {
            $screenSize = $resolution . (empty($version) ? '' : self::XMLCONNECT_GLUE . $version);
            $source = !empty($root['source']) ? $root['source'] : $defaultScreenSource;
            $updates = isset($root['updates']) && is_array($root['updates']) ? $root['updates'] : array();
        } else {
            $screenSize = $defaultScreenSize;
            $source = $defaultScreenSource;
        }

        $imageLimits = Mage::getStoreConfig('screen_size/' . $source);
        if (!is_array($imageLimits)) {
            $imageLimits = Mage::getStoreConfig('screen_size/default');
        }

        foreach ($updates as $update) {
            $path = $update['path'];
            $function = $update['function'];
            switch ($function) {
                case 'zoom':
                    $data = $update['data'];
                    $target =& $this->findPath($imageLimits, $path);
                    if (is_array($target)) {
                        array_walk_recursive($target, array($this, '_zoom'), $data);
                    } else {
                        $this->_zoom($target, null, $data);
                    }
                    break;
                case 'update':
                    $data = $update['data'];
                    $target =& $this->findPath($imageLimits, $path);
                    $target = $data;
                    break;
                case 'insert':
                    $data = $update['data'];
                    $target =& $this->findPath($imageLimits, $path);
                    if (($target !== null) && (is_array($target)) && (is_array($data))) {
                        foreach ($data as $key => $val) {
                            $target[$key] = $val;
                        }
                    }
                    break;
                case 'delete':
                    $data = $update['data'];
                    $target =& $this->findPath($imageLimits, $path);
                    if (isset($target[$data])) {
                        unset($target[$data]);
                    }
                    break;
                default:
                    break;
            }

        }
        if (!is_array($imageLimits)) {
            $imageLimits = array();
        }

        $this->_imageLimits[$screenSize] = $imageLimits;
        return $imageLimits;
    }

    /**
     * Return reference to the $path in $array
     *
     * @deprecated will delete in the next version
     * @param array &$array
     * @param string $path
     * @return mixed reference
     */
    public function &findPath(&$array, $path)
    {
        $target =& $array;
        if ($path !== '/') {
            $pathArray = explode('/', $path);
            foreach ($pathArray as $node) {
                if (is_array($target) && isset($target[$node])) {
                    $target =& $target[$node];
                } else {
                    return null;
                }
            }
        }
        return $target;
    }

    /**
     * Multiply given $item by $value if non array
     *
     * @deprecated will delete in the next version
     * @param mixed $item (argument to change)
     * @param mixed $key (used with array_walk_recursive function as a key of given array)
     * @param string $value (contains float)
     * @return null
     */
    protected function _zoom(&$item, $key, $value)
    {
        if (is_string($item)) {
            $item = (int) round($item * $value);
        }
    }

    /**
     * Ensure $dir exists (if not then create one)
     *
     * @param string $dir
     * @throw Mage_Core_Exception
     */
    protected function _verifyDirExist($dir)
    {
        try {
            $ioFile = new Varien_Io_File();
            $ioFile->checkAndCreateFolder($dir);
        } catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * Return customSizeUploadDir path
     *
     * @deprecated will delete in the next version
     * @param string $screenSize
     * @return string
     */
    public function getCustomSizeUploadDir($screenSize)
    {
        $screenSize = $this->filterScreenSize($screenSize);
        $customDir = $this->getMediaPath('custom' . DS . $screenSize);
        $this->_verifyDirExist($customDir);
        return $customDir;
    }

    /**
     * Return originalSizeUploadDir path
     *
     * @deprecated will delete in the next version
     * @return string
     */
    public function getOriginalSizeUploadDir()
    {
        $dir = $this->getMediaPath('original');
        $this->_verifyDirExist($dir);
        return $dir;
    }

    /**
     * Return oldUpload dir path  (media/xmlconnect)
     *
     * @deprecated from 1.6.1.0
     * @return string
     */
    public function getOldUploadDir()
    {
        $dir = $this->getMediaPath();
        $this->_verifyDirExist($dir);
        return $dir;
    }

    /**
     * Return default size upload dir path
     *
     * @deprecated will delete in the next version
     * @return string
     */
    public function getDefaultSizeUploadDir()
    {
        return $this->getCustomSizeUploadDir(Mage_XmlConnect_Model_Application::APP_SCREEN_SIZE_DEFAULT);
    }

    /**
     * Return array for interface images paths in the config
     *
     * @deprecated will delete in the next version
     * @return array
     */
    public function getInterfaceImagesPathsConf()
    {
        if (!isset($this->_confPaths)) {
            $this->_confPaths = array();
            $paths = $this->getInterfaceImagesPaths();
            if (is_array($paths)) {
                $len = strlen('conf/native/');
                while (list($path,) = each($paths)) {
                    $this->_confPaths[$path] = substr($path, $len);
                }
            }
        }
        return $this->_confPaths;
    }

    /**
     * Return
     * - default interface image path for specified $imagePath
     * - array of image paths
     *
     * @deprecated will delete in the next version
     * @param string $imagePath
     * @return array|string
     */
    public function getInterfaceImagesPaths($imagePath = null)
    {
        $paths = array (
            'conf/native/navigationBar/icon' => 'smallIcon_1_6.png',
            'conf/native/body/bannerImage' => 'banner_1_2.png',
            'conf/native/body/bannerIpadLandscapeImage' => 'banner_ipad_l.png',
            'conf/native/body/bannerIpadImage' => 'banner_ipad.png',
            'conf/native/body/bannerAndroidImage' => 'banner_android.png',
            'conf/native/body/backgroundImage' => 'accordion_open.png',
            'conf/native/body/backgroundIpadLandscapeImage' => 'accordion_open_ipad_l.png',
            'conf/native/body/backgroundIpadPortraitImage' => 'accordion_open_ipad_p.png',
            'conf/native/body/backgroundAndroidLandscapeImage' => 'accordion_open_android_l.png',
            'conf/native/body/backgroundAndroidPortraitImage' => 'accordion_open_android_p.png',
        );
        if ($imagePath == null) {
            return $paths;
        } else if (isset($paths[$imagePath])) {
            return $paths[$imagePath];
        } else {
            return null;
        }
    }

    /**
     * Check image and get full file path
     *
     * @deprecated will delete in the next version
     * @param string &$icon
     * @return bool
     */
    public function checkAndGetImagePath(&$icon)
    {
        $icon = basename($icon);
        if (is_file($this->getDefaultSizeUploadDir() . DS . $icon)) {
            $icon = $this->getDefaultSizeUploadDir() . DS . $icon;
            return true;
        }
        return false;
    }
}
