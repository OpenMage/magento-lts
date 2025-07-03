<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Varien_Image_Adapter_Imagick extends Varien_Image_Adapter_Abstract
{
    /**
     * @var Imagick
     */
    protected $_imageHandler;

    /**
     * @var string[]
     */
    protected $_requiredExtensions = ["imagick"];

    /**
     * Whether image was resized or not
     *
     * @var bool
     */
    protected $_resized = false;

    public function __construct()
    {
        register_shutdown_function(array($this, 'destruct'));
    }

    /**
     * Destroy object image on shutdown
     */
    public function destruct()
    {
        if ($this->_imageHandler) {
            $this->_imageHandler->destroy();
        }
    }

    /**
     * Opens image file.
     *
     * @param string $filename
     * @throws ImagickException
     * @throws Mage_Core_Exception
     */
    public function open($filename)
    {
        $this->_fileName = $filename;
        $this->getMimeType();
        $this->_getFileAttributes();

        $this->_imageHandler = new Imagick();
        $this->_imageHandler->readImage($filename);

        $orientation = $this->_imageHandler->getImageProperty('exif:Orientation');
        if (!empty($orientation)) {
            switch ($orientation) {
                case 1:
                    // Do nothing
                    break;
                case 3:
                    $this->_imageHandler->rotateImage('#000000', 180);
                    break;
                case 6:
                    $this->_imageHandler->rotateImage('#000000', 90);
                    break;
                case 8:
                    $this->_imageHandler->rotateImage('#000000', -90);
                    break;
                default:
                    Mage::throwException('Unsupported EXIF orientation: ' . $orientation);
            }
        }
        $this->refreshImageDimensions();
    }

    public function save($destination = null, $newName = null)
    {
        if ($destination && $newName) {
            $fileName = $destination . "/" . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $info = pathinfo($destination);
            $fileName = $destination;
            $destination = $info['dirname'];
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . "/" . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }

        $destinationDir = (isset($destination)) ? $destination : $this->_fileSrcPath;

        if (!is_writable($destinationDir)) {
            try {
                $io = new Varien_Io_File();
                $io->mkdir($destination);
            } catch (Exception $e) {
                throw new Exception("Unable to write file into directory '{$destinationDir}'. Access forbidden.");
            }
        }

        // set quality param for PNG file type
        $quality = $this->quality();
        if ($quality !== null) {
            if ($quality < 1) {
                throw new RuntimeException('Image compression quality cannot be < 1');
            } elseif ($quality > 100) {
                throw new RuntimeException('Image compression quality cannot be > 100');
            }
            $this->_imageHandler->setCompressionQuality($quality);
        }
        $this->_imageHandler->writeImage($fileName);
    }

    /**
     * @inheritDoc
     */
    public function display()
    {
        header("Content-type: " . $this->getMimeTypeWithOutFileType());
        echo $this->_imageHandler->getImageBlob();
    }

    /**
     * @param int $frameWidth
     * @param int $frameHeight
     */
    public function resize($frameWidth = null, $frameHeight = null)
    {
        if (!$frameWidth && !$frameHeight) {
            throw new RuntimeException('Invalid image dimensions.');
        }

        // calculate lacking dimension
        if ($this->_keepFrame) {
            if (null === $frameWidth) {
                $frameWidth = $frameHeight;
            } elseif (null === $frameHeight) {
                $frameHeight = $frameWidth;
            }
        } else {
            if (null === $frameWidth) {
                $frameWidth = round($frameHeight * ($this->_imageSrcWidth / $this->_imageSrcHeight));
            } elseif (null === $frameHeight) {
                $frameHeight = round($frameWidth * ($this->_imageSrcHeight / $this->_imageSrcWidth));
            }
        }

        // define coordinates of image inside new frame
        $dstWidth = $frameWidth;
        $dstHeight = $frameHeight;

        if ($this->_keepAspectRatio) {
            // do not make picture bigger, than it is, if required
            if ($this->_constrainOnly) {
                if (($frameWidth >= $this->_imageSrcWidth) && ($frameHeight >= $this->_imageSrcHeight)) {
                    $dstWidth = $this->_imageSrcWidth;
                    $dstHeight = $this->_imageSrcHeight;
                }
            }
            // keep aspect ratio
            if ($this->_imageSrcWidth / $this->_imageSrcHeight >= $frameWidth / $frameHeight) {
                $dstHeight = ($dstWidth / $this->_imageSrcWidth) * $this->_imageSrcHeight;
            } else {
                $dstWidth = ($dstHeight / $this->_imageSrcHeight) * $this->_imageSrcWidth;
            }
        }

        $dstWidth = (int)round($dstWidth);
        $dstHeight = (int)round($dstHeight);
        $frameWidth = (int)round($frameWidth);
        $frameHeight = (int)round($frameHeight);


        $filter = \Imagick::FILTER_LANCZOS;
        $this->_imageHandler->resizeImage($dstWidth, $dstHeight, $filter, 1);

        if ($this->_keepFrame) {
            // Add borders top+bottom or left+right
            $canvas = new Imagick();
            // TODO support more than just JPG?
            $canvas->newImage($frameWidth, $frameHeight, 'white', 'jpg');
            $offsetX = (int)round(($frameWidth - $dstWidth) / 2);
            $offsetY = (int)round(($frameHeight - $dstHeight) / 2);
            $canvas->compositeImage($this->_imageHandler, \Imagick::COMPOSITE_OVER, $offsetX, $offsetY);
            $this->_imageHandler = $canvas;
        }

        $this->refreshImageDimensions();
        $this->_resized = true;
    }

    /**
     * @param $angle float
     * @return void
     * @throws ImagickException
     */
    public function rotate($angle)
    {
        $this->_imageHandler->rotateImage($this->imageBackgroundColor, $angle);
        $this->refreshImageDimensions();
    }

    public function watermark($watermarkImage, $positionX = 0, $positionY = 0, $watermarkImageOpacity = 30, $repeat = false)
    {
        throw new RuntimeException('Watermark is not supported.');
    }

    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0)
    {
        if ($left == 0 && $top == 0 && $right == 0 && $bottom == 0) {
            return;
        }

        $newWidth = $this->_imageSrcWidth - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top - $bottom;

        $this->_imageHandler->cropImage($newWidth, $newHeight, $left, $top);
        $this->refreshImageDimensions();
    }

    public function checkDependencies()
    {
        foreach ($this->_requiredExtensions as $value) {
            if (!extension_loaded($value)) {
                throw new Exception("Required PHP extension '{$value}' was not loaded.");
            }
        }
    }

    private function refreshImageDimensions()
    {
        $this->_imageSrcWidth = $this->_imageHandler->getImageWidth();
        $this->_imageSrcHeight = $this->_imageHandler->getImageHeight();
    }

    /**
     * Gives real mime-type with not considering file type field
     *
     * @return string
     */
    public function getMimeTypeWithOutFileType()
    {
        return $this->_fileMimeType;
    }
}
