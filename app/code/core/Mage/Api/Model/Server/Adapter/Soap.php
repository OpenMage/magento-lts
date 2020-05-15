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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice soap adapter
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Server_Adapter_Soap extends Varien_Object implements Mage_Api_Model_Server_Adapter_Interface
{
    /**
     * Wsdl config
     *
     * @var Varien_Object
     */
    protected $wsdlConfig = null;

    /**
     * Soap server
     *
     * @var SoapServer
     */
    protected $_soap = null;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->wsdlConfig = $this->_getWsdlConfig();
    }

    /**
     * Get wsdl config
     *
     * @return Varien_Object
     */
    protected function _getWsdlConfig()
    {
        $wsdlConfig = new Varien_Object();
        $queryParams = $this->getController()->getRequest()->getQuery();
        if (isset($queryParams['wsdl'])) {
            unset($queryParams['wsdl']);
        }

        $wsdlConfig->setUrl(Mage::helper('api')->getServiceUrl('*/*/*', array('_query' => $queryParams), true));
        $wsdlConfig->setName('Magento');
        $wsdlConfig->setHandler($this->getHandler());
        return $wsdlConfig;
    }

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
     * @return $this
     */
    public function setController(Mage_Api_Controller_Action $controller)
    {
         $this->setData('controller', $controller);
         return $this;
    }

    /**
     * Retrive webservice api controller. If no controller have been set - emulate it by the use of Varien_Object
     *
     * @return Varien_Object
     */
    public function getController()
    {
        $controller = $this->getData('controller');

        if (null === $controller) {
            $controller = new Varien_Object(
                array('request' => Mage::app()->getRequest(), 'response' => Mage::app()->getResponse())
            );

            $this->setData('controller', $controller);
        }
        return $controller;
    }

    /**
     * Run webservice
     *
     * @return $this
     * @throws SoapFault
     */
    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig("api/config/charset");

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            // Generating wsdl content from template
            $io = new Varien_Io_File();
            $io->open(array('path'=>Mage::getModuleDir('etc', 'Mage_Api')));

            $wsdlContent = $io->read('wsdl.xml');

            $template = Mage::getModel('core/email_template_filter');

            $template->setVariables(array('wsdl' => $this->wsdlConfig));

            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'text/xml; charset='.$apiConfigCharset)
                ->setBody(
                    preg_replace(
                        '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                        '<?xml version="$1" encoding="'.$apiConfigCharset.'"?>',
                        $template->filter($wsdlContent)
                    )
                );
        } else {
            try {
                $this->_instantiateServer();

                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'text/xml; charset='.$apiConfigCharset)
                    ->setBody(
                        preg_replace(
                            '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                            '<?xml version="$1" encoding="'.$apiConfigCharset.'"?>',
                            $this->_soap->handle()
                        )
                    );
            } catch (Zend_Soap_Server_Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            } catch (Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            }
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
     * Check whether Soap extension is loaded
     *
     * @return boolean
     */
    protected function _extensionLoaded()
    {
        return class_exists('SoapServer', false);
    }

    /**
     * Transform wsdl url if $_SERVER["PHP_AUTH_USER"] is set
     *
     * @param array $params
     * @param bool $withAuth
     * @return string
     * @throws Zend_Uri_Exception
     */
    protected function getWsdlUrl($params = null, $withAuth = true)
    {
        $urlModel = Mage::getModel('core/url')
            ->setUseSession(false);

        $wsdlUrl = $params !== null
            ? Mage::helper('api')->getServiceUrl('*/*/*', array('_current' => true, '_query' => $params))
            : Mage::helper('api')->getServiceUrl('*/*/*');

        if ($withAuth) {
            $phpAuthUser = rawurlencode($this->getController()->getRequest()->getServer('PHP_AUTH_USER', false));
            $phpAuthPw = rawurlencode($this->getController()->getRequest()->getServer('PHP_AUTH_PW', false));
            $scheme = rawurlencode($this->getController()->getRequest()->getScheme());

            if ($phpAuthUser && $phpAuthPw) {
                $wsdlUrl = sprintf(
                    "%s://%s:%s@%s",
                    $scheme,
                    $phpAuthUser,
                    $phpAuthPw,
                    str_replace($scheme . '://', '', $wsdlUrl)
                );
            }
        }

        return $wsdlUrl;
    }

    /**
     * Try to instantiate Zend_Soap_Server
     * If schema import error is caught, it will retry in 1 second.
     *
     * @throws Zend_Soap_Server_Exception
     */
    protected function _instantiateServer()
    {
        $apiConfigCharset = Mage::getStoreConfig('api/config/charset');
        $wsdlCacheEnabled = (bool) Mage::getStoreConfig('api/config/wsdl_cache_enabled');

        if ($wsdlCacheEnabled) {
            ini_set('soap.wsdl_cache_enabled', '1');
        } else {
            ini_set('soap.wsdl_cache_enabled', '0');
        }

        $tries = 0;
        do {
            $retry = false;
            try {
                $this->_soap = new Zend_Soap_Server(
                    $this->getWsdlUrl(array("wsdl" => 1)),
                    array('encoding' => $apiConfigCharset)
                );
            } catch (SoapFault $e) {
                if (false !== strpos(
                    $e->getMessage(),
                    "can't import schema from 'http://schemas.xmlsoap.org/soap/encoding/'"
                )
                ) {
                    $retry = true;
                    sleep(1);
                } else {
                    throw $e;
                }
                $tries++;
            }
        } while ($retry && $tries < 5);
        use_soap_error_handler(false);
        $this->_soap
            ->setReturnResponse(true)
            ->setClass($this->getHandler());
    }
}
