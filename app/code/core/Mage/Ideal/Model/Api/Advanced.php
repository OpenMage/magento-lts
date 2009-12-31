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
 * @package     Mage_Ideal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Advanced Api Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Ideal_Model_Api_Advanced extends Varien_Object
{
    /**
     * Transaction status returned in Status Request
     */
    const STATUS_OPEN = 'Open';
    const STATUS_EXPIRED = 'Expired';
    const STATUS_SUCCESS = 'Success';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_FAILED = 'Failed';

    /**
     * @var Mage_Ideal_Model_Api_Advanced_Security
     */
    protected $_security;

    /**
     * array with configuration values
     *
     * @var array()
     */
    protected $_conf;

    /**
     * @var Varien_Http_Adapter_Curl
     */
    protected $_http;

    public function __construct()
    {
        $this->_http = new Varien_Http_Adapter_Curl();
        $this->_security = new Mage_Ideal_Model_Api_Advanced_Security();

        if ($this->getConfigData('test_flag') == 1) {
            $acquirerUrl = 'https://idealtest.secure-ing.com/ideal/iDeal';
        } else {
            $acquirerUrl = 'https://ideal.secure-ing.com/ideal/iDeal';
        }

        if (!($description = $this->getConfigData('description'))) {
            $description = Mage::app()->getStore()->getName() . ' payment';
        }

        $this->_conf = array(
            'PRIVATEKEY' => $this->getConfigData('private_key'),
            'PRIVATEKEYPASS' => $this->getConfigData('private_keypass'),
            'PRIVATECERT' => $this->getConfigData('private_cert'),
            'AUTHENTICATIONTYPE' => 'SHA1_RSA',
            'CERTIFICATE0' => $this->getConfigData('certificate'),
            'ACQUIRERURL' => $acquirerUrl,
            'ACQUIRERTIMEOUT' => '10',
            'MERCHANTID' => $this->getConfigData('merchant_id'),
            'SUBID' => '0',
            'MERCHANTRETURNURL' => Mage::getUrl('ideal/advanced/result', array('_secure' => true)),
            'CURRENCY' => 'EUR',
            'EXPIRATIONPERIOD' => 'PT10M',
            'LANGUAGE' => 'nl',
            'DESCRIPTION' => $description,
            'ENTRANCECODE' => ''
        );

        if ((int)$this->getConfigData('expire_period') >= 1 && $this->getConfigData('expire_period') < 60) {
            $this->_conf['EXPIRATIONPERIOD'] = 'PT' . $this->getConfigData('expire_period') . 'M';
        } else if ($this->getConfigData('description') == 60) {
            $this->_conf['EXPIRATIONPERIOD'] = 'PT1H';
        }
    }

    /**
     * Getting config parametrs
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('payment/ideal_advanced/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     * this method processes a request regardless of the type.
     */

    public function processRequest($requestType, $debug = false)
    {
        if($requestType instanceof Mage_Ideal_Model_Api_Advanced_DirectoryRequest) {
            $response = $this->processDirRequest($requestType);
        } else if($requestType instanceof Mage_Ideal_Model_Api_Advanced_AcquirerStatusRequest) {
            $response = $this->processStatusRequest($requestType);
        } else if($requestType instanceof Mage_Ideal_Model_Api_Advanced_AcquirerTrxRequest) {
            $response = $this->processTrxRequest($requestType);
        }

        if ($debug) {

            if ($response === false) {
                $responseData = $this->getError();
            } else {
                $responseData = $response->getData();
            }

            Mage::getModel('ideal/api_debug')
                ->setResponseBody(get_class($requestType) . "\n" . print_r($responseData, true))
                ->setRequestBody(get_class($requestType) . "\n" . print_r($requestType->getData(), true))
                ->save();
        }

        return $response;
    }

    /**
     * This method sends HTTP XML DirectoryRequest to the Acquirer system.
     * Befor calling, all mandatory properties have to be set in the Request object
     * by calling the associated setter methods.
     * If the request was successful, the response Object is returned.
     * @param Request Object filled with necessary data for the XML Request
     * @return Response Object with the data of the XML response.
     */
    public function processDirRequest($request)
    {
        if ($request->getMerchantId() == "") {
            $request->setMerchantId($this->_conf["MERCHANTID"]);
        }

        if ($request->getSubId() == "") {
            $request->setSubId($this->_conf["SUBID"]);
        }

        if ($request->getAuthentication() == "") {
            $request->setAuthentication($this->_conf["AUTHENTICATIONTYPE"]);
        }

        $res = new Mage_Ideal_Model_Api_Advanced_DirectoryResponse();

        if (!$request->checkMandatory()) {
            $res->setError(Mage::helper('ideal')->__('Required fields missing'));
            return $res;
        }

        // build concatenated string
        $timestamp = $this->getGMTTimeMark();
        $token = "";
        $tokenCode = "";

        if ("SHA1_RSA" == $request->getAuthentication()) {
            $message = $timestamp . $request->getMerchantId() . $request->getSubId();
            $message = $this->_strip($message);

            //build fingerprint of your own certificate
            $token = $this->_security->createCertFingerprint($this->_conf["PRIVATECERT"]);

            //sign the part of the message that need to be signed
            $tokenCode = $this->_security->signMessage($this->_conf["PRIVATEKEY"], $this->_conf["PRIVATEKEYPASS"], $message);

            //encode with base64
            $tokenCode = base64_encode($tokenCode);
        }

        $reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<DirectoryReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
            . "<createDateTimeStamp>" . utf8_encode( $timestamp ) . "</createDateTimeStamp>\n"
            . "<Merchant>\n"
            . "<merchantID>" . utf8_encode( htmlspecialchars( $request->getMerchantId() ) ) . "</merchantID>\n"
            . "<subID>" . utf8_encode( $request->getSubId() ) . "</subID>\n"
            . "<authentication>" . utf8_encode( $request->getAuthentication() ) . "</authentication>\n"
            . "<token>" . utf8_encode( $token ) . "</token>\n"
            . "<tokenCode>" . utf8_encode( $tokenCode ) . "</tokenCode>\n"
            . "</Merchant>\n"
            . "</DirectoryReq>";

        $response = $this->_post($this->_conf["ACQUIRERURL"], $this->_conf["ACQUIRERTIMEOUT"], $reqMsg);

        if ($response === false) {
            return false;
        }

        $xml = new SimpleXMLElement($response);

        if(!$xml->Error) {
            $res->setOk(true);
            $res->setAcquirer($xml->Acquirer->acquirerID);
            $issuerArray = array();
            foreach ($xml->Directory->Issuer as $issuer) {
                $issuerArray[(string)$issuer->issuerID] = (string)$issuer->issuerName;
            }
            $res->setIssuerList($issuerArray);
            return $res;
        } else {
            $this->setError($xml->Error->consumerMessage);
            return false;
        }
    }

    /**
     * This method sends HTTP XML AcquirerTrxRequest to the Acquirer system.
     * Befor calling, all mandatory properties have to be set in the Request object
     * by calling the associated setter methods.
     * If the request was successful, the response Object is returned.
     * @param Request Object filled with necessary data for the XML Request
     * @return Response Object with the data of the XML response.
     */
    public function processTrxRequest($request) {

        if ($request->getMerchantId() == "")
            $request->setMerchantId($this->_conf["MERCHANTID"]);
        if ($request->getSubId() == "")
            $request->setSubId($this->_conf["SUBID"]);
        if ($request->getAuthentication() == "")
            $request->setAuthentication($this->_conf["AUTHENTICATIONTYPE"]);
        if ($request->getMerchantReturnUrl() == "")
            $request->setMerchantReturnUrl($this->_conf["MERCHANTRETURNURL"]);
        if ($request->getCurrency() == "")
            $request->setCurrency($this->_conf["CURRENCY"]);
        if ($request->getExpirationPeriod() == "")
            $request->setExpirationPeriod($this->_conf["EXPIRATIONPERIOD"]);
        if ($request->getLanguage() == "")
            $request->setLanguage($this->_conf["LANGUAGE"]);
        if ($request->getEntranceCode() == "")
            $request->setEntranceCode($this->_conf["ENTRANCECODE"]);
        if ($request->getDescription() == "")
            $request->setDescription($this->_conf["DESCRIPTION"]);

        $res = new Mage_Ideal_Model_Api_Advanced_AcquirerTrxResponse();

        if (!$request->checkMandatory()) {
            $res->setError(Mage::helper('ideal')->__('Required fields missing'));
            return $res;
        }

        // build concatenated string
        $timestamp = $this->getGMTTimeMark();
        $token = "";
        $tokenCode = "";
        if ( "SHA1_RSA" == $request->getAuthentication() ) {
            $message = $timestamp
                . $request->getIssuerId()
                . $request->getMerchantId()
                . $request->getSubId()
                . $request->getMerchantReturnUrl()
                . $request->getPurchaseId()
                . $request->getAmount()
                . $request->getCurrency()
                . $request->getLanguage()
                . $request->getDescription()
                . $request->getEntranceCode();
            $message = $this->_strip($message);

            //create fingerprint so the receiver knows what certificate to use
            $token = $this->_security->createCertFingerprint($this->_conf["PRIVATECERT"]);

            //sign the message
            $tokenCode = $this->_security->signMessage($this->_conf["PRIVATEKEY"], $this->_conf["PRIVATEKEYPASS"], $message);
            //encode it with base64
            $tokenCode = base64_encode($tokenCode);
        }

        $reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
                . "<AcquirerTrxReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
                . "<createDateTimeStamp>" . utf8_encode($timestamp) .  "</createDateTimeStamp>\n"
                . "<Issuer>" . "<issuerID>" . utf8_encode(htmlspecialchars($request->getIssuerId())) . "</issuerID>\n"
                . "</Issuer>\n"
                . "<Merchant>" . "<merchantID>" . utf8_encode(htmlspecialchars($request->getMerchantId())) . "</merchantID>\n"
                . "<subID>" . utf8_encode($request->getSubId()) . "</subID>\n"
                . "<authentication>" . utf8_encode($request->getAuthentication()) . "</authentication>\n"
                . "<token>" . utf8_encode($token) . "</token>\n"
                . "<tokenCode>" . utf8_encode($tokenCode) . "</tokenCode>\n"
                . "<merchantReturnURL>" . utf8_encode(htmlspecialchars($request->getMerchantReturnUrl())) . "</merchantReturnURL>\n"
                . "</Merchant>\n"
                . "<Transaction>" . "<purchaseID>" . utf8_encode(htmlspecialchars($request->getPurchaseId())) . "</purchaseID>\n"
                . "<amount>" . utf8_encode($request->getAmount()) . "</amount>\n"
                . "<currency>" . utf8_encode($request->getCurrency()) . "</currency>\n"
                . "<expirationPeriod>" . utf8_encode($request->getExpirationPeriod()) . "</expirationPeriod>\n"
                . "<language>" . utf8_encode($request->getLanguage()) . "</language>\n"
                . "<description>" . utf8_encode(htmlspecialchars($request->getDescription())) . "</description>\n"
                . "<entranceCode>" . utf8_encode(htmlspecialchars($request->getEntranceCode())) . "</entranceCode>\n"
                . "</Transaction>" . "</AcquirerTrxReq>";

        $response = $this->_post($this->_conf["ACQUIRERURL"], $this->_conf["ACQUIRERTIMEOUT"], $reqMsg);

        if ($response === false) {
            return false;
        }

        $xml = new SimpleXMLElement($response);

        if(!$xml->Error) {
            $issuerUrl = (string)$xml->Issuer->issuerAuthenticationURL;
            $transactionId = (string)$xml->Transaction->transactionID;
            $res->setIssuerAuthenticationUrl($issuerUrl);
            $res->setTransactionId($transactionId);
            $res->setOk(true);
            return $res;
        } else {
            $this->setError($xml->Error->consumerMessage);
            return false;
        }
    }

    /**
     * This method sends HTTP XML AcquirerStatusRequest to the Acquirer system.
     * Befor calling, all mandatory properties have to be set in the Request object
     * by calling the associated setter methods.
     * If the request was successful, the response Object is returned.
     * @param Request Object filled with necessary data for the XML Request
     * @return Response Object with the data of the XML response.
     */
    public function processStatusRequest($request)
    {
        if ($request->getMerchantId() == "")
            $request->setMerchantId($this->_conf["MERCHANTID"]);
        if ($request->getSubId() == "")
            $request->setSubId($this->_conf["SUBID"]);
        if ($request->getAuthentication() == "")
            $request->setAuthentication($this->_conf["AUTHENTICATIONTYPE"]);

        $res = new Mage_Ideal_Model_Api_Advanced_AcquirerStatusResponse();

        if (!$request->checkMandatory()) {
            $$request->setErrorMessage(Mage::helper('ideal')->__('Required fields missing'));
            return $res;
        }

        // build concatenated string
        $timestamp = $this->getGMTTimeMark();
        $token = "";
        $tokenCode = "";
        if ("SHA1_RSA" == $request->getAuthentication()) {
            $message = $timestamp . $request->getMerchantId() . $request->getSubId() . $request->getTransactionId();
            $message = $this->_strip($message);

            //create fingerprint of your own certificate
            $token = $this->_security->createCertFingerprint($this->_conf["PRIVATECERT"]);
            //sign the message
            $tokenCode = $this->_security->signMessage( $this->_conf["PRIVATEKEY"], $this->_conf["PRIVATEKEYPASS"], $message );
            //encode with base64
            $tokenCode = base64_encode($tokenCode);
        }
        $reqMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            . "<AcquirerStatusReq xmlns=\"http://www.idealdesk.com/Message\" version=\"1.1.0\">\n"
            . "<createDateTimeStamp>" . utf8_encode($timestamp) . "</createDateTimeStamp>\n"
            . "<Merchant>" . "<merchantID>" . utf8_encode(htmlspecialchars($request->getMerchantId())) . "</merchantID>\n"
            . "<subID>" . utf8_encode($request->getSubId()) . "</subID>\n"
            . "<authentication>" . utf8_encode($request->getAuthentication()) . "</authentication>\n"
            . "<token>" . utf8_encode($token) . "</token>\n"
            . "<tokenCode>" . utf8_encode($tokenCode) . "</tokenCode>\n"
            . "</Merchant>\n"
            . "<Transaction>" . "<transactionID>" . utf8_encode(htmlspecialchars($request->getTransactionId())) . "</transactionID>\n"
            . "</Transaction>" . "</AcquirerStatusReq>";

        $response = $this->_post($this->_conf["ACQUIRERURL"], $this->_conf["ACQUIRERTIMEOUT"], $reqMsg);

        if ($response === false) {
            return false;
        }

        //Process response
        $xml = new SimpleXMLElement($response);
        $status = (string)$xml->Transaction->status;
        $creationTime = (string)$xml->createDateTimeStamp;
        $transactionId = (string)$xml->Transaction->transactionID;
        $consumerAccountNumber = (string)$xml->Transaction->consumerAccountNumber;
        $consumerName = (string)$xml->Transaction->consumerName;
        $consumerCity = (string)$xml->Transaction->consumerCity;

        //Check status
        if ( strtoupper('Success') == strtoupper($status) ) {
            $res->setAuthenticated(true);
        } else {
            $res->setAuthenticated(false);
        }

        $res->setTransactionStatus($status);
        $res->setTransactionId($transactionId);
        $res->setConsumerAccountNumber($consumerAccountNumber);
        $res->setConsumerName($consumerName);
        $res->setConsumerCity($consumerCity);
        $res->setCreationTime($creationTime);

        // now check the signature of the incoming message
        //create signed message string
        $message = $creationTime . $transactionId . $status . $consumerAccountNumber;
        $message = trim( $message );

        //now we want to check the signature that has been sent
        $signature64 = (string)$xml->Signature->signatureValue;

        //decode the base64 encoded signature
        $sig = base64_decode($signature64);

        //get the fingerprint out of the response
        $fingerprint = (string)$xml->Signature->fingerprint;

        //search for the certificate file with the given fingerprint
        $certfile = $this->_security->getCertificateName($fingerprint, $this->_conf);

        if($certfile == false) {
            $res->setAuthenticated(false);
            $res->setError('Fingerprint unknown.');
            return $res;
        }

        //prepend directory
        $valid = $this->_security->verifyMessage($certfile, $message, $sig);

        if( $valid != 1 ) {
            $res->setAuthenticated(false);
            $res->setError('Bad signature.');
            return $res;
        }

        $res->setOk(true);
        return $res;
    }

    /**
     * Return GMT Time mark
     *
     * @return string
     */
    public function getGMTTimeMark()
    {
        return gmdate('Y') . '-' . gmdate('m') . '-' . gmdate('d') . 'T'
            . gmdate('H') . ':' . gmdate('i') . ':' . gmdate('s') . '.000Z';
    }

    /**
     * Post a request, note that for a HTTPS URL no port is required
     *
     * @param string $url
     * @param string $path
     * @param array  $params
     * @return mixed
     */
    protected function _post($url, $timeout, $dataToSend)
    {
        $config = array('timeout' => 30);
        $this->_http->setConfig($config);
        $this->_http->write(Zend_Http_Client::POST, $url, '1.1', array(), $dataToSend);
        $response = $this->_http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if ($this->_http->getErrno()) {
            $this->_http->close();
            $this->setError($this->_http->getErrno() . ':' . $this->_http->getError());
            return false;
        }
        $this->_http->close();
        return $response;
    }

    /**
     * stripping spaces
     *
     * @param string $message
     * @return string
     */
    protected function _strip($message)
    {
        $message = str_replace(' ', '', $message);
        $message = str_replace("\t", '', $message);
        $message = str_replace("\n", '', $message );
        return $message;
    }
}
