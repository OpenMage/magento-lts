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
 * @category   Varien
 * @package    Varien_File
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * @file        Image.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Varien_File_Uploader_Image extends Varien_File_Uploader
{

    function __construct($file=null)
    {
        $this->newUploader($file);
    }

    /**
     * Resizes an image
     * Set parameters to the wanted (or maximum/minimum) width for the processed image, in pixels
     */
    public function resize($width=null, $height=null)
    {
        $this->uploader->image_resize = true;

        $this->uploader->image_ratio_x = ( $width == null ) ? true : false;
        $this->uploader->image_ratio_y = ( $height == null ) ? true : false;

        $this->uploader->image_x = $width;
        $this->uploader->image_y = $height;
    }

    /**
     * Rotates the image by increments of 45 degrees
     *
     * Value is either 90, 180 or 270
     *
     * Default value is NULL (no rotation)
     *
     */
    public function rotate($degrees=null)
    {
        $this->uploader->image_rotate = intval($degrees);
    }

    /**
     * Flips the image vertically or horizontally
     *
     * Value is either 'h' or 'v', as in horizontal and vertical
     *
     * Default value is h (flip horizontally)
     *
     * @access public
     * @var string;
     */
    public function flip($type="h")
    {
        $this->uploader->image_flip = $type;
    }

    /**
     * Crops an image
     *
     * $crop values are four dimensions, or two, or one (CSS style)
     * They represent the amount cropped top, right, bottom and left.
     * These values can either be in an array, or a space separated string.
     * Each value can be in pixels (with or without 'px'), or percentage (of the source image)
     *
     * For instance, are valid:
     * $foo->crop(20)                  OR array(20);
     * $foo->crop('20px')              OR array('20px');
     * $foo->crop('20 40')             OR array('20', 40);
     * $foo->crop('-20 25%')           OR array(-20, '25%');
     * $foo->crop('20px 25%')          OR array('20px', '25%');
     * $foo->crop('20% 25%')           OR array('20%', '25%');
     * $foo->crop('20% 25% 10% 30%')   OR array('20%', '25%', '10%', '30%');
     * $foo->crop('20px 25px 2px 2px') OR array('20px', '25%px', '2px', '2px');
     * $foo->crop('20 25% 40px 10%')   OR array(20, '25%', '40px', '10%');
     *
     * If a value is negative, the image will be expanded, and the extra parts will be filled with black
     *
     * Default value is NULL (no cropping)
     */
    public function crop($crop=0)
    {
        $this->uploader->image_crop = $crop;
    }

    /**
     * Coverts an image
     *
     * Possibles $color values are : ''; 'png'; 'jpeg'; 'gif'
     *
     * Default value is 'jpeg'
     *
     */
    public function convert($format="jpeg")
    {
        $this->uploader->image_convert = $format;
    }

    /**
     * Adds a watermark on the image
     *
     * $fileName is a local image filename, relative or absolute. GIF, JPG and PNG are supported, as well as PNG alpha.
     * $position sets the watermarkposition within the image
     *
     * Value of positions is one or two out of 'TBLR' (top, bottom, left, right)
     *
     * The positions are as following:   TL  T  TR
     *                                   L       R
     *                                   BL  B  BR
     *
     * Default value is "BL" (bottom left)
     *
     * Note that is $absoluteX and $absoluteY are used, $position has no effect
     *
     * $absoluteX sets the watermark absolute X position within the image
     *
     * Value is in pixels, representing the distance between the top of the image and the watermark
     * If a negative value is used, it will represent the distance between the bottom of the image and the watermark
     *
     * $absoluteY sets the twatermark absolute Y position within the image
     *
     * Value is in pixels, representing the distance between the left of the image and the watermark
     * If a negative value is used, it will represent the distance between the right of the image and the watermark    
     *
     */
    public function addWatermark($fileName=null, $position="BL", $absoluteX=null, $absoluteY=null)
    {
        if( !isset($fileName) ) {
            return;
        }

        $this->uploader->image_watermark = $fileName;
        $this->uploader->image_watermark_position = $position;
        $this->uploader->image_watermark_x = $absoluteX;
        $this->uploader->image_watermark_y = $absoluteY;
    }

    /**
     * $height sets the height of the reflection
     *
     * Value is an integer in pixels, or a string which format can be in pixels or percentage.
     * For instance, values can be : 40, '40', '40px' or '40%'
     *     
     * $space sets the space between the source image and its relection
     *
     * Value is an integer in pixels, which can be negative
     *     
     * $color sets the color of the reflection background.
     *
     * Value is an hexadecimal color, such as #FFFFFF
     *
     * $opacity sets the initial opacity of the reflection
     *
     * Value is an integer between 0 (no opacity) and 100 (full opacity).
     *     
     */
    public function addReflection($height="10%", $space=0, $color="#FFFFFF", $opacity=60)
    {
        if( intval($height) == 0 ) {
            return;
        }
        
        $this->uploader->image_reflection_height = $height;
        $this->uploader->image_reflection_space = $space;
        $this->uploader->image_reflection_color = $color;
        $this->uploader->image_reflection_opacity = $opacity;
    }

    /**
     * Adds a text label on the image
     *
     * Value is a string, any text. Text will not word-wrap, although you can use breaklines in your text "\n"
     */
    public function addText($string="")
    {
        if( trim($string) == "" ) {
            return;
        }

        $this->uploader->image_text = $string;
    }

    public function setTextDirection($direction)
    {
        $this->uploader->image_text_direction = $direction;
    }

    public function setTextColor($color)
    {
        $this->uploader->image_text_color = $color;
    }

    public function setTextVisibilityPercent($percent)
    {
        $this->uploader->image_text_percent = $visibilityPercent;
    }

    public function setTextBackgroundColor($color)
    {
        $this->uploader->image_text_background = $color;
    }

    public function setTextBackgroundVisPercent($percent)
    {
        $this->uploader->image_text_background_percent = $percent;
    }

    public function setTextFont($font)
    {
        $this->uploader->image_text_font = $font;
    }

    public function setTextPosition($position="TR")
    {
        $this->uploader->image_text_position = $position;
    }

    public function setTextAbsoluteX($absoluteX)
    {
        $this->uploader->image_text_x = $absoluteX;
    }

    public function setTextAbsoluteY($absoluteY)
    {
        $this->uploader->image_text_y = $absoluteY;
    }

    public function setTextPadding($padding)
    {
        $this->uploader->image_text_padding = $padding;
    }

    public function setTextPaddingX($padding)
    {
        $this->uploader->image_text_padding_x = $padding;
    }

    public function setTextPaddingY($padding)
    {
        $this->uploader->image_text_padding_y = $padding;
    }

    public function setTextAlignment($alignment)
    {
        $this->uploader->image_text_alignment = $alignment;
    }

    public function setTextLineSpacing($lineSpacing)
    {
        $this->uploader->image_text_line_spacing = $lineSpacing;
    }

    /**
     * Turns the image into greyscale
     */
    public function convertToGreyscale()
    {
        $this->uploader->image_greyscale = true;
    }

    /**
     * Inverts the color of an image
     *
     */
    public function colorInvert()
    {
        $this->uploader->image_negative = true;
    }

    /**
     * Applies a colored overlay on the image
     *
     * $color value is an hexadecimal color, such as #FFFFFF
     *
     * $percent value is a percentage, as an integer between 0 and 100
     *
     */
    public function colorOverlay($color="#FFFFFF", $percent=50)
    {
        $this->uploader->image_overlay_color = $color;
        $this->uploader->image_overlay_percent = $percent;
    }

    /**
     * Corrects the image contrast
     *
     * Value can range between -127 and 127
     *
     */
    public function setContrast($value=0)
    {
        $this->uploader->image_contrast = $value;
    }

    /**
     * Corrects the image brightness
     *
     * Value can range between -127 and 127
     *
     */
    public function setBrightness($value=0)
    {
        $this->uploader->image_brightness = $value;
    }

    /**
     * Quality of JPEG created/converted destination image
     *
     * Default value is 85
     *
     */
    public function setJpegQuality($value=85)
    {
        $this->uploader->jpeg_quality = $value;
        #
    }

    /**
     * Default color of the image background
     *
     * Is generally used when cropping an image with negative margins
     *
     */
    public function setBgColor($color="#000000")
    {
        $this->uploader->image_background_color = $color;
    }

    function __destruct()
    {
        $this->uploader->Clean();
    }
}
 
// ft:php
// fileformat:unix
// tabstop:4
?>
