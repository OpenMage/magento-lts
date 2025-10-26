<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Validator for check is uploaded file is image
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_File_Validator_Image
{
    public const NAME = 'isImage';

    protected $_allowedImageTypes = [
        IMAGETYPE_WEBP,
        IMAGETYPE_JPEG,
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG2000,
        IMAGETYPE_PNG,
        IMAGETYPE_ICO,
        IMAGETYPE_TIFF_II,
        IMAGETYPE_TIFF_MM,
    ];

    /**
     * Setter for allowed image types
     *
     * @return $this
     */
    public function setAllowedImageTypes(array $imageFileExtensions = [])
    {
        $map = [
            'webp' => [IMAGETYPE_WEBP],
            'tif' => [IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM],
            'tiff' => [IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM],
            'jpg' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'jpe' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'jpeg' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'gif' => [IMAGETYPE_GIF],
            'png' => [IMAGETYPE_PNG],
            'ico' => [IMAGETYPE_ICO],
            'apng' => [IMAGETYPE_PNG],
        ];

        $this->_allowedImageTypes = [];

        foreach ($imageFileExtensions as $extension) {
            if (isset($map[$extension])) {
                foreach ($map[$extension] as $imageType) {
                    $this->_allowedImageTypes[$imageType] = $imageType;
                }
            }
        }

        return $this;
    }

    /**
     * Validation callback for checking if file is image
     * Destroy malicious code in image by reprocessing
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return null
     * @throws Mage_Core_Exception
     */
    public function validate($filePath)
    {
        [$imageWidth, $imageHeight, $fileType] = getimagesize($filePath);
        if ($fileType) {
            if ($fileType === IMAGETYPE_ICO) {
                return null;
            }

            if ($this->isImageType($fileType)) {
                // Config 'general/reprocess_images/active' is deprecated, replacement is the following:
                $imageQuality = Mage::getStoreConfig('admin/security/reprocess_image_quality');
                if ($imageQuality != '') {
                    $imageQuality = (int) $imageQuality;
                } else {
                    // Value not set in backend. For BC, if depcrecated config does not exist, default to 85.
                    $imageQuality = Mage::getStoreConfig('general/reprocess_images/active') === null
                        ? 85
                        : (Mage::getStoreConfigFlag('general/reprocess_images/active') ? 85 : 0);
                }

                if ($imageQuality === 0) {
                    return null;
                }

                //replace tmp image with re-sampled copy to exclude images with malicious data
                $image = imagecreatefromstring(file_get_contents($filePath));
                if ($image !== false) {
                    $img = imagecreatetruecolor($imageWidth, $imageHeight);
                    imagealphablending($img, false);
                    imagecopyresampled($img, $image, 0, 0, 0, 0, $imageWidth, $imageHeight, $imageWidth, $imageHeight);
                    imagesavealpha($img, true);

                    switch ($fileType) {
                        case IMAGETYPE_GIF:
                            $transparencyIndex = imagecolortransparent($image);
                            if ($transparencyIndex >= 0) {
                                imagecolortransparent($img, $transparencyIndex);
                                for ($y = 0; $y < $imageHeight; ++$y) {
                                    for ($x = 0; $x < $imageWidth; ++$x) {
                                        if (((imagecolorat($img, $x, $y) >> 24) & 0x7F)) {
                                            imagesetpixel($img, $x, $y, $transparencyIndex);
                                        }
                                    }
                                }
                            }

                            if (!imageistruecolor($image)) {
                                imagetruecolortopalette($img, false, imagecolorstotal($image));
                            }

                            imagegif($img, $filePath);
                            break;
                        case IMAGETYPE_JPEG:
                            imagejpeg($img, $filePath, $imageQuality);
                            break;
                        case IMAGETYPE_WEBP:
                            imagewebp($img, $filePath, $imageQuality);
                            break;
                        case IMAGETYPE_PNG:
                            imagepng($img, $filePath);
                            break;
                        default:
                            break;
                    }

                    imagedestroy($img);
                    imagedestroy($image);
                    return null;
                } else {
                    throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid image.'));
                }
            }
        }

        throw Mage::exception('Mage_Core', Mage::helper('core')->__('Invalid MIME type.'));
    }

    /**
     * Returns is image by image type
     * @param int $nImageType
     * @return bool
     */
    protected function isImageType($nImageType)
    {
        return in_array($nImageType, $this->_allowedImageTypes);
    }
}
