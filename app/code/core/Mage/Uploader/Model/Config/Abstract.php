<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Uploader
 */

/**
 * Uploader Config Instance Abstract Model
 *
 * @package    Mage_Uploader
 */
abstract class Mage_Uploader_Model_Config_Abstract extends Varien_Object
{
    /**
     * Get file helper
     *
     * @return Mage_Uploader_Helper_File
     */
    protected function _getHelper()
    {
        return Mage::helper('uploader/file');
    }

    /**
     * Set/Get attribute wrapper
     * Also set data in cameCase for config values
     *
     * @param  string                   $method
     * @param  array                    $args
     * @return bool|mixed|Varien_Object
     * @throws Varien_Exception
     * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
     */
    public function __call($method, $args)
    {
        $key = lcfirst($this->_camelize(substr($method, 3)));
        return match (substr($method, 0, 3)) {
            'get' => $this->getData($key, $args[0] ?? null),
            'set' => $this->setData($key, $args[0] ?? null),
            'uns' => $this->unsetData($key),
            'has' => isset($this->_data[$key]),
            default => throw new Varien_Exception('Invalid method ' . static::class . '::' . $method . '(' . print_r($args, true) . ')'),
        };
    }
}
