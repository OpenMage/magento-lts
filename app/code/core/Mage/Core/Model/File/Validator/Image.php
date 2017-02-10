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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    protected $_allowedImageTypes = array(
        IMAGETYPE_JPEG,
        IMAGETYPE_GIF,
        IMAGETYPE_JPEG2000,
        IMAGETYPE_PNG,
        IMAGETYPE_ICO,
        IMAGETYPE_TIFF_II,
        IMAGETYPE_TIFF_MM
    );

    /**
     * Setter for allowed image types
     *
     * @param array $imageFileExtensions
     * @return $this
     */
    public function setAllowedImageTypes(array $imageFileExtensions = array())
    {
        $map = array(
            'tif' => array(IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM),
            'tiff' => array(IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM),
            'jpg' => array(IMAGETYPE_JPEG, IMAGETYPE_JPEG2000),
            'jpe' => array(IMAGETYPE_JPEG, IMAGETYPE_JPEG2000),
            'jpeg' => array(IMAGETYPE_JPEG, IMAGETYPE_JPEG2000),
            'gif' => array(IMAGETYPE_GIF),
            'png' => array(IMAGETYPE_PNG),
            'ico' => array(IMAGETYPE_ICO),
            'apng' => array(IMAGETYPE_PNG)
        );

        $this->_allowedImageTypes = array();

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
        $fileInfo = getimagesize($filePath);
        if (is_array($fileInfo) and isset($fileInfo[2])) {
            if ($this->isImageType($fileInfo[2])) {
                return null;
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
