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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ipad preview model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Preview_Ipad extends Mage_XmlConnect_Model_Preview_Abstract
{
    /**
     * Current device orientation
     *
     * @var string
     */
    protected $_orientation = 'unknown';

    /**
     * Set device orientation
     *
     * @param string $orientation
     * @return Mage_XmlConnect_Model_Preview_Ipad
     */
    public function setOrientation($orientation)
    {
        $this->_orientation = $orientation;
        return $this;
    }

    /**
     * Get current device orientation
     *
     * @return string
     */
    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * Get application banner image url
     *
     * @return string
     */
    public function getBannerImage()
    {
        $result = array();
        switch ($this->getOrientation()) {
            case Mage_XmlConnect_Model_Device_Ipad::ORIENTATION_LANDSCAPE:
                $bannerImages = $this->getImageModel()
                    ->getDeviceImagesByType(Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BANNER);
                if (!empty($bannerImages)) {
                    $width  = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_LANDSCAPE_BANNER_WIDTH;
                    $height = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_LANDSCAPE_BANNER_HEIGHT;
                    foreach ($bannerImages as $banner) {
                        if (!isset($banner['image_file'])) {
                            continue;
                        }
                        $result[] = $this->getImageModel()->getCustomSizeImageUrl(
                            $banner['image_file'], $width, $height
                        );
                    }
                }
                break;
            case Mage_XmlConnect_Model_Device_Ipad::ORIENTATION_PORTRAIT:
                $bannerImages = $this->getImageModel()
                    ->getDeviceImagesByType(Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BANNER);
                if (!empty($bannerImages)) {
                    $width  = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_PORTRAIT_BANNER_WIDTH;
                    $height = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_PORTRAIT_BANNER_HEIGHT;
                    foreach ($bannerImages as $banner) {
                        if (!isset($banner['image_file'])) {
                            continue;
                        }
                        $result[] = $this->getImageModel()->getCustomSizeImageUrl(
                            $banner['image_file'], $width, $height
                        );
                    }
                }
                break;
        }
        return $result;
    }

    /**
     * Get background image url according orientation
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getBackgroundImage()
    {
        switch ($this->getOrientation()) {
            case Mage_XmlConnect_Model_Device_Ipad::ORIENTATION_LANDSCAPE:
                $backgroundImage = $this->getImageModel()
                    ->getImageItemByType(Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BACKGROUND);
                if (is_object($backgroundImage) && $backgroundImage->getImageFile()) {
                    $width = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_LANDSCAPE_BACKGROUND_WIDTH;
                    $height = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT;
                    $backgroundImage = $this->getImageModel()
                        ->getCustomSizeImageUrl($backgroundImage->getImageFile(), $width, $height);
                } else {
                    $backgroundImage = $this->getPreviewImagesUrl('ipad/background_home_landscape.jpg');
                }
                break;
            case Mage_XmlConnect_Model_Device_Ipad::ORIENTATION_PORTRAIT:
                $backgroundImage = $this->getImageModel()
                    ->getImageItemByType(Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BACKGROUND);
                if (is_object($backgroundImage) && $backgroundImage->getImageFile()) {
                    $width = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_PORTRAIT_BACKGROUND_WIDTH;
                    $height = Mage_XmlConnect_Model_Device_Ipad::PREVIEW_PORTRAIT_BACKGROUND_HEIGHT;
                    $backgroundImage = $this->getImageModel()
                        ->getCustomSizeImageUrl($backgroundImage->getImageFile(), $width, $height);
                } else {
                    $backgroundImage = $this->getPreviewImagesUrl('ipad/background_portrait.jpg');
                }
                break;
            default:
                Mage::throwException(
                    Mage::helper('xmlconnect')->__('Wrong Ipad background image orientation has been specified: "%s".', $this->getOrientation())
                );
                break;
        }
        return $backgroundImage;
    }
}
