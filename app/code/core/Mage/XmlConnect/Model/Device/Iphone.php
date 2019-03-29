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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect iPhone device model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Device_Iphone extends Mage_XmlConnect_Model_Device_Abstract
{
    /**
     * Portrait banner image type
     */
    const IMAGE_TYPE_PORTRAIT_BANNER = 'iphone_portrait_banner';

    /**
     * Portrait background image type
     */
    const IMAGE_TYPE_PORTRAIT_BACKGROUND = 'iphone_portrait_background';

    /**
     * iPhone default scale used for calculation rate
     */
    const DEFAULT_SCALE = 320;

    /**
     * iPhone default screen size
     */
    const SCREEN_SIZE_DEFAULT   = '320x480';

    /**
     * Portrait preview banner width
     */
    const PREVIEW_PORTRAIT_BANNER_WIDTH = '320';

    /**
     * Portrait preview banner height
     */
    const PREVIEW_PORTRAIT_BANNER_HEIGHT = '230';

    /**
     * Device specific image size configuration
     *
     * @var array
     */
    protected $_imageSizeConfiguration = array(
        self::IMAGE_TYPE_ICON => array('width' => 35, 'height' => 35),
        self::IMAGE_TYPE_PORTRAIT_BANNER => array('width' => 320, 'height' => 230),
        self::IMAGE_TYPE_PORTRAIT_BACKGROUND => array('width' => 320, 'height' => 367),
        'content' => array('product_gallery_big' => 560),
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
     * Get iPhone default screen size
     *
     * @return string
     */
    public function getDefaultScreenSize()
    {
        return self::SCREEN_SIZE_DEFAULT;
    }

    /**
     * Get iPhone default scale
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
