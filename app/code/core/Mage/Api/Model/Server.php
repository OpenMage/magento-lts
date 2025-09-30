<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice api abstract
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Server
{
    /**
     * Api Name by Adapter
     * @var string
     */
    protected $_api = '';

    /**
     * Web service adapter
     *
     * @var Mage_Api_Model_Server_Adapter_Interface
     */
    protected $_adapter;

    /**
     * Complex retrieve adapter code by calling auxiliary model method
     *
     * @param string $alias Alias name
     * @return string|null Returns NULL if no alias found
     */
    public function getAdapterCodeByAlias($alias)
    {
        /** @var Mage_Api_Model_Config $config */
        $config  = Mage::getSingleton('api/config');
        $aliases = $config->getAdapterAliases();

        if (!isset($aliases[$alias])) {
            return null;
        }
        $object = Mage::getModel($aliases[$alias][0]);
        $method = $aliases[$alias][1];

        if (!method_exists($object, $method)) {
            Mage::throwException(Mage::helper('api')->__('Can not find webservice adapter.'));
        }
        return $object->$method();
    }

    /**
     * Initialize server components
     *
     * @param string $adapter Adapter name
     * @param string $handler Handler name
     * @return $this
     */
    public function init(Mage_Api_Controller_Action $controller, $adapter = 'default', $handler = 'default')
    {
        $this->initialize($adapter, $handler);

        $this->_adapter->setController($controller);

        return $this;
    }

    /**
     * Initialize server components. Lightweight implementation of init() method
     *
     * @param string $adapterCode Adapter code
     * @param string $handler OPTIONAL Handler name (if not specified, it will be found from config)
     * @return $this
     */
    public function initialize($adapterCode, $handler = null)
    {
        /** @var Mage_Api_Model_Config $helper */
        $helper   = Mage::getSingleton('api/config');
        $adapters = $helper->getActiveAdapters();

        if (isset($adapters[$adapterCode])) {
            /** @var Mage_Api_Model_Server_Adapter_Interface $adapterModel */
            $adapterModel = Mage::getModel((string) $adapters[$adapterCode]->model);

            if (!($adapterModel instanceof Mage_Api_Model_Server_Adapter_Interface)) {
                Mage::throwException(Mage::helper('api')->__('Invalid webservice adapter specified.'));
            }
            $this->_adapter = $adapterModel;
            $this->_api     = $adapterCode;

            // get handler code from config if no handler passed as argument
            if ($handler === null && !empty($adapters[$adapterCode]->handler)) {
                $handler = (string) $adapters[$adapterCode]->handler;
            }
            $handlers = $helper->getHandlers();

            if (!isset($handlers->$handler)) {
                Mage::throwException(Mage::helper('api')->__('Invalid webservice handler specified.'));
            }
            $handlerClassName = Mage::getConfig()->getModelClassName((string) $handlers->$handler->model);

            $this->_adapter->setHandler($handlerClassName);
        } else {
            Mage::throwException(Mage::helper('api')->__('Invalid webservice adapter specified.'));
        }
        return $this;
    }

    /**
     * Run server
     *
     */
    public function run()
    {
        $this->getAdapter()->run();
    }

    /**
     * Get Api name by Adapter
     * @return string
     */
    public function getApiName()
    {
        return $this->_api;
    }

    /**
     * Retrieve web service adapter
     *
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }
}
