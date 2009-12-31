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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cybersource
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Cybersource_Model_Api_ExtendedSoapClient extends SoapClient
{
    /**
     * Store Id for retrieving config data
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Store Id setter
     *
     * @param int $storeId
     * @return Mage_Cybersource_Model_Api_ExtendedSoapClient
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Store Id getter
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * XPaths that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataXPaths = array(
        '//*[contains(name(),\'merchantID\')]/text()',
        '//*[contains(name(),\'card\')]/*/text()',
        '//*[contains(name(),\'UsernameToken\')]/*/text()'
    );

    public function __construct($wsdl, $options = array())
    {
        parent::__construct($wsdl, $options);
    }

    protected function getBaseApi()
    {
        return Mage::getSingleton('cybersource/soap');
    }

    public function __doRequest($request, $location, $action, $version)
    {
        $api = $this->getBaseApi();
        $user = $api->getConfigData('merchant_id', $this->getStoreId());
        $password = $api->getConfigData('security_key', $this->getStoreId());
        $soapHeader = "<SOAP-ENV:Header xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:wsse=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd\"><wsse:Security SOAP-ENV:mustUnderstand=\"1\"><wsse:UsernameToken><wsse:Username>$user</wsse:Username><wsse:Password Type=\"http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText\">$password</wsse:Password></wsse:UsernameToken></wsse:Security></SOAP-ENV:Header>";

        $requestDOM = new DOMDocument('1.0');
        $soapHeaderDOM = new DOMDocument('1.0');
        $requestDOM->loadXML($request);
        $soapHeaderDOM->loadXML($soapHeader);

        $node = $requestDOM->importNode($soapHeaderDOM->firstChild, true);
        $requestDOM->firstChild->insertBefore(
        $node, $requestDOM->firstChild->firstChild);

        $request = $requestDOM->saveXML();
        if ($api->getConfigData('debug', $this->getStoreId())) {

            $requestDOMXPath = new DOMXPath($requestDOM);

            foreach ($this->_debugReplacePrivateDataXPaths as $xPath) {
                foreach ($requestDOMXPath->query($xPath) as $element) {
                    $element->data = '***';
                }
            }

            $debug = Mage::getModel('cybersource/api_debug')
                ->setAction($action)
                ->setRequestBody($requestDOM->saveXML())
                ->save();
        }

        $response = parent::__doRequest($request, $location, $action, $version);

        if (!empty($debug)) {
            $debug
                ->setResponseBody($response)
                ->save();
        }

        return $response;
    }
}
