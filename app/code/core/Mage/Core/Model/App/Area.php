<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Application area model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_App_Area
{
    public const AREA_GLOBAL   = 'global';

    public const AREA_FRONTEND = 'frontend';

    public const AREA_ADMIN    = 'admin';

    public const AREA_ADMINHTML = 'adminhtml';

    public const PART_CONFIG   = 'config';

    public const PART_EVENTS   = 'events';

    public const PART_TRANSLATE = 'translate';

    public const PART_DESIGN   = 'design';

    /**
     * Array of area loaded parts
     *
     * @var array
     */
    protected $_loadedParts;

    /**
     * Area code
     *
     * @var string
     */
    protected $_code;

    /**
     * Area application
     *
     * @var Mage_Core_Model_App
     */
    protected $_application;

    /**
     * Mage_Core_Model_App_Area constructor.
     * @param string              $areaCode
     * @param Mage_Core_Model_App $application
     */
    public function __construct($areaCode, $application)
    {
        $this->_code = $areaCode;
        $this->_application = $application;
    }

    /**
     * Retrieve area application
     *
     * @return Mage_Core_Model_App
     */
    public function getApplication()
    {
        return $this->_application;
    }

    /**
     * Load area data
     *
     * @param  null|string              $part
     * @return Mage_Core_Model_App_Area
     */
    public function load($part = null)
    {
        if (is_null($part)) {
            $this->_loadPart(self::PART_CONFIG)
                ->_loadPart(self::PART_EVENTS)
                ->_loadPart(self::PART_DESIGN)
                ->_loadPart(self::PART_TRANSLATE);
        } else {
            $this->_loadPart($part);
        }

        return $this;
    }

    /**
     * Loading part of area
     *
     * @param  string                   $part
     * @return Mage_Core_Model_App_Area
     */
    protected function _loadPart($part)
    {
        if (isset($this->_loadedParts[$part])) {
            return $this;
        }

        Varien_Profiler::start('mage::dispatch::controller::action::predispatch::load_area::' . $this->_code . '::' . $part);
        switch ($part) {
            case self::PART_CONFIG:
                $this->_initConfig();
                break;
            case self::PART_EVENTS:
                $this->_initEvents();
                break;
            case self::PART_TRANSLATE:
                $this->_initTranslate();
                break;
            case self::PART_DESIGN:
                $this->_initDesign();
                break;
        }

        $this->_loadedParts[$part] = true;
        Varien_Profiler::stop('mage::dispatch::controller::action::predispatch::load_area::' . $this->_code . '::' . $part);
        return $this;
    }

    protected function _initConfig() {}

    /**
     * @return $this
     */
    protected function _initEvents()
    {
        Mage::app()->addEventArea($this->_code);
        #Mage::app()->getConfig()->loadEventObservers($this->_code);
        return $this;
    }

    /**
     * @return $this
     */
    protected function _initTranslate()
    {
        Mage::app()->getTranslator()->init($this->_code);
        return $this;
    }

    /**
     * @return $this|void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _initDesign()
    {
        if (Mage::app()->getRequest()->isStraight()) {
            return $this;
        }

        $designPackage = Mage::getSingleton('core/design_package');
        if ($designPackage->getArea() != self::AREA_FRONTEND) {
            return;
        }

        $currentStore = Mage::app()->getStore()->getStoreId();

        $designChange = Mage::getSingleton('core/design')
            ->loadChange($currentStore);

        if ($designChange->getData()) {
            $designPackage->setPackageName($designChange->getPackage())
                ->setTheme($designChange->getTheme());
        }
    }
}
