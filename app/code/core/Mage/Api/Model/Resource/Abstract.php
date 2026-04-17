<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Api resource abstract
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Abstract
{
    /**
     * Resource configuration
     *
     * @var Varien_Simplexml_Element
     */
    protected $_resourceConfig = null;

    /**
     * Retrieve webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('api/config');
    }

    /**
     * Set configuration for api resource
     *
     * @return $this
     */
    public function setResourceConfig(Varien_Simplexml_Element $xml)
    {
        $this->_resourceConfig = $xml;
        return $this;
    }

    /**
     * Retrieve configuration for api resource
     *
     * @return Varien_Simplexml_Element
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Retrieve webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('api/server');
    }

    /**
     * Dispatches fault
     *
     * @param  string             $code
     * @param  null|string        $customMessage
     * @return never
     * @throws Mage_Api_Exception
     */
    protected function _fault($code, $customMessage = null)
    {
        throw new Mage_Api_Exception($code, $customMessage);
    }
}
