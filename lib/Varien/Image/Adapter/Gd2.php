<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Image
 */

class Varien_Image_Adapter_Gd2 extends Varien_Image_Adapter_Abstract
{
    protected $_requiredExtensions = ['gd'];

    private static $_callbacks = [
        IMAGETYPE_WEBP => ['output' => 'imagewebp', 'create' => 'imagecreatefromwebp'],
        IMAGETYPE_GIF  => ['output' => 'imagegif',  'create' => 'imagecreatefromgif'],
        IMAGETYPE_JPEG => ['output' => 'imagejpeg', 'create' => 'imagecreatefromjpeg'],
        IMAGETYPE_PNG  => ['output' => 'imagepng',  'create' => 'imagecreatefrompng'],
        IMAGETYPE_XBM  => ['output' => 'imagexbm',  'create' => 'imagecreatefromxbm'],
        IMAGETYPE_WBMP => ['output' => 'imagewbmp', 'create' => 'imagecreatefromwbmp'],
    ];

    /**
     * Whether image was resized or not
     *
     * @var bool
     */
    protected $_resized = false;

    public function __construct()
    {
        // Initialize shutdown function
        register_shutdown_function([$this, 'destruct']);
    }

    /**
     * Destroy object image on shutdown
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function destruct()
    {
        if (is_resource($this->_imageHandler) || (class_exists('GdImage') && $this->_imageHandler instanceof \GdImage)) {
            @imagedestroy($this->_imageHandler);
        }
    }

    /**
     * Opens image file.
     *
     * @param  string           $fileName
     * @throws Exception
     * @throws Varien_Exception
     */
    public function open($fileName)
    {
        $this->_fileName = $fileName;
        $this->getMimeType();
        $this->_getFileAttributes();
        $this->_imageHandler = call_user_func($this->_getCallback('create'), $this->_fileName);
    }

    /**
     * Checks whether memory limit is reached.
     *
     * @return bool
     * @deprecated
     */
    protected function _isMemoryLimitReached()
    {
        $limit = $this->_convertToByte(ini_get('memory_limit'));
        /**
         * In case if memory limit was converted to 0, treat it as unlimited
         */
        if ($limit === 0) {
            return false;
        }

        $size = getimagesize($this->_fileName);
        $requiredMemory = $size[0] * $size[1] * 3;

        return (memory_get_usage(true) + $requiredMemory) > $limit;
    }

    /**
     * Convert PHP memory limit value into bytes
     * Notation in value is supported only for PHP
     * Shorthand byte options are case insensitive
     *
     * @param  string $memoryValue
     * @return int
     * @deprecated
     * @see http://php.net/manual/en/faq.using.php#faq.using.shorthandbytes
     */
    protected function _convertToByte($memoryValue)
    {
        $memoryValue = trim($memoryValue);
        if (empty($memoryValue)) {
            return 0;
        }

        if (preg_match('~^([1-9][0-9]*)[\s]*(k|m|g)b?$~i', $memoryValue, $matches)) {
            $option = strtolower($matches[2]);
            $memoryValue = (int) $matches[1];
            switch ($option) {
                case 'g':
                    $memoryValue *= 1024;
                    // no break
                case 'm':
                    $memoryValue *= 1024;
                    // no break
                case 'k':
                    $memoryValue *= 1024;
                    break;
                default:
                    break;
            }
        }

        $memoryValue = (int) $memoryValue;

        return max($memoryValue, 0);
    }

    /**
     * @throws Exception
     */
    public function save($destination = null, $newName = null)
    {
        if (isset($destination) && isset($newName)) {
            $fileName = $destination . '/' . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $info = pathinfo($destination);
            $fileName = $destination;
            $destination = $info['dirname'];
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . '/' . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }

        $destinationDir = $destination ?? $this->_fileSrcPath;

        if (!is_writable($destinationDir)) {
            try {
                $io = new Varien_Io_File();
                $io->mkdir($destination);
            } catch (Exception $exception) {
                throw new Exception("Unable to write file into directory '{$destinationDir}'. Access forbidden.", $exception->getCode(), $exception);
            }
        }

        // convert palette based image to true color
        if ($this->_fileType == IMAGETYPE_WEBP) {
            imagepalettetotruecolor($this->_imageHandler);
        }

        if (!$this->_resized) {
            // keep alpha transparency
            $isAlpha     = false;
            $isTrueColor = false;
            $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
            if ($isAlpha) {
                if ($isTrueColor) {
                    $newImage = imagecreatetruecolor($this->_imageSrcWidth, $this->_imageSrcHeight);
                } else {
                    $newImage = imagecreate($this->_imageSrcWidth, $this->_imageSrcHeight);
                }

                $this->_fillBackgroundColor($newImage);
                imagecopy(
                    $newImage,
                    $this->_imageHandler,
                    0,
                    0,
                    0,
                    0,
                    $this->_imageSrcWidth,
                    $this->_imageSrcHeight,
                );
                $this->_imageHandler = $newImage;
            }
        }

        $functionParameters = [];
        $functionParameters[] = $this->_imageHandler;
        $functionParameters[] = $fileName;

        // set quality param for JPG file type
        if (!is_null($this->quality()) && $this->_fileType == IMAGETYPE_JPEG) {
            $functionParameters[] = $this->quality();
        }

        // make jpegs progressive
        if ($this->_fileType == IMAGETYPE_JPEG) {
            $threshold = (int) Mage::getStoreConfig('catalog/product_image/progressive_threshold');
            if ($threshold && $threshold <= (imagesx($this->_imageHandler) * imagesy($this->_imageHandler) / 1000000)) {
                imageinterlace($this->_imageHandler, true);
            }
        }

        // set quality param for PNG file type
        if (!is_null($this->quality()) && $this->_fileType == IMAGETYPE_PNG) {
            $functionParameters[] = 9;
        }

        call_user_func_array($this->_getCallback('output'), $functionParameters);
    }

    /**
     * @throws Exception
     */
    public function display()
    {
        header('Content-type: ' . $this->getMimeTypeWithOutFileType());
        call_user_func($this->_getCallback('output'), $this->_imageHandler);
    }

    /**
     * Obtain function name, basing on image type and callback type
     *
     * @param  string    $callbackType
     * @param  int       $fileType
     * @return string
     * @throws Exception
     */
    private function _getCallback($callbackType, $fileType = null, $unsupportedText = 'Unsupported image format.')
    {
        if (null === $fileType) {
            $fileType = $this->_fileType;
        }

        if (empty(self::$_callbacks[$fileType])) {
            throw new Exception("{$unsupportedText}. Type: {$fileType}. File: {$this->_fileName}");
        }

        if (empty(self::$_callbacks[$fileType][$callbackType])) {
            throw new Exception("Callback not found. Callbacktype: {$callbackType}. File: {$this->_fileName}");
        }

        return self::$_callbacks[$fileType][$callbackType];
    }

    /**
     * @throws Exception
     */
    private function _fillBackgroundColor(&$imageResourceTo)
    {
        // try to keep transparency, if any
        if ($this->_keepTransparency) {
            $isAlpha = false;
            $transparentIndex = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
            try {
                // fill truecolor png with alpha transparency
                if ($isAlpha) {
                    if (!imagealphablending($imageResourceTo, false)) {
                        throw new Exception('Failed to set alpha blending for PNG image. File: {$this->_fileName}');
                    }

                    $transparentAlphaColor = imagecolorallocatealpha($imageResourceTo, 0, 0, 0, 127);
                    if (false === $transparentAlphaColor) {
                        throw new Exception('Failed to allocate alpha transparency for PNG image. File: {$this->_fileName}');
                    }

                    if (!imagefill($imageResourceTo, 0, 0, $transparentAlphaColor)) {
                        throw new Exception('Failed to fill PNG image with alpha transparency. File: {$this->_fileName}');
                    }

                    if (!imagesavealpha($imageResourceTo, true)) {
                        throw new Exception('Failed to save alpha transparency into PNG image. File: {$this->_fileName}');
                    }

                    return $transparentAlphaColor;
                }

                // fill truecolor png with alpha transparency
                if (false !== $transparentIndex) {
                    // fill image with indexed non-alpha transparency
                    $transparentColor = false;
                    if ($transparentIndex >= 0 && $transparentIndex < imagecolorstotal($this->_imageHandler)) {
                        [$rgbR, $rgbG, $rgbB]  = array_values(imagecolorsforindex($this->_imageHandler, $transparentIndex));
                        $transparentColor = imagecolorallocate($imageResourceTo, (int) $rgbR, (int) $rgbG, (int) $rgbB);
                    }

                    if (false === $transparentColor) {
                        throw new Exception('Failed to allocate transparent color for image.');
                    }

                    if (!imagefill($imageResourceTo, 0, 0, $transparentColor)) {
                        throw new Exception('Failed to fill image with transparency.');
                    }

                    imagecolortransparent($imageResourceTo, $transparentColor);
                    return $transparentColor;
                }
            } catch (Exception) {
                // fallback to default background color
            }
        }

        [$rgbR, $rgbG, $rgbB] = $this->_backgroundColor;
        $color = imagecolorallocate($imageResourceTo, (int) $rgbR, (int) $rgbG, (int) $rgbB);
        if (!imagefill($imageResourceTo, 0, 0, $color)) {
            throw new Exception("Failed to fill image background with color {$rgbR} {$rgbG} {$rgbB}. File: {$this->_fileName}");
        }

        return $color;
    }

    /**
     * Gives true for a PNG with alpha, false otherwise
     *
     * @param  string $fileName
     * @return bool
     */
    public function checkAlpha($fileName)
    {
        return ((ord(file_get_contents($fileName, false, null, 25, 1)) & 6) & 4) == 4;
    }

    private function _getTransparency($imageResource, $fileType, &$isAlpha = false, &$isTrueColor = false)
    {
        $isAlpha     = false;
        $isTrueColor = false;
        // assume that transparency is supported by gif/png/webp only
        if (in_array($fileType, [IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            // check for specific transparent color
            $transparentIndex = imagecolortransparent($imageResource);
            if ($transparentIndex >= 0) {
                return $transparentIndex;
            }

            if ($fileType === IMAGETYPE_PNG || $fileType === IMAGETYPE_WEBP) {
                $isAlpha = $this->checkAlpha($this->_fileName);
                $isTrueColor = true;
                return $transparentIndex;
                // -1
            }
        }

        if ($fileType === IMAGETYPE_JPEG) {
            $isTrueColor = true;
        }

        return false;
    }

    /**
     * Change the image size
     *
     * @param  int       $width
     * @param  int       $height
     * @throws Exception
     */
    public function resize($width = null, $height = null)
    {
        if (empty($width) && empty($height)) {
            throw new Exception('Invalid image dimensions. File: {$this->_fileName}');
        }

        // calculate lacking dimension
        if (!$this->_keepFrame) {
            if (null === $width) {
                $width = round($height * ($this->_imageSrcWidth / $this->_imageSrcHeight));
            } elseif (null === $height) {
                $height = round($width * ($this->_imageSrcHeight / $this->_imageSrcWidth));
            }
        } elseif (null === $width) {
            $width = $height;
        } elseif (null === $height) {
            $height = $width;
        }

        // define coordinates of image inside new frame
        $srcX = 0;
        $srcY = 0;
        $dstWidth  = $width;
        $dstHeight = $height;
        if ($this->_keepAspectRatio) {
            // do not make picture bigger, than it is, if required
            if ($this->_constrainOnly) {
                if (($width >= $this->_imageSrcWidth) && ($height >= $this->_imageSrcHeight)) {
                    $dstWidth  = $this->_imageSrcWidth;
                    $dstHeight = $this->_imageSrcHeight;
                }
            }

            // keep aspect ratio
            if ($this->_imageSrcWidth / $this->_imageSrcHeight >= $width / $height) {
                $dstHeight = round(($dstWidth / $this->_imageSrcWidth) * $this->_imageSrcHeight);
            } else {
                $dstWidth = round(($dstHeight / $this->_imageSrcHeight) * $this->_imageSrcWidth);
            }
        }

        // define position in center (TODO: add positions option)
        $dstY = round(($height - $dstHeight) / 2);
        $dstX = round(($width - $dstWidth) / 2);

        // get rid of frame (fallback to zero position coordinates)
        if (!$this->_keepFrame) {
            $width  = $dstWidth;
            $height = $dstHeight;
            $dstY = 0;
            $dstX = 0;
        }

        // create new image
        $isAlpha     = false;
        $isTrueColor = false;
        $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha, $isTrueColor);
        if ($isTrueColor) {
            $newImage = imagecreatetruecolor($width, $height);
        } else {
            $newImage = imagecreate($width, $height);
        }

        // fill new image with required color
        $this->_fillBackgroundColor($newImage);

        // resample source image and copy it into new frame
        imagecopyresampled(
            $newImage,
            $this->_imageHandler,
            $dstX,
            $dstY,
            $srcX,
            $srcY,
            $dstWidth,
            $dstHeight,
            $this->_imageSrcWidth,
            $this->_imageSrcHeight,
        );
        $this->_imageHandler = $newImage;
        $this->refreshImageDimensions();
        $this->_resized = true;
    }

    public function rotate($angle)
    {
        /*
                $isAlpha = false;
                $backgroundColor = $this->_getTransparency($this->_imageHandler, $this->_fileType, $isAlpha);
                list($r, $g, $b) = $this->_backgroundColor;
                if ($isAlpha) {
                    $backgroundColor = imagecolorallocatealpha($this->_imageHandler, 0, 0, 0, 127);
                }
                elseif (false === $backgroundColor) {
                    $backgroundColor = imagecolorallocate($this->_imageHandler, $r, $g, $b);
                }
                $this->_imageHandler = imagerotate($this->_imageHandler, $angle, $backgroundColor);
        //*/
        $this->_imageHandler = imagerotate($this->_imageHandler, $angle, $this->imageBackgroundColor);
        $this->refreshImageDimensions();
    }

    /**
     * @throws Exception
     */
    public function watermark($watermarkImage, $positionX = 0, $positionY = 0, $watermarkImageOpacity = 30, $repeat = false)
    {
        [$watermarkSrcWidth, $watermarkSrcHeight, $watermarkFileType, ] = getimagesize($watermarkImage);
        $this->_getFileAttributes();
        $watermark = call_user_func($this->_getCallback(
            'create',
            $watermarkFileType,
            'Unsupported watermark image format.',
        ), $watermarkImage);

        if ($this->getWatermarkWidth()
            && $this->getWatermarkHeigth()
            && ($this->getWatermarkPosition() != self::POSITION_STRETCH)
        ) {
            $newWatermark = imagecreatetruecolor($this->getWatermarkWidth(), $this->getWatermarkHeigth());
            imagealphablending($newWatermark, false);
            $col = imagecolorallocate($newWatermark, 255, 255, 255);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->getWatermarkWidth(), $this->getWatermarkHeigth(), $col);
            imagealphablending($newWatermark, true);
            imagesavealpha($newWatermark, true);
            imagecopyresampled(
                $newWatermark,
                $watermark,
                0,
                0,
                0,
                0,
                $this->getWatermarkWidth(),
                $this->getWatermarkHeigth(),
                imagesx($watermark),
                imagesy($watermark),
            );
            $watermark = $newWatermark;
        }

        if ($this->getWatermarkPosition() == self::POSITION_TILE) {
            $repeat = true;
        } elseif ($this->getWatermarkPosition() == self::POSITION_STRETCH) {
            $newWatermark = imagecreatetruecolor($this->_imageSrcWidth, $this->_imageSrcHeight);
            imagealphablending($newWatermark, false);
            $col = imagecolorallocate($newWatermark, 255, 255, 255);
            imagecolortransparent($newWatermark, $col);
            imagefilledrectangle($newWatermark, 0, 0, $this->_imageSrcWidth, $this->_imageSrcHeight, $col);
            imagealphablending($newWatermark, true);
            imagesavealpha($newWatermark, true);
            imagecopyresampled(
                $newWatermark,
                $watermark,
                0,
                0,
                0,
                0,
                $this->_imageSrcWidth,
                $this->_imageSrcHeight,
                imagesx($watermark),
                imagesy($watermark),
            );
            $watermark = $newWatermark;
        } elseif ($this->getWatermarkPosition() == self::POSITION_CENTER) {
            $positionX = ($this->_imageSrcWidth / 2 - imagesx($watermark) / 2);
            $positionY = ($this->_imageSrcHeight / 2 - imagesy($watermark) / 2);
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_TOP_RIGHT) {
            $positionX = ($this->_imageSrcWidth - imagesx($watermark));
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_TOP_LEFT) {
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_BOTTOM_RIGHT) {
            $positionX = ($this->_imageSrcWidth - imagesx($watermark));
            $positionY = ($this->_imageSrcHeight - imagesy($watermark));
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        } elseif ($this->getWatermarkPosition() == self::POSITION_BOTTOM_LEFT) {
            $positionY = ($this->_imageSrcHeight - imagesy($watermark));
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        }

        if ($repeat === false) {
            imagecopymerge(
                $this->_imageHandler,
                $watermark,
                $positionX,
                $positionY,
                0,
                0,
                imagesx($watermark),
                imagesy($watermark),
                $this->getWatermarkImageOpacity(),
            );
        } else {
            $offsetX = $positionX;
            $offsetY = $positionY;
            while ($offsetY <= ($this->_imageSrcHeight + imagesy($watermark))) {
                while ($offsetX <= ($this->_imageSrcWidth + imagesx($watermark))) {
                    imagecopymerge(
                        $this->_imageHandler,
                        $watermark,
                        $offsetX,
                        $offsetY,
                        0,
                        0,
                        imagesx($watermark),
                        imagesy($watermark),
                        $this->getWatermarkImageOpacity(),
                    );
                    $offsetX += imagesx($watermark);
                }

                $offsetX = $positionX;
                $offsetY += imagesy($watermark);
            }
        }

        imagedestroy($watermark);
        $this->refreshImageDimensions();
    }

    public function crop($top = 0, $left = 0, $right = 0, $bottom = 0)
    {
        if ($left == 0 && $top == 0 && $right == 0 && $bottom == 0) {
            return;
        }

        $newWidth = $this->_imageSrcWidth - $left - $right;
        $newHeight = $this->_imageSrcHeight - $top - $bottom;

        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        if ($this->_fileType == IMAGETYPE_PNG) {
            $this->_saveAlpha($canvas);
        }

        imagecopyresampled(
            $canvas,
            $this->_imageHandler,
            0,
            0,
            $left,
            $top,
            $newWidth,
            $newHeight,
            $newWidth,
            $newHeight,
        );

        $this->_imageHandler = $canvas;
        $this->refreshImageDimensions();
    }

    /**
     * @throws Exception
     */
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
        $this->_imageSrcWidth = imagesx($this->_imageHandler);
        $this->_imageSrcHeight = imagesy($this->_imageHandler);
    }

    /*
     * Fixes saving PNG alpha channel
     */
    private function _saveAlpha($imageHandler)
    {
        $background = imagecolorallocate($imageHandler, 0, 0, 0);
        imagecolortransparent($imageHandler, $background);
        imagealphablending($imageHandler, false);
        imagesavealpha($imageHandler, true);
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
