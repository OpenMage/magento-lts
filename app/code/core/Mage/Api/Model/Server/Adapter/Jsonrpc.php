<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2023-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Server_Adapter_Jsonrpc extends Varien_Object implements Mage_Api_Model_Server_Adapter_Interface
{
    protected $_jsonRpc = null;

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

        if (null === $controller) {
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
        $this->_jsonRpc = new Zend_Json_Server();
        $this->_jsonRpc->setClass($this->getHandler());

        // Allow soap_v2 style request.
        $request = $this->_jsonRpc->getRequest();
        $method = $request->getMethod();
        if (!$this->_jsonRpc->getServiceMap()->getService($method)) {
            // Convert request to v1 style.
            $request->setMethod('call');
            $params = $request->getParams();
            $sessionId = $params[0] ?? null;
            unset($params[0]);
            $params = count($params)
                ? [$sessionId, $method, $params]
                : [$sessionId, $method];
            $request->setParams($params);
        }

        $this->getController()->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json; charset=utf8')
            ->setBody($this->_jsonRpc->handle());

        Mage::dispatchEvent('api_server_adapter_jsonrpc_run_after', [
            'method' => $method,
            'request' => $request,
            'response' => $this->_jsonRpc->getResponse()
        ]);

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
        throw new Zend_Json_Exception($message, $code);
    }
}
