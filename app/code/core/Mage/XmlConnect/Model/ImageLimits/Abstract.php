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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Abstract Image Limit model for screen size resolution config
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Model_ImageLimits_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Default update type
     *
     * @var array
     */
    protected $_defaultUpdateType = array();

    /**
     * Current update params (based on a current screen size)
     *
     * @var array
     */
    protected $_currentUpdate = array();

    /**
     * Current configuration
     *
     * @var array
     */
    protected $_configuration = array();

    /**
     * Config updated flag
     *
     * @var bool
     */
    protected $_configUpdatedFlag = false;

    /**
     * Initialize resource model
     *
     * @param string $updateType
     */
    public function __construct($updateType)
    {
        $this->_setCurrentUpdate($updateType)->_setDefaultConfig();
    }

    /**
     * Set default configuration params for current device
     *
     * @return Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    protected function _setDefaultConfig()
    {
        $deviceModel = Mage::helper('xmlconnect')->getApplication()->getDeviceModel();
        $this->_configuration = $deviceModel->getImageSizeConfig();
        return $this;
    }

    /**
     * Set configuration for current device
     *
     * @param array $data
     * @return Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    public function setConfig($data)
    {
        $this->_configuration = $data;
        return $this;
    }

    /**
     * Set update type
     *
     * @param string $updateType
     * @return Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    protected function _setCurrentUpdate($updateType)
    {
        if (isset($this->{'_' . $updateType . 'UpdateType'})) {
            $this->_currentUpdate = $this->{'_' . $updateType . 'UpdateType'};
        } else {
            $this->_currentUpdate = $this->_defaultUpdateType;
        }
        return $this;
    }

    /**
     * Get config for current screen size
     *
     * @return array
     */
    public function getScreenSizeConfig()
    {
        if (!$this->_isConfigUpdated()) {
            $this->_doUpdateConfig();
            $this->_configUpdatedFlag = true;
        }
        return $this->_configuration;
    }

    /**
     * Update config for current screen size
     *
     * @return array
     */
    protected function _doUpdateConfig()
    {
        if (!$this->_currentUpdate) {
            return $this->_configuration;
        }

        foreach ($this->_currentUpdate as $function => $params) {
            $this->_doFunctionUpdate($function, $params);
        }

        return $this->_configuration;
    }

    /**
     * Update config using custom update function
     *
     * @param string $function
     * @param array $params
     * @return Mage_XmlConnect_Model_ImageLimits_Abstract
     */
    protected function _doFunctionUpdate($function, $params)
    {
        foreach ($params as $param) {
            $data = $param['data'];
            $path = $param['path'];

            $target =& $this->findPath($this->_configuration, $path);
            switch ($function) {
                case 'zoom':
                    if (is_array($target)) {
                        array_walk_recursive($target, array($this, '_zoom'), $data);
                    } else {
                        $this->_zoom($target, null, $data);
                    }
                    break;
                case 'update':
                    $this->_update($target, $data);
                    break;
                case 'insert':
                    $this->_insert($target, $data, false);
                    break;
                case 'insert_force':
                    $this->_insert($target, $data);
                    break;
                case 'delete':
                    $this->_delete($target, $data);
                    break;
                default:
                    break;
            }
        }
        return $this;
    }

    /**
     * Get config updated flag state
     *
     * @return bool
     */
    protected function _isConfigUpdated()
    {
        return $this->_configUpdatedFlag;
    }

    /**
     * Return reference to the $path in $array
     *
     * @param array &$array
     * @param string $path
     * @return mixed reference
     */
    public function &findPath(&$array, $path)
    {
        $target =& $array;
        if ($path !== '/') {
            $pathArray = explode('/', $path);
            foreach ($pathArray as $node) {
                if (is_array($target) && isset($target[$node])) {
                    $target =& $target[$node];
                } else {
                    return null;
                }
            }
        }
        return $target;
    }

    /**
     * Insert value(s) in array
     *
     * @param mixed &$target old value(s)
     * @param mixed $data new value(s)
     * @param bool $force
     * @return null
     */
    protected function _insert(&$target, $data, $force = true)
    {
        if (is_array($target) && is_array($data)) {
            foreach ($data as $key => $val) {
                if (!$force && isset($target[$key])) {
                    continue;
                }
                $target[$key] = $val;
            }
        }
    }

    /**
     * Update array value(s) by reference
     *
     * @param mixed &$target old value(s)
     * @param mixed $data new value(s)
     * @return null
     */
    protected function _update(&$target, $data)
    {
        if (is_array($target) && is_array($data)) {
                $data = array_intersect_key($data, $target);
                foreach ($data as $key => $val) {
                    $target[$key] = $val;
                }
        } elseif (is_array($target)) {
            foreach ($target as $key => $val) {
                $target[$key] = $data;
            }
        } elseif ($target) {
            $target = $data;
        }
    }

    /**
     * Delete given key(s) from array
     *
     * @param array &$item
     * @param string|array $value items to delete
     * @return null
     */
    protected function _delete(&$item, $value)
    {
        if (is_array($value)) {
            foreach ($value as $keyToDelete) {
                unset($item[$keyToDelete]);
            }
            return;
        }
        unset($item[$value]);
    }

    /**
     * Multiply given $item by $value if it's a string
     *
     * @param mixed &$item (argument to change)
     * @param mixed $key (used with array_walk_recursive function as a key of given array)
     * @param string $value (contains float)
     * @return null
     */
    protected function _zoom(&$item, $key, $value)
    {
        if (is_scalar($item)) {
            $item = (int) round($item * $value);
        }
    }
}
