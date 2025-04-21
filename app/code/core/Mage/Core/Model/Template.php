<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Template model class
 *
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Template extends Mage_Core_Model_Abstract
{
    /**
     * Types of template
     */
    public const TYPE_TEXT = 1;
    public const TYPE_HTML = 2;

    /**
     * Default design area for emulation
     */
    public const DEFAULT_DESIGN_AREA = 'frontend';

    /**
     * Configuration of design package for template
     *
     * @var Varien_Object|null
     */
    protected $_designConfig;

    /**
     * Configuration of emulated design package.
     *
     * @var Varien_Object|false
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
     * @return $this
     */
    protected function _applyDesignConfig()
    {
        $designConfig = $this->getDesignConfig();
        $store = $designConfig->getStore();
        $storeId = is_object($store) ? $store->getId() : $store;
        $area = $designConfig->getArea();
        if (!is_null($storeId) && ($storeId != Mage::app()->getStore()->getId())) {
            $appEmulation = Mage::getSingleton('core/app_emulation');
            $this->_initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId, $area);
        }
        return $this;
    }

    /**
     * Revert design settings to previous
     *
     * @return $this
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
        if (is_null($this->_designConfig)) {
            $store = Mage::getDesign()->getStore();
            $storeId = is_object($store) ? $store->getId() : $store;
            $this->_designConfig = new Varien_Object([
                'area' => Mage::getDesign()->getArea(),
                'store' => $storeId,
            ]);
        }
        return $this->_designConfig;
    }

    /**
     * Initialize design information for template processing
     *
     * @return  $this
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
    public function emulateDesign($storeId, $area = self::DEFAULT_DESIGN_AREA)
    {
        if ($storeId) {
            // save current design settings
            $this->_emulatedDesignConfig = clone $this->getDesignConfig();
            if ($this->getDesignConfig()->getStore() != $storeId) {
                $this->setDesignConfig(['area' => $area, 'store' => $storeId]);
                $this->_applyDesignConfig();
            }
        } else {
            $this->_emulatedDesignConfig = false;
        }
    }

    /**
     * Revert to last design config, used before emulation
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
     * @return bool
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
