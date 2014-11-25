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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Android device model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Device_Android extends Mage_XmlConnect_Model_Device_Abstract
{
    /**
     * Banner image type
     */
    const IMAGE_TYPE_PORTRAIT_BANNER = 'android_portrait_banner';

    /**
     * Android default screen size
     */
    const SCREEN_SIZE_DEFAULT   = '480x800';

    /**
     * Android default scale used for calculation rate
     */
    const DEFAULT_SCALE = 480;

    /**
     * Android preview banner width
     */
    const PREVIEW_BANNER_WIDTH = 320;

    /**
     * Android preview banner image height
     */
    const PREVIEW_BANNER_HEIGHT = 258;

    /**
     * Device specific image size configuration
     *
     * @var array
     */
    protected $_imageSizeConfiguration = array(
        self::IMAGE_TYPE_ICON => array('width' => 53, 'height' => 53),
        self::IMAGE_TYPE_PORTRAIT_BANNER => array('width' => 480, 'height' => 387),
        'content' => array('product_gallery_big' => 840),
    );

    /**
     * Initialize model
     *
     * @param null|Mage_XmlConnect_Model_Application $applicationModel
     */
    public function __construct($applicationModel = null)
    {
        parent::__construct($applicationModel);
    }

    /**
     * Get Android default screen size
     *
     * @return string
     */
    public function getDefaultScreenSize()
    {
        return self::SCREEN_SIZE_DEFAULT;
    }

    /**
     * Get Android default scale
     *
     * @return int
     */
    public function getDefaultScaleValue()
    {
        return self::DEFAULT_SCALE;
    }

    /**
     * Return image size configuration for device
     *
     * @return array
     */
    protected function _getImageSizeConfiguration()
    {
        return $this->_imageSizeConfiguration;
    }
}
