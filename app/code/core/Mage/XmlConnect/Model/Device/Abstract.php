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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Abstract device model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Model_Device_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Icon image type
     */
    const IMAGE_TYPE_ICON = 'icon';

    /**
     * Icon image type count
     */
    const IMAGE_TYPE_ICON_COUNT = 1;

    /**
     * Banner image type count
     */
    const IMAGE_TYPE_BANNER_COUNT = 5;

    /**
     * Background image count
     */
    const IMAGE_TYPE_BACKGROUND_COUNT = 1;

    /**
     * Application model
     *
     * @var Mage_XmlConnect_Model_Application
     */
    protected $_applicationModel;

    /**
     * Image size configuration that is the same for all devices
     *
     * @var array
     */
    protected $_defaultSizeConfiguration = array(
        'content' => array(
            'product_small'         => 70,
            'product_big'           => 130,
            'category'              => 80,
            'product_gallery_small' => 40
        ),
        'tabBar' => array(
            'home' => array('icon' => array('width' => 35, 'height' => 35)),
            'shop' => array('icon' => array('width' => 35, 'height' => 35)),
            'search' => array('icon' => array('width' => 35, 'height' => 35)),
            'cart' => array('icon' => array('width' => 35, 'height' => 35)),
            'more' => array('icon' => array('width' => 35, 'height' => 35))
        )
    );

    /**
     * Initialize model
     *
     * @param null|Mage_XmlConnect_Model_Application $applicationModel
     */
    public function __construct($applicationModel = null)
    {
        $this->_setApplicationModel($applicationModel);
    }

    /**
     * Return image size configuration for device
     *
     * @return array
     */
    abstract protected function _getImageSizeConfiguration();

    /**
     * Get application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _getApplicationModel()
    {
        if (null !== $this->_applicationModel) {
            $this->setApplicationModel(Mage::helper('xmlconnect')->getApplication());
        }
        return $this->_applicationModel;
    }

    /**
     * Set application model
     *
     * @param null|Mage_XmlConnect_Model_Application $applicationModel
     * @return Mage_XmlConnect_Model_Device_Abstract
     */
    protected function _setApplicationModel($applicationModel = null)
    {
        if ($applicationModel instanceof Mage_XmlConnect_Model_Application) {
            $this->_applicationModel = $applicationModel;
        } else {
            $this->_applicationModel = Mage::helper('xmlconnect')->getApplication();
        }
        return $this;
    }

    /**
     * Return default configuration
     *
     * @return array
     */
    protected function _getDefaultSizeConfiguration()
    {
        return $this->_defaultSizeConfiguration;
    }

    /**
     * Get image size configuration for current device
     *
     * @return array
     */
    public function getImageSizeConfig()
    {
        if (function_exists('array_replace_recursive')) {
            return array_replace_recursive($this->_getDefaultSizeConfiguration(), $this->_getImageSizeConfiguration());
        } else {
            return $this->_arrayReplaceRecursive(
                $this->_getDefaultSizeConfiguration(), $this->_getImageSizeConfiguration()
            );
        }
    }

    /**
     * Replaces elements from passed arrays into the first array recursively
     * Analog of array_replace_recursive()
     *
     * @param array $base
     * @param array $replacements
     * @return array
     */
    protected function _arrayReplaceRecursive(array $base, array $replacements)
    {
        foreach ($replacements as $key => $value) {
            if (!isset($base[$key]) || (isset($base[$key]) && !is_array($base[$key]))) {
                $base[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
                $value = $this->_arrayReplaceRecursive($base[$key], $value);
            }
            $base[$key] = $value;
        }
        return $base;
    }
}
