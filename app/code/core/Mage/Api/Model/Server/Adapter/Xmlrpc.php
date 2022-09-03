<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice XmlRpc adapter
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Mage_Api_Controller_Action $controller
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
                ['request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse()]
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
        $apiConfigCharset = Mage::getStoreConfig("api/config/charset");

        $this->_xmlRpc = new Zend_XmlRpc_Server();
        $this->_xmlRpc->setEncoding($apiConfigCharset)
            ->setClass($this->getHandler());
        $this->getController()->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'text/xml; charset='.$apiConfigCharset)
            ->setBody($this->_xmlRpc->handle());
        return $this;
    }

    /**
     * Dispatch webservice fault
     *
     * @param int $code
     * @param string $message
     */
    public function fault($code, $message)
    {
        throw new Zend_XmlRpc_Server_Exception($message, $code);
    }
}
