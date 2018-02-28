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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Images model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Images extends Mage_Core_Model_Abstract
{
    /**
     * Array of required submit data
     *
     * @var array
     */
    protected $_requiredSubmitData = array('application_id', 'image_type', 'order');

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/images');
        parent::_construct();
    }

    /**
     * Delete item and repair order of images
     *
     * @return Mage_XmlConnect_Model_Images
     */
    public function deleteAndRepairOrder()
    {
        Mage::helper('xmlconnect')->getApplication()->getImageActionModel()->deleteAction($this->getId());
        $this->delete()->_deleteImageFiles($this->getImageFile())->getResource()->repairOrder($this);
        return $this;
    }

    /**
     * Delete image action
     *
     * @return Mage_XmlConnect_Model_Images
     */
    public function deleteImageAction()
    {
        Mage::helper('xmlconnect')->getApplication()->getImageActionModel()->deleteAction($this->getId());
        return $this;
    }

    /**
     * Remove all image files
     *
     * @param string $imageFile
     * @param bool $deleteOriginal
     * @return Mage_XmlConnect_Model_Images
     */
    protected function _deleteImageFiles($imageFile, $deleteOriginal = true)
    {
        $convertedImageFile = $this->_convertFileExtensionToPng($imageFile);
        $ioAdapter = new Varien_Io_File();
        $baseImageDir = Mage::helper('xmlconnect/image')->getMediaPath('custom');
        if (is_dir($baseImageDir)) {
            $dirArray = array_diff(scandir($baseImageDir), array('.', '..'));
            foreach ($dirArray as $item) {
                if (!is_dir($baseImageDir . DS . $item)) {
                    continue;
                }
                $ioAdapter->rm($baseImageDir . DS . $item . DS . basename($convertedImageFile));
            }
        }
        if ($deleteOriginal) {
            $ioAdapter->rm(self::getBasePath() . DS . basename($imageFile));
        }
        return $this;
    }

    /**
     * Check image type
     *
     * @param string $type
     * @return Mage_XmlConnect_Model_Images
     */
    public function checkType($type = '')
    {
        if ($type) {
            $this->setImageType($type);
        }

        if (!$this->getImageType()) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Image type is required.'));
        }
        return $this;
    }

    /**
     * Get image count for image type
     *
     * @param string $type
     * @return int
     */
    public function getImageCount($type = '')
    {
        if (!$type) {
            $type = $this->getImageType();
        }
        return Mage::helper('xmlconnect')->getDeviceHelper()->getImageCount($type);
    }

    /**
     * Check is application record exists
     *
     * @param int $appId
     * @return Mage_XmlConnect_Model_Images
     */
    public function checkApplication($appId = 0)
    {
        if (!$appId) {
            $appId = $this->getApplicationId();
        }

        $appModel = Mage::getModel('xmlconnect/application')->load($appId);
        if (!$appModel->getId()) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Application doesn\'t exist.'));
        }
        return $this;
    }

    /**
     * Validate submit data
     *
     * @param array $data
     * @return Mage_XmlConnect_Model_Images
     */
    public function validateAndSetSubmitData($data)
    {
        foreach ($this->_requiredSubmitData as $submitValue) {
            if (empty($data[$submitValue])) {
                Mage::throwException(Mage::helper('xmlconnect')->__('Image %s is required.', $submitValue));
            } else {
                $this->setData($submitValue, $data[$submitValue]);
            }
        }
        return $this;
    }

    /**
     * Retrieve Base files path
     *
     * @param string $filePath
     * @return string
     */
    public static function getBasePath($filePath = '')
    {
        if ($filePath && strpos($filePath, DS) !== 0) {
            $filePath = DS . $filePath;
        }
        return Mage::getBaseDir('media') . DS . 'xmlconnect' . DS . 'original' . $filePath;
    }

    /**
     * Get original image url
     *
     * @param string $image
     * @return string
     */
    public function getImageUrl($image = '')
    {
        if ($image && strpos($image, '/') !== 0) {
            $image = '/' . $image;
        }

        return Mage::getBaseUrl('media') . 'xmlconnect/original' . $image;
    }

    /**
     * Get device image array by type
     *
     * @param string $type
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getDeviceImagesByType($type, $limit = null, $offset = null)
    {
        if (!$this->getApplicationId()) {
            $this->setApplicationId(Mage::helper('xmlconnect')->getApplicationId());
        }
        return $this->getCollection()->addApplicationToFilter($this->getApplicationId())->addImageTypeToFilter($type)
            ->setPositionOrder()->setLimit($limit, $offset)->getData();
    }

    /**
     * Get device image by type
     *
     * @param string $type
     * @return array|bool
     */
    public function getImageItemByType($type)
    {
        if (!$this->getApplicationId()) {
            $this->setApplicationId(Mage::helper('xmlconnect')->getApplicationId());
        }
        return $this->getCollection()->addApplicationToFilter($this->getApplicationId())->addImageTypeToFilter($type)
            ->setPositionOrder()->fetchItem();
    }

    /**
     * Check is image exists in original folder
     *
     * @param string $imagePath
     * @return bool
     */
    protected  function _checkImageExists($imagePath)
    {
        $image = new Varien_Io_File();
        return $image->fileExists(self::getBasePath() . $imagePath);
    }

    /**
     * Retrieve custom size image url
     *
     * @param string $imageFile
     * @param int $width
     * @param int $height
     * @return string|bool
     */
    public function getCustomSizeImageUrl($imageFile, $width = 100, $height = 100)
    {
        /** @var $imageHelper Mage_XmlConnect_Helper_Image */
        $imageHelper = Mage::helper('xmlconnect/image');
        $screenSize = $width . 'x' . $height;
        $customDir = $imageHelper->getMediaPath('custom' . DS . $screenSize);
        $ioFile = new Varien_Io_File();
        $ioFile->checkAndCreateFolder($customDir);
        $filePath = self::getBasePath() . DS . $imageFile;
        $isImagePng = true;

        if (!$ioFile->fileExists($filePath)) {
            return false;
        }

        $originalImageType = $this->_getImageType($filePath);
        if ($originalImageType !== IMAGETYPE_PNG) {
            $imageFile = $this->_convertFileExtensionToPng($imageFile);
            $isImagePng = false;
        }

        $customSizeFile = $customDir . DS . $imageFile;
        if (!file_exists($customSizeFile)) {
            if (!$isImagePng) {
                $filePath = $this->_forcedConvertPng($filePath, $customSizeFile, $originalImageType);
            }

            $image = new Varien_Image($filePath);
            $widthOriginal = $image->getOriginalWidth();
            $heightOriginal = $image->getOriginalHeight();

            if ($width != $widthOriginal) {
                $widthOriginal = $width;
            }

            if ($height != $heightOriginal) {
                $heightOriginal = $height;
            }

            if (($widthOriginal != $image->getOriginalWidth()) || ($heightOriginal != $image->getOriginalHeight()) ) {
                $image->keepTransparency(true);
                $image->keepFrame(true);
                $image->keepAspectRatio(true);
                $image->backgroundColor(array(0, 0, 0));
                $image->resize($widthOriginal, $heightOriginal);
                $image->save($customDir, basename($imageFile));
            } else {
                $ioFile->cp($filePath, $customSizeFile);
            }
        }
        return $imageHelper->getMediaUrl("custom/{$screenSize}/" . basename($imageFile));
    }

    /**
     * Save image data
     *
     * @param int $applicationId
     * @param string $imageFile
     * @param string $imageType
     * @param int $order
     * @return Mage_XmlConnect_Model_Images
     */
    public function saveImage($applicationId, $imageFile, $imageType, $order)
    {
        $this->getResource()->saveImage($applicationId, $imageFile, $imageType, $order);
        return $this;
    }

    /**
     * Add image node to the config
     * Used for backward compatibility only
     *
     * @param array $config
     * @return Mage_XmlConnect_Model_Images
     */
    public function loadOldImageNodes(&$config)
    {
        $iconImageFile = $this->getImageItemByType(Mage_XmlConnect_Model_Device_Abstract::IMAGE_TYPE_ICON);

        if (is_object($iconImageFile) && $iconImageFile->getImageFile()) {
            $iconImageType = Mage_XmlConnect_Model_Device_Abstract::IMAGE_TYPE_ICON;
            $config['navigationBar']['icon'] = $this->getCustomSizeImageUrl(
                $iconImageFile->getImageFile(),
                $this->getImageLimitParam($iconImageType, 'width'),
                $this->getImageLimitParam($iconImageType, 'height')
            );
        }

        $imageArray = $this->_getOldConfigImageNodeArray();
        $imageArray = $imageArray[Mage::helper('xmlconnect')->getDeviceType()];
        foreach ($imageArray as $node => $imageType) {
            $imageFile = $this->getImageItemByType($imageType);
            if (!is_object($imageFile) || !$imageFile->getImageFile()) {
                continue;
            }
            $config['body'][$node] = $this->getCustomSizeImageUrl(
                $imageFile->getImageFile(),
                $this->getImageLimitParam($imageType, 'width'),
                $this->getImageLimitParam($imageType, 'height')
            );
        }
        return $this;
    }

    /**
     * Get image url for current screen size
     *
     * @param $imageFile
     * @param $imageType
     * @return bool|string
     */
    public function getScreenSizeImageUrlByType($imageFile, $imageType)
    {
        if (is_numeric($this->getImageLimitParam($imageType))) {
            $width = $height = $this->getImageLimitParam($imageType);
        } else {
            $width = $this->getImageLimitParam($imageType, 'width');
            $height = $this->getImageLimitParam($imageType, 'height');
        }
        if (!is_numeric($width) || !is_numeric($height)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Image limits don\'t recognized for "%s" image type', $imageType));
        }

        return $this->getCustomSizeImageUrl($imageFile, $width, $height);
    }

    /**
     * Get image limit param value
     *
     * @param string $imageType
     * @param string|null $key
     * @param string $screenSize
     * @return mixed
     */
    public function getImageLimitParam($imageType = '', $key = null, $screenSize = '')
    {
        return $this->getImageLimitsModel()->getImageLimitsByType($imageType, $key, $screenSize);
    }

    /**
     * Get Image Limits model
     *
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    public function getImageLimitsModel()
    {
        return Mage::helper('xmlconnect')->getApplication()->getImageLimitsModel();
    }

    /**
     * Get old config image node for devices
     *
     * @return array
     */
    protected function _getOldConfigImageNodeArray()
    {
        // array as old config node => new image type (separated by device types)
        return array(
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID => array(
                'bannerAndroidImage' => Mage_XmlConnect_Model_Device_Android::IMAGE_TYPE_PORTRAIT_BANNER
            ),
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE => array(
                'bannerImage' => Mage_XmlConnect_Model_Device_Iphone::IMAGE_TYPE_PORTRAIT_BANNER,
                'backgroundImage' => Mage_XmlConnect_Model_Device_Iphone::IMAGE_TYPE_PORTRAIT_BACKGROUND
            ),
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD => array(
                'bannerIpadLandscapeImage' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BANNER,
                'bannerIpadImage' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BANNER,
                'backgroundIpadLandscapeImage' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BACKGROUND,
                'backgroundIpadPortraitImage' => Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BACKGROUND
            )
        );
    }

    /**
     * Update old image records in database
     * For data upgrade usage only
     *
     * @see data upgrade file: mysql4-data-upgrade-1.6.0.0-1.6.0.0.1.php
     * @param array $records
     * @return null
     */
    public function dataUpgradeOldConfig($records)
    {
        // array as old config node => new image type (separated by device types)
        $oldConfigPathByDeviceType = $this->_getOldConfigImageNodeArray();

        /** @var $applicationModel Mage_XmlConnect_Model_Application */
        $applicationModel = Mage::getModel('xmlconnect/application');

        /** @var $configModel Mage_XmlConnect_Model_ConfigData */
        $configModel = $applicationModel->getConfigModel();
        $deprecatedFlag = Mage_XmlConnect_Model_Application::DEPRECATED_CONFIG_FLAG;

        foreach ($records as $application_id) {
            /** @var $applicationModel Mage_XmlConnect_Model_Application */
            $applicationModel->load($application_id);
            $configPathList = $oldConfigPathByDeviceType[$applicationModel->getType()];
            $configData = $configModel->loadApplicationData($application_id);

            // old icon config path
            $iconConfigPath = 'native/navigationBar/icon';
            if (!empty($configData[$deprecatedFlag][$iconConfigPath])) {
                // Add icon to image table
                $iconFile = basename($configData[$deprecatedFlag][$iconConfigPath]);
                $this->saveImage(
                    $application_id, $iconFile, Mage_XmlConnect_Model_Device_Abstract::IMAGE_TYPE_ICON, 1
                );

                // delete old icon record from config_data table
                $configModel->deleteConfig($application_id, $deprecatedFlag, $iconConfigPath);

                // delete all icon files from file system
                $this->_deleteImageFiles($iconFile, false);
            }

            $i = 0;
            // old config path prefix native/body/
            $configPrefix = 'native/body/';
            foreach ($configPathList as $configPath => $imageType) {
                if (empty($configData[$deprecatedFlag][$configPrefix . $configPath])) {
                    continue;
                }
                $fileName = basename($configData[$deprecatedFlag][$configPrefix . $configPath]);
                // add new record to image table
                $this->saveImage($application_id, $fileName, $imageType, ++$i);

                // delete all image files from file system
                $this->_deleteImageFiles($fileName, false);

                // remove old record from config_data table
                $configModel->deleteConfig($application_id, $deprecatedFlag, $configPrefix . $configPath);
            }
        }
        return $this;
    }

    /**
     * Convert uploaded file to PNG
     *
     * @param string $originalFile
     * @param string $destinationFile
     * @param int|null $originalImageType
     * @return string
     */
    protected function _forcedConvertPng($originalFile, $destinationFile, $originalImageType = null)
    {
        switch ($originalImageType) {
            case IMAGETYPE_GIF:
                $img = imagecreatefromgif($originalFile);
                imagealphablending($img, false);
                imagesavealpha($img, true);
                break;
            case IMAGETYPE_JPEG:
                $img = imagecreatefromjpeg($originalFile);
                break;
            case IMAGETYPE_WBMP:
                $img = imagecreatefromwbmp($originalFile);
                break;
            case IMAGETYPE_XBM:
                $img = imagecreatefromxbm($originalFile);
                break;
            default:
                return '';
        }
        imagepng($img, $destinationFile);
        imagedestroy($img);

        return $destinationFile;
    }

    /**
     * Convert image file extension to PNG
     *
     * @param string $fileName
     * @return string
     */
    protected function _convertFileExtensionToPng($fileName)
    {
        $dotPosition = strrpos($fileName, '.');
        if ($dotPosition !== false) {
            $fileName = substr($fileName, 0 , $dotPosition);
        }
        $fileName .= '.png';

        return $fileName;
    }

    /**
     * Get image type
     *
     * @param string $filePath
     * @return int
     */
    protected function _getImageType($filePath)
    {
        list(,, $originalImageType) = getimagesize($filePath);
        return $originalImageType;
    }
}
