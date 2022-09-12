<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Api resource abstract
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Varien_Simplexml_Element $xml
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
     * @param string $code
     * @param string|null $customMessage
     * @throws Mage_Api_Exception
     */
    protected function _fault($code, $customMessage = null)
    {
        throw new Mage_Api_Exception($code, $customMessage);
    }
}
