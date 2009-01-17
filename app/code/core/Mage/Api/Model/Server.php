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
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api abstract
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server
{
    /**
     * Web service adapter
     *
     * @var Mage_Api_Model_Server_Adaper_Interface
     */
    protected $_adapter;

    public function init(Mage_Api_Controller_Action $controller, $adapter='default', $handler='default')
    {
        $adapters = Mage::getSingleton('api/config')->getActiveAdapters();
        $handlers = Mage::getSingleton('api/config')->getHandlers();
        if (isset($adapters[$adapter])) {
            $adapterModel = Mage::getModel((string) $adapters[$adapter]->model);
            /* @var $adapterModel Mage_Api_Model_Server_Adapter_Interface */
            if (!($adapterModel instanceof Mage_Api_Model_Server_Adapter_Interface)) {
                Mage::throwException(Mage::helper('api')->__('Invalid webservice adapter specified'));
            }

            $this->_adapter = $adapterModel;
            $this->_adapter->setController($controller);

            if (!isset($handlers->$handler)) {
                Mage::throwException(Mage::helper('api')->__('Invalid webservice handler specified'));
            }

            $handlerClassName = Mage::getConfig()->getModelClassName((string) $handlers->$handler->model);
            $this->_adapter->setHandler($handlerClassName);
        } else {
            Mage::throwException(Mage::helper('api')->__('Invalid webservice adapter specified'));
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
     * Retrieve web service adapter
     *
     * @return Mage_Api_Model_Server_Adaper_Interface
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }


} // Class Mage_Api_Model_Server_Abstract End