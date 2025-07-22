<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice soap adapter
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Server_Wsi_Adapter_Soap extends Mage_Api_Model_Server_Adapter_Soap
{
    /**
     * Get wsdl config
     *
     * @return Mage_Api_Model_Wsdl_Config
     */
    protected function _getWsdlConfig()
    {
        $wsdlConfig = Mage::getModel('api/wsdl_config');
        $wsdlConfig->setHandler($this->getHandler())
            ->init();
        return $wsdlConfig;
    }

    /**
     * Run webservice
     *
     * @return $this
     * @throws SoapFault
     */
    public function run()
    {
        $apiConfigCharset = Mage::getStoreConfig('api/config/charset');

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                ->setBody(
                    preg_replace(
                        '/(\>\<)/i',
                        ">\n<",
                        str_replace(
                            '<soap:operation soapAction=""></soap:operation>',
                            "<soap:operation soapAction=\"\" />\n",
                            str_replace(
                                '<soap:body use="literal"></soap:body>',
                                "<soap:body use=\"literal\" />\n",
                                preg_replace(
                                    '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                    '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                                    $this->wsdlConfig->getWsdlContent(),
                                ),
                            ),
                        ),
                    ),
                );
        } else {
            try {
                $this->_instantiateServer();

                $content = str_replace(
                    '><',
                    ">\n<",
                    str_replace(
                        '<soap:operation soapAction=""></soap:operation>',
                        "<soap:operation soapAction=\"\" />\n",
                        str_replace(
                            '<soap:body use="literal"></soap:body>',
                            "<soap:body use=\"literal\" />\n",
                            preg_replace(
                                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                                $this->_soap->handle(),
                            ),
                        ),
                    ),
                );

                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                    ->setHeader('Content-Length', strlen($content), true)
                    ->setBody($content);
            } catch (Zend_Soap_Server_Exception|Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            }
        }

        return $this;
    }
}
