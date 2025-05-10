<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice XmlRpc adapter
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Server_Adapter_Xmlrpc extends Varien_Object implements Mage_Api_Model_Server_Adapter_Interface
{
    /**
     * XmlRpc Server
     *
     * @var Zend_XmlRpc_Server
     */
    protected $_xmlRpc = null;

    /**
     * Set handler class name for webservice
     *
     * @param string $handler
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->setData('handler', $handler);
        return $this;
    }

    /**
     * Retrieve handler class name for webservice
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->getData('handler');
    }

    /**
     * Set webservice api controller
     *
     * @return $this
     */
    public function setController(Mage_Api_Controller_Action $controller)
    {
        $this->setData('controller', $controller);
        return $this;
    }

    /**
     * Retrieve webservice api controller. If no controller have been set - emulate it by the use of Varien_Object
     *
     * @return Mage_Api_Controller_Action|Varien_Object
     */
    public function getController()
    {
        $controller = $this->getData('controller');

        if ($controller === null) {
            $controller = new Varien_Object(
                ['request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse()],
            );

            $this->setData('controller', $controller);
        }
        return $controller;
    }

    /**
     * Run webservice
     *
     * @return $this
     */
    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig('api/config/charset');

        $this->_xmlRpc = new Zend_XmlRpc_Server();
        $this->_xmlRpc->setEncoding($apiConfigCharset)
            ->setClass($this->getHandler());
        $this->getController()->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
            ->setBody($this->_xmlRpc->handle());
        return $this;
    }

    /**
     * Dispatch webservice fault
     *
     * @param int $code
     * @param string $message
     * @return never
     */
    public function fault($code, $message)
    {
        throw new Zend_XmlRpc_Server_Exception($message, $code);
    }
}
