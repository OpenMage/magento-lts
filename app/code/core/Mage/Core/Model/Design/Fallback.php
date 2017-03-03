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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Configuration for Design model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Design_Fallback
{
    /**
     * @var Mage_Core_Model_Design_Config
     */
    protected $_config = null;

    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store = null;

    /**
     * Use for caching fallback schemes
     *
     * @var array
     */
    protected $_cachedSchemes = array();

    /**
     * Used to find circular dependencies
     *
     * @var array
     */
    protected $_visited;

    /**
     * Constructor
     */
    public function __construct(array $params = array())
    {
        $this->_config = isset($params['config']) ? $params['config'] : Mage::getModel('core/design_config');
    }

    /**
     * Retrieve store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->_store === null) {
            return Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * @param $store string|integer|Mage_Core_Model_Store
     * @return $this
     */
    public function setStore($store)
    {
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }
        $this->_store = $store;
        $this->_cachedSchemes = array();
        return $this;
    }

    /**
     * Get fallback scheme
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @return array
     */
    public function getFallbackScheme($area, $package, $theme)
    {
        $cacheKey = $area . '/' . $package . '/' . $theme;

        if (!isset($this->_cachedSchemes[$cacheKey])) {

            if ($this->_isInheritanceDefined($area, $package, $theme)) {
                $scheme = $this->_getFallbackScheme($area, $package, $theme);
            } else {
                $scheme = $this->_getLegacyFallbackScheme();
            }

            $this->_cachedSchemes[$cacheKey] = $scheme;
        }

        return $this->_cachedSchemes[$cacheKey];
    }

    /**
     * Check if inheritance defined in theme config
     *
     * @param $area
     * @param $package
     * @param $theme
     * @return bool
     */
    protected function _isInheritanceDefined($area, $package, $theme)
    {
        $path = $area . '/' . $package . '/' . $theme . '/parent';
        return $this->_config->getNode($path) !== false;
    }

    /**
     * Get fallback scheme according to theme config
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _getFallbackScheme($area, $package, $theme)
    {
        $scheme = array(array());
        $this->_visited = array();
        while ($parent = (string)$this->_config->getNode($area . '/' . $package . '/' . $theme . '/parent')) {

            $this->_checkVisited($area, $package, $theme);

            $parts = explode('/', $parent);
            if (count($parts) !== 2) {
                throw new Mage_Core_Exception('Parent node should be defined as "package/theme"');
            }
            list($package, $theme) = $parts;
            $scheme[] = array('_package' => $package, '_theme' => $theme);
        }

        return $scheme;
    }

    /**
     * Prevent circular inheritance
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @throws Mage_Core_Exception
     * @return array
     */
    protected function _checkVisited($area, $package, $theme)
    {
        $path = $area . '/' . $package . '/' . $theme;
        if (in_array($path, $this->_visited)) {
            throw new Mage_Core_Exception(
                'Circular inheritance in theme ' . $package . '/' . $theme
            );
        }
        $this->_visited[] = $path;
    }

    /**
     * Get fallback scheme when inheritance is not defined (backward compatibility)
     *
     * @return array
     */
    protected function _getLegacyFallbackScheme()
    {
        return array(
            array(),
            array('_theme' => $this->_getFallbackTheme()),
            array('_theme' => Mage_Core_Model_Design_Package::DEFAULT_THEME),
        );
    }

    /**
     * Default theme getter
     * @return string
     */
    protected function _getFallbackTheme()
    {
        return $this->getStore()->getConfig('design/theme/default');
    }
}
