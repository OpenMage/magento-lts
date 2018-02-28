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
 * Android preview model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Preview_Android extends Mage_XmlConnect_Model_Preview_Abstract
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
     * @return Mage_XmlConnect_Model_Preview_Android
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
        $bannerImages = $this->getImageModel()
            ->getDeviceImagesByType(Mage_XmlConnect_Model_Device_Android::IMAGE_TYPE_PORTRAIT_BANNER);
        if (!empty($bannerImages)) {
            $width  = Mage_XmlConnect_Model_Device_Android::PREVIEW_BANNER_WIDTH;
            $height = Mage_XmlConnect_Model_Device_Android::PREVIEW_BANNER_HEIGHT;
            foreach ($bannerImages as $banner) {
                if (!isset($banner['image_file'])) {
                    continue;
                }
                $result[] = $this->getImageModel()->getCustomSizeImageUrl($banner['image_file'], $width, $height);
            }
        }
        return $result;
    }

    /**
     * We doesn't support background images for android
     *
     * @return false
     */
    public function getBackgroundImage()
    {
        return false;
    }
}
