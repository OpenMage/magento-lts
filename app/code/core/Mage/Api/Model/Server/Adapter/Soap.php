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
 * Webservice soap adapter
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_Adapter_Soap
    extends Varien_Object
    implements Mage_Api_Model_Server_Adapter_Interface
{
    /**
     * Soap server
     *
     * @var SoapServer
     */
    protected $_soap = null;

    /**
     * Set handler class name for webservice
     *
     * @param string $handler
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function setHandler($handler)
    {
        $this->setData('handler', $handler);
        return $this;
    }

    /**
     * Retrive handler class name for webservice
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
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function setController(Mage_Api_Controller_Action $controller)
    {
         $this->setData('controller', $controller);
         return $this;
    }

    /**
     * Retrive webservice api controller
     *
     * @return Mage_Api_Controller_Action
     */
    public function getController()
    {
        return $this->getData('controller');
    }

    /**
     * Run webservice
     *
     * @param Mage_Api_Controller_Action $controller
     * @return Mage_Api_Model_Server_Adapter_Soap
     */
    public function run()
    {
        if ($this->getController()->getRequest()->getParam('wsdl')) {
            // Generating wsdl content from template
            $io   = new Varien_Io_File();
            $io->open(array('path'=>Mage::getModuleDir('etc', 'Mage_Api')));
            $wsdlContent = $io->read('wsdl.xml');

            $template = Mage::getModel('core/email_template_filter');

            $wsdlConfig = new Varien_Object();
            $wsdlConfig->setUrl(Mage::getUrl('*/*/*'));
            $wsdlConfig->setName('Magento');
            $wsdlConfig->setHandler($this->getHandler());

            $template->setVariables(array('wsdl'=>$wsdlConfig));

            $this->getController()->getResponse()
                ->setHeader('Content-Type','text/xml')
                ->setBody($template->filter($wsdlContent));

        } elseif ($this->_extensionLoaded()) {
            $this->_soap = new SoapServer(Mage::getUrl('*/*/*', array('wsdl'=>1)));
            use_soap_error_handler(false);
            $this->_soap->setClass($this->getHandler());
            $this->getController()->getResponse()
                ->setHeader('Content-Type', 'text/xml')
                ->setBody($this->_soap->handle());

        } else {
            $this->fault('0', 'Unable to load Soap extension on the server');
        }
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
        if ($this->_extensionLoaded()) {
            throw new SoapFault($code, $message);
        } else {
            die('<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                <SOAP-ENV:Body>
                <SOAP-ENV:Fault>
                <faultcode>' . $code . '</faultcode>
                <faultstring>' . $message . '</faultstring>
                </SOAP-ENV:Fault>
                </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>');
        }

    }

    /**
     *  Check whether Soap extension is loaded
     *
     *  @param    none
     *  @return	  boolean
     */
    protected function _extensionLoaded ()
    {
        return class_exists('SoapServer');
    }

} // Class Mage_Api_Model_Server_Adapter_Soap End