<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
abstract class Mage_Core_Model_Resource_Entity_Abstract
{
    protected $_name = null;
    /**
     * Configuration object
     *
     * @var Varien_Simplexml_Config
     */
    protected $_config = [];

    /**
     * Set config
     *
     * @param Varien_Simplexml_Config $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Get config by key
     *
     * @param string $key
     * @return string|bool
     */
    public function getConfig($key = '')
    {
        if ($key === '') {
            return $this->_config;
        } elseif (isset($this->_config->$key)) {
            return $this->_config->$key;
        } else {
            return false;
        }
    }
}
