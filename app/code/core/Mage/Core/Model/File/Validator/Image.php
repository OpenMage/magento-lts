<?php

/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validator for check is uploaded file is image
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Validator_Image
{
    const NAME = "isImage";

    protected $_allowedImageTypes = [
        IMAGETYPE_JPEG,
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG2000,
        IMAGETYPE_PNG,
        IMAGETYPE_ICO,
        IMAGETYPE_TIFF_II,
        IMAGETYPE_TIFF_MM
    ];

    /**
     * Setter for allowed image types
     *
     * @param array $imageFileExtensions
     * @return $this
     */
    public function setAllowedImageTypes(array $imageFileExtensions = [])
    {
        $map = [
            'tif' => [IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM],
            'tiff' => [IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM],
            'jpg' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'jpe' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'jpeg' => [IMAGETYPE_JPEG, IMAGETYPE_JPEG2000],
            'gif' => [IMAGETYPE_GIF],
            'png' => [IMAGETYPE_PNG],
            'ico' => [IMAGETYPE_ICO],
            'apng' => [IMAGETYPE_PNG]
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
     * Validation callback for checking is file is image
     *
     * @param  string $filePath Path to temporary uploaded file
     * @return null
     * @throws Mage_Core_Exception
     */
    public function validate($filePath)
    {
        list($imageWidth, $imageHeight, $fileType) = getimagesize($filePath);
        if ($fileType) {
            if ($this->isImageType($fileType)) {
                /** if 'general/reprocess_images/active' false then skip image reprocessing. */
                if (!Mage::getStoreConfigFlag('general/reprocess_images/active')) {
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
                            imagejpeg($img, $filePath, 100);
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
