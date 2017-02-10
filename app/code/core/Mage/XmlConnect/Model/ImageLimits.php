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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Image Limits model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_ImageLimits extends Mage_Core_Model_Abstract
{
    /**
     * Screen size update type glue
     */
    const SCREEN_SIZE_UPDATE_TYPE_GLUE = '_';

    /**
     * Screen size glue
     */
    const SCREEN_SIZE_GLUE = 'x';

    /**
     * Maximum allowed screen sizes
     */
    const MAX_ALLOWED_SCREEN_SIZES = 2560;

    /**
     * Current screen size
     *
     * @var string
     */
    protected $_screenSize;

    /**
     * Current screen rate
     *
     * @var float
     */
    protected $_screenRate;

    /**
     * Update type
     *
     * @var string
     */
    protected $_updateType;

    /**
     * Screen size image limits array
     *
     * @var array
     */
    protected $_imageLimits = array();

    /**
     * Screen size config update model
     *
     * @var Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    protected $_sizeModel;

    /**
     * Application model
     *
     * @var Mage_XmlConnect_Model_Application
     */
    protected $_applicationModel;

    /**
     * Initialize model
     *
     * @param null|Mage_XmlConnect_Model_Application $applicationModel
     */
    public function __construct($applicationModel = null)
    {
        parent::_construct();
        $this->_setApplicationModel($applicationModel)->_setScreenSize()->_initSizeModel();
    }

    /**
     * Set application model
     *
     * @param Mage_XmlConnect_Model_Application $applicationModel
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
     * Get application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _getApplicationModel()
    {
        if (null === $this->_applicationModel) {
            $this->setApplicationModel(Mage::helper('xmlconnect')->getApplication());
        }
        return $this->_applicationModel;
    }

    /**
     * Do steps to set current screen size and image limits configuration
     *
     * @param string $screenSize
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _doUpdateConfig($screenSize = '')
    {
        $this->_setScreenSize($screenSize)->_initSizeModel()->_afterCalculate();
        return $this;
    }

    /**
     * Set screen current screen siz
     *
     * @param string $screenSize
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _setScreenSize($screenSize = '')
    {
        /** @var $applicationModel Mage_XmlConnect_Model_Application */
        $applicationModel = Mage::helper('xmlconnect')->getApplication();
        if (!$screenSize) {
            $screenSize = $applicationModel->getScreenSize();
        }

        if (isset($this->_imageLimits[$screenSize])) {
            return $this;
        }

        $screenSizeExplodeArray = explode(self::SCREEN_SIZE_UPDATE_TYPE_GLUE, $screenSize);
        switch (count($screenSizeExplodeArray)) {
            case 2:
                $this->_updateType = $screenSizeExplodeArray[1];
            case 1:
                $this->_screenSize = $screenSizeExplodeArray[0];
                break;
            default:
                $this->_screenSize = $applicationModel->getScreenSize();
                break;
        }
        $this->_checkMaximumAllowedSize();
        return $this;
    }

    /**
     * Check maximum allowed screen size for devices
     *
     * @throws Mage_Core_Exception
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _checkMaximumAllowedSize()
    {
        $screenSizeArray = explode(self::SCREEN_SIZE_GLUE, $this->_screenSize);
        if (count($screenSizeArray) != 2 || $screenSizeArray[0] > self::MAX_ALLOWED_SCREEN_SIZES
            || $screenSizeArray[1] > self::MAX_ALLOWED_SCREEN_SIZES
        ) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Screen resolution is not supported'));
        }
        return $this;
    }

    /**
     * Init image limit configuration model
     *
     * @throws Mage_Core_Exception
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _initSizeModel()
    {
        $defaultScreenSize = $this->_getApplicationModel()->getDeviceModel()->getDefaultScreenSize();

        if ($this->_screenSize && $this->_screenSize != $defaultScreenSize) {
            if (!$this->getSizeModel()) {
                $modelClass = Mage::getConfig()->getModelClassName('xmlconnect/imageLimits_' . $this->_screenSize);
                if (class_exists($modelClass, false) || mageFindClassFile($modelClass)) {
                    $sizeModel = Mage::getModel('xmlconnect/imageLimits_' . $this->_screenSize, $this->_updateType);
                    $this->setSizeModel($sizeModel);
                }
            }
            $this->_calculateImageLimits()->_afterCalculate();
        } else {
            $this->_setScreenSize($defaultScreenSize)->_setDefaultSizeModel();
        }
        return $this;
    }

    /**
     * Set default image size model
     *
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _setDefaultSizeModel()
    {
        $defaultSizeModel = $this->_getApplicationModel()->getDeviceModel();
        $this->_imageLimits[$this->_screenSize] = $defaultSizeModel->getImageSizeConfig();
        return $this;
    }

    /**
     * After calculate image limits screen size update
     *
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected  function _afterCalculate()
    {
        if (isset($this->_imageLimits[$this->_screenSize])) {
            return $this;
        }
        $this->_imageLimits[$this->_screenSize] = $this->getSizeModel()->getScreenSizeConfig();
        return $this;
    }

    /**
     * Calculate image limits for current screen size
     *
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected  function _calculateImageLimits()
    {
        $defaultSizeModel = $this->_getApplicationModel()->getDeviceModel();
        $sizeModel = $this->getSizeModel();

        $updatedConfiguration = $this->_calculateImageRate()->_updateConfigByRate(
            $defaultSizeModel->getImageSizeConfig()
        );

        if (!$sizeModel) {
            $this->_imageLimits[$this->_screenSize] = $updatedConfiguration;
        } else {
            $sizeModel->setConfig($updatedConfiguration);
        }
        return $this;
    }

    /**
     * Calculate image rate
     *
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    protected function _calculateImageRate()
    {
        $defaultScaleValue = $this->_getApplicationModel()->getDeviceModel()->getDefaultScaleValue();

        list($width, $height) = explode(self::SCREEN_SIZE_GLUE, $this->_screenSize);
        // Calculate rate for current resolution
        $this->_screenRate = min($width, $height) / $defaultScaleValue;

        return $this;
    }

    /**
     * Update image limits based on device default screen size
     *
     * @param array $defaultValues
     * @return array
     */
    protected function _updateConfigByRate($defaultValues)
    {
        foreach ($defaultValues as $key => $value) {
            if (is_array($value)) {
                $defaultValues[$key] = $this->_updateConfigByRate($value);
            } else {
                $defaultValues[$key] = round($value * $this->_screenRate);
            }
        }
        return $defaultValues;
    }

    /**
     * Get screen size config update model
     *
     * @return Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    public function getSizeModel()
    {
        return $this->_sizeModel;
    }

    /**
     * Set screen size config update model
     *
     * @param object $sizeModel
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    public function setSizeModel($sizeModel)
    {
        $this->_sizeModel = $sizeModel;
        return $this;
    }

    /**
     * Get image limits config
     *
     * @return array
     */
    public function getImageLimits()
    {
        return $this->_imageLimits;
    }

    /**
     * Set image limits config
     *
     * @param array $imageLimits
     * @return Mage_XmlConnect_Model_ImageLimits
     */
    public function setImageLimits($imageLimits)
    {
        $this->_imageLimits = $imageLimits;
        return $this;
    }

    /**
     * Get image limits by type
     *
     * @param string $key
     * @param null|string $index
     * @param string $screenSize
     * @return mixed
     */
    public function getImageLimitsByType($key = '', $index = null, $screenSize = '')
    {
        if ($screenSize && !isset($this->_imageLimits[$screenSize])) {
            $this->_doUpdateConfig($screenSize);
        }

        $this->setData($this->_imageLimits[$this->_screenSize]);
        $result = $this->getData($key, $index);
        $this->unsetData();

        return $result;
    }
}
