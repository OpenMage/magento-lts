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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Template model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Template extends Mage_Core_Model_Abstract
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;

    /**
     * Default design area for emulation
     */
    const DEFAULT_DESIGN_AREA = 'frontend';

    /**
     * Configuration of desing package for template
     *
     * @var Varien_Object
     */
    protected $_designConfig;

            
    /**
     * Configuration of emulated desing package.
     *
     * @var Varien_Object|boolean
     */
    protected $_emulatedDesignConfig = false;

    /**
     * Initial environment information
     * @see self::_applyDesignConfig()
     *
     * @var Varien_Object|null
     */
    protected $_initialEnvironmentInfo = null;

    /**
     * Applying of design config
     *
     * @return Mage_Core_Model_Template
     */
    protected function _applyDesignConfig()
    {
        $designConfig = $this->getDesignConfig();
        $store = $designConfig->getStore();
        $storeId = is_object($store) ? $store->getId() : $store;
        $area = $designConfig->getArea();
        if (!is_null($storeId)) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $this->_initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, $area);
        }
        return $this;
    }

    /**
     * Revert design settings to previous
     *
     * @return Mage_Core_Model_Template
     */
    protected function _cancelDesignConfig()
    {
        if (!empty($this->_initialEnvironmentInfo)) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $appEmulation->stopEnvironmentEmulation($this->_initialEnvironmentInfo);
            $this->_initialEnvironmentInfo = null;
        }
        return $this;
    }

    /**
     * Get design configuration data
     *
     * @return Varien_Object
     */
    protected function getDesignConfig()
    {
        if(is_null($this->_designConfig)) {
            $store = Mage::getDesign()->getStore();
            $storeId = is_object($store) ? $store->getId() : $store;
            $this->_designConfig = new Varien_Object(array(
                'area' => Mage::getDesign()->getArea(),
                'store' => $storeId
            ));
        }
        return $this->_designConfig;
    }

    /**
     * Initialize design information for template processing
     *
     * @param   array $config
     * @return  Mage_Core_Model_Template
     */
    public function setDesignConfig(array $config)
    {
        $this->getDesignConfig()->setData($config);
        return $this;
    }

    /**
     * Save current design config and replace with design config from specified store
     * Event is not dispatched.
     *
     * @param int|string $storeId
     * @param string $area
     */
    public function emulateDesign($storeId, $area=self::DEFAULT_DESIGN_AREA)
    {
        if ($storeId) {
            // save current design settings
            $this->_emulatedDesignConfig = clone $this->getDesignConfig();
            if ($this->getDesignConfig()->getStore() != $storeId) {
                $this->setDesignConfig(array('area' => $area, 'store' => $storeId));
                $this->_applyDesignConfig();
            }
        } else {
            $this->_emulatedDesignConfig = false;
        }
    }

    /**
     * Revert to last design config, used before emulation
     *
     */
    public function revertDesign()
    {
        if ($this->_emulatedDesignConfig) {
            $this->setDesignConfig($this->_emulatedDesignConfig->getData());
            $this->_cancelDesignConfig();
            $this->_emulatedDesignConfig = false;
        }
    }

    /**
     * Return true if template type eq text
     *
     * @return boolean
     */
    public function isPlain()
    {
        return $this->getType() == self::TYPE_TEXT;
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    abstract public function getType();
}
