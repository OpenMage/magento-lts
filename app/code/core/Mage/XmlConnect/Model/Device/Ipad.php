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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect iPad device model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Device_Ipad extends Mage_XmlConnect_Model_Device_Abstract
{
    /**
     * Portrait banner image type
     */
    const IMAGE_TYPE_PORTRAIT_BANNER = 'ipad_portrait_banner';

    /**
     * Landscape banner image type
     */
    const IMAGE_TYPE_LANDSCAPE_BANNER = 'ipad_landscape_banner';

    /**
     * Portrait background image type
     */
    const IMAGE_TYPE_PORTRAIT_BACKGROUND = 'ipad_portrait_background';

    /**
     * Landscape background image type
     */
    const IMAGE_TYPE_LANDSCAPE_BACKGROUND = 'ipad_landscape_background';

    /**
     * Ipad landscape orientation identificator
     */
    const ORIENTATION_LANDSCAPE = 'landscape';

    /**
     * Ipad portrait orientation identificator
     */
    const ORIENTATION_PORTRAIT = 'portrait';

    /**
     * Ipad portrait preview banner widht
     */
    const PREVIEW_PORTRAIT_BANNER_WIDTH = 350;

    /**
     * Ipad portrait preview banner image height
     */
    const PREVIEW_PORTRAIT_BANNER_HEIGHT = 135;

    /**
     * Ipad landscape preview banner width
     */
    const PREVIEW_LANDSCAPE_BANNER_WIDTH = 467;

    /**
     * Ipad landscape preview banner image height
     */
    const PREVIEW_LANDSCAPE_BANNER_HEIGHT = 157;

    /**
     * Ipad landscape orientation preview image width
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_WIDTH = 467;

    /**
     * Ipad landscape orientation preview image height
     */
    const PREVIEW_LANDSCAPE_BACKGROUND_HEIGHT = 321;

    /**
     * Ipad portrait orientation preview image width
     */
    const PREVIEW_PORTRAIT_BACKGROUND_WIDTH = 350;

    /**
     * Ipad portrait orientation preview image height
     */
    const PREVIEW_PORTRAIT_BACKGROUND_HEIGHT = 438;

    /**
     * Ipad default scale used for calculation rate
     */
    const DEFAULT_SCALE = 768;

    /**
     * Ipad default screen size
     */
    const SCREEN_SIZE_DEFAULT   = '768x1024';

    /**
     * Device specific image size configuration
     *
     * @var array
     */
    protected $_imageSizeConfiguration = array(
        self::IMAGE_TYPE_ICON => array('width' => 35, 'height' => 35),
        self::IMAGE_TYPE_PORTRAIT_BANNER => array('width' => 768, 'height' => 294),
        self::IMAGE_TYPE_LANDSCAPE_BANNER => array('width' => 1024, 'height' => 344),
        self::IMAGE_TYPE_PORTRAIT_BACKGROUND => array('width' => 768, 'height' => 960),
        self::IMAGE_TYPE_LANDSCAPE_BACKGROUND => array('width' => 1024, 'height' => 704),
        'content' => array(
            'product_small' => 210,
            'product_big' => 400,
            'category' => 243,
            'product_gallery_big' => 1288,
        ),
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
     * Get iPad default screen size
     *
     * @return string
     */
    public function getDefaultScreenSize()
    {
        return self::SCREEN_SIZE_DEFAULT;
    }

    /**
     * Get iPad default scale
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
