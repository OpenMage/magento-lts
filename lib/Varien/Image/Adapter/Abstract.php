<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Image
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @file       Abstract.php
 */

abstract class Varien_Image_Adapter_Abstract
{
    public $fileName = null;

    /**
     * @var int Color used to fill space when rotating image, do not confuse it with $_backgroundColor
     */
    public $imageBackgroundColor = 0;

    public const POSITION_TOP_LEFT = 'top-left';
    public const POSITION_TOP_RIGHT = 'top-right';
    public const POSITION_BOTTOM_LEFT = 'bottom-left';
    public const POSITION_BOTTOM_RIGHT = 'bottom-right';
    public const POSITION_STRETCH = 'stretch';
    public const POSITION_TILE = 'tile';
    public const POSITION_CENTER = 'center';

    /**
     * Image file type of the image $this->_fileName
     * e.g 2 for IMAGETYPE_JPEG
     *
     * @var int
     */
    protected $_fileType = null;

    /**
     * Absolute path to an original image
     *
     * @var string
     */
    protected $_fileName = null;

    /**
     * Image mime type e.g. image/jpeg
     *
     * @var string
     */
    protected $_fileMimeType = null;

    /**
     * Image file name (without path, with extension)
     *
     * @var string
     */
    protected $_fileSrcName = null;

    /**
     * Absolute path to a folder containing original image
     *
     * @var string
     */
    protected $_fileSrcPath = null;

    /**
     * Image resource created e.g. using imagecreatefromjpeg
     * This resource is being processed, so after open() it contains
     * original image, but after resize() it's already a scaled version.
     *
     * @see Varien_Image_Adapter_Gd2::open()
     * @var resource|GdImage
     */
    protected $_imageHandler = null;

    /**
     * Width of the image stored in $_imageHandler
     *
     * @see getMimeType
     * @var string|int
     */
    protected $_imageSrcWidth = null;

    /**
     * Height of the image stored in $_imageHandler
     *
     * @see getMimeType
     * @var string|int
     */
    protected $_imageSrcHeight = null;
    protected $_requiredExtensions = null;
    protected $_watermarkPosition = null;
    protected $_watermarkWidth = null;
    protected $_watermarkHeigth = null;
    protected $_watermarkImageOpacity = null;
    protected $_quality = null;

    protected $_keepAspectRatio;
    protected $_keepFrame;

    /**
     * @var bool If set to true and image format supports transparency (e.g. PNG),
     * transparency will be kept in scaled images. Otherwise transparent areas will be changed to $_backgroundColor
     */
    protected $_keepTransparency;

    /**
     * Array with RGB values for background color e.g. [255, 255, 255]
     * used e.g. when filling transparent color in scaled images
     *
     * @var array
     */
    protected $_backgroundColor;

    /**
     * @var bool If true, images will not be scaled up (when original image is smaller then requested size)
     */
    protected $_constrainOnly;

    abstract public function open($fileName);

    abstract public function save($destination = null, $newName = null);

    abstract public function display();

    abstract public function resize($width = null, $height = null);

    abstract public function rotate($angle);

    abstract public function crop($top = 0, $left = 0, $right = 0, $bottom = 0);

    abstract public function watermark($watermarkImage, $positionX = 0, $positionY = 0, $watermarkImageOpacity = 30, $repeat = false);

    abstract public function checkDependencies();

    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function getMimeType()
    {
        if ($this->_fileMimeType) {
            return $this->_fileMimeType;
        }
        $imageInfo = @getimagesize($this->_fileName);
        if ($imageInfo === false) {
            throw new RuntimeException('Failed to read image at ' . $this->_fileName);
        }
        $this->_imageSrcWidth = $imageInfo[0];
        $this->_imageSrcHeight = $imageInfo[1];
        $this->_fileType = $imageInfo[2];
        $this->_fileMimeType = $imageInfo['mime'];
        return $this->_fileMimeType;
    }

    /**
     * Retrieve Original Image Width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        $this->getMimeType();
        return $this->_imageSrcWidth;
    }

    /**
     * Retrieve Original Image Height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        $this->getMimeType();
        return $this->_imageSrcHeight;
    }

    public function setWatermarkPosition($position)
    {
        $this->_watermarkPosition = $position;
        return $this;
    }

    public function getWatermarkPosition()
    {
        return $this->_watermarkPosition;
    }

    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->_watermarkImageOpacity = $imageOpacity;
        return $this;
    }

    public function getWatermarkImageOpacity()
    {
        return $this->_watermarkImageOpacity;
    }

    public function setWatermarkWidth($width)
    {
        $this->_watermarkWidth = $width;
        return $this;
    }

    public function getWatermarkWidth()
    {
        return $this->_watermarkWidth;
    }

    public function setWatermarkHeigth($heigth)
    {
        $this->_watermarkHeigth = $heigth;
        return $this;
    }

    public function getWatermarkHeigth()
    {
        return $this->_watermarkHeigth;
    }

    /**
     * Get/set keepAspectRatio
     *
     * @param bool $value
     * @return bool
     */
    public function keepAspectRatio($value = null)
    {
        if (null !== $value) {
            $this->_keepAspectRatio = (bool) $value;
        }
        return $this->_keepAspectRatio;
    }

    /**
     * Get/set keepFrame
     *
     * @param bool $value
     * @return bool
     */
    public function keepFrame($value = null)
    {
        if (null !== $value) {
            $this->_keepFrame = (bool) $value;
        }
        return $this->_keepFrame;
    }

    /**
     * Get/set keepTransparency
     *
     * @param bool $value
     * @return bool
     */
    public function keepTransparency($value = null)
    {
        if (null !== $value) {
            $this->_keepTransparency = (bool) $value;
        }
        return $this->_keepTransparency;
    }

    /**
     * Get/set constrainOnly
     *
     * @param bool $value
     * @return bool
     */
    public function constrainOnly($value = null)
    {
        if (null !== $value) {
            $this->_constrainOnly = (bool) $value;
        }
        return $this->_constrainOnly;
    }

    /**
     * Get/set quality, values in percentage from 0 to 100
     *
     * @param int $value
     * @return int|null
     */
    public function quality($value = null)
    {
        if (null !== $value) {
            $this->_quality = (int) $value;
        }
        return $this->_quality;
    }

    /**
     * Get/set keepBackgroundColor
     *
     * @param array $value
     * @return array|void
     */
    public function backgroundColor($value = null)
    {
        if (null !== $value) {
            if ((!is_array($value)) || (3 !== count($value))) {
                return;
            }
            foreach ($value as $color) {
                if ((!is_integer($color)) || ($color < 0) || ($color > 255)) {
                    return;
                }
            }
            $this->_backgroundColor = $value;
        }

        return $this->_backgroundColor;
    }

    protected function _getFileAttributes()
    {
        $pathinfo = pathinfo($this->_fileName);

        $this->_fileSrcPath = $pathinfo['dirname'];
        $this->_fileSrcName = $pathinfo['basename'];
    }
}
