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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application area nodel
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_App_Area
{
    const AREA_GLOBAL   = 'global';
    const AREA_FRONTEND = 'frontend';
    const AREA_ADMIN    = 'admin';

    const PART_CONFIG   = 'config';
    const PART_EVENTS   = 'events';
    const PART_TRANSLATE= 'translate';
    const PART_DESIGN   = 'design';

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
     * @param   string|null $part
     * @return  Mage_Core_Model_App_Area
     */
    public function load($part=null)
    {
        if (is_null($part)) {
            $this->_loadPart(self::PART_CONFIG)
                ->_loadPart(self::PART_EVENTS)
                ->_loadPart(self::PART_DESIGN)
                ->_loadPart(self::PART_TRANSLATE);
        }
        else {
            $this->_loadPart($part);
        }
        return $this;
    }

    /**
     * Loading part of area
     *
     * @param   string $part
     * @return  Mage_Core_Model_App_Area
     */
    protected function _loadPart($part)
    {
        if (isset($this->_loadedParts[$part])) {
            return $this;
        }
        Varien_Profiler::start('mage::dispatch::controller::action::predispatch::load_area::'.$this->_code.'::'.$part);
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
        Varien_Profiler::stop('mage::dispatch::controller::action::predispatch::load_area::'.$this->_code.'::'.$part);
        return $this;
    }

    protected function _initConfig()
    {

    }

    protected function _initEvents()
    {
        Mage::app()->addEventArea($this->_code);
        #Mage::app()->getConfig()->loadEventObservers($this->_code);
        return $this;
    }

    protected function _initTranslate()
    {
        Mage::app()->getTranslator()->init($this->_code);
        return $this;
    }

    protected function _initDesign()
    {
        if (Mage::app()->getRequest()->isStraight()) {
            return $this;
        }
        $designPackage = Mage::getSingleton('core/design_package');
        if ($designPackage->getArea() != self::AREA_FRONTEND)
            return;

        $currentStore = Mage::app()->getStore()->getStoreId();

        $designChange = Mage::getSingleton('core/design')
            ->loadChange($currentStore);

        if ($designChange->getData()) {
            $designPackage->setPackageName($designChange->getPackage())
                ->setTheme($designChange->getTheme());
        }
    }
}
