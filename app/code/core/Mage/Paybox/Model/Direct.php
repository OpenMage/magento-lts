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
 * @package     Mage_Paybox
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paybox Direct Model
 *
 * @category   Mage
 * @package    Mage_Paybox
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paybox_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    /**
     * Paybox direct payment actions
     */
    const PBX_PAYMENT_ACTION_ATHORIZE = '00001';
    const PBX_PAYMENT_ACTION_DEBIT = '00002';
    const PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE = '00003';
    const PBX_PAYMENT_ACTION_CANCELLATION = '00005';
    const PBX_PAYMENT_ACTION_REFUND = '00004';

    const PBX_VERSION = '00103';

    /**
     * ECL(Electronic Commerce Indicator).
     * Type of ordering items. Need for some banks.
     * 024 - request by internet
     */
    const PBX_ACTIVITE_VALUE = '024';

    protected $_code  = 'paybox_direct';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = true;

    protected $_formBlockType = 'paybox/direct_form';
    protected $_infoBlockType = 'paybox/direct_info';

    protected $_order;
    protected $_currenciesNumbers;
    protected $_questionNumberModel;

    /**
     * Return paybox gateway url.
     * If $recallNumber > 0 (primary url is not available) return url of backup gateway
     *
     * @param integer $recallNumber
     * @return string
     */
    public function getPayboxUrl($recallNumber)
    {
        $path = 'pbx_url';
        if ($recallNumber) {
            $path = 'pbx_backupurl';
        }
        return $this->getConfigData($path);
    }

    /**
     * Get Payment Action of Paybox Direct,
     * changed to Paybox specification
     *
     * @return string
     */
    public function getPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        switch ($paymentAction) {
            case self::ACTION_AUTHORIZE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
            case self::ACTION_AUTHORIZE_CAPTURE:
                return self::PBX_PAYMENT_ACTION_ATHORIZE_CAPTURE;
                break;
            default:
                return self::PBX_PAYMENT_ACTION_ATHORIZE;
                break;
        }
    }

    /**
     * Return site number of account (TPE)
     *
     * @return string
     */
    public function getSiteNumber()
    {
        return $this->getConfigData('pbx_site');
    }

    /**
     * Return rang number of account
     *
     * @return string
     */
    public function getRang()
    {
        return $this->getConfigData('pbx_rang');
    }

    /**
     * Return Cle number of account
     *
     * @return string
     */
    public function getCleNumber()
    {
        return $this->getConfigData('pbx_cle');
    }

    /**
     * Return currency number in ISO4217 format
     *
     * @return string
     */
    public function getCurrencyNumb()
    {
        $currencyCode = $this->getPayment()->getOrder()->getBaseCurrencyCode();
        if (!$this->_currenciesNumbers) {
            $this->_currenciesNumbers = simplexml_load_file(Mage::getBaseDir().'/app/code/core/Mage/Paybox/etc/currency.xml');
        }
        if ($this->_currenciesNumbers->$currencyCode) {
            return (string)$this->_currenciesNumbers->$currencyCode;
        }
    }

    /**
     * Return model of Question Number
     *
     * @return Mage_Paybox_Model_Question_Number
     */
    public function getQuestionNumberModel()
    {
        if (!$this->_questionNumberModel) {
            $accountHash = md5($this->getSiteNumber().$this->getRang());
            $this->_questionNumberModel = Mage::getModel('paybox/question_number')->load($accountHash, 'account_hash');
        }
        return $this->_questionNumberModel;
    }

    /**
     * Return Debug Flag
     *
     * @return string
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug_flag');
    }

    public function authorize(Varien_Object $payment, $amount)
    {
        parent::authorize($payment, $amount);

        $this->setAmount($amount)
            ->setPayment($payment);

        if ($this->callDoDirectPayment()!==false) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId())
                ->setPayboxRequestNumber($this->getRequestNumber())
                ->setPayboxQuestionNumber($this->getQuestionNumber());
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('paybox')->__('There has been an error processing your payment. ') . $e['message'];
            } else {
                $message = Mage::helper('paybox')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }

        return $this;
    }

    public function capture(Varien_Object $payment, $amount)
    {
        parent::capture($payment, $amount);

        $this->setAmount($amount)
            ->setPayment($payment);

        if ($payment->getLastTransId()) {//if after authorize
            $result = $this->callDoDebitPayment()!==false;
        } else {//authorize+capture (debit)
            $result = $this->callDoDirectPayment()!==false;
        }

        if ($result) {
            $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId())
                ->setPayboxRequestNumber($this->getRequestNumber());
        } else {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('paybox')->__('There has been an error processing your payment. ') . $e['message'];
            } else {
                $message = Mage::helper('paybox')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED);
        return $this;
    }

    public function refund(Varien_Object $payment, $amount)
    {
        parent::refund($payment, $amount);

        $error = false;
        if($payment->getRefundTransactionId() && $amount>0) {
            $this->setTransactionId($payment->getRefundTransactionId())
                ->setPayment($payment)
                ->setAmount($amount);

            if ($this->callDoRefund()!==false) {
                $payment->setStatus(self::STATUS_SUCCESS)
                    ->setCcTransId($this->getTransactionId());
            } else {
                $payment->setStatus(self::STATUS_ERROR);
                $e = $this->getError();
                if (isset($e['message'])) {
                    $error = $e['message'];
                } else {
                    $error = Mage::helper('paybox')->__('Error in refunding the payment');
                }
            }
        } else {
            $payment->setStatus(self::STATUS_ERROR);
            $error = Mage::helper('paybox')->__('Error in refunding the payment');
        }
        if ($error !== false) {
            Mage::throwException($error);
        }

        return $this;
    }

    /**
     * Building array of params for direct payment
     *
     * @return bool | array
     */
    public function callDoDirectPayment()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => self::PBX_VERSION,
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => $this->getPaymentAction(),
            'NUMQUESTION' => $this->getQuestionNumberModel()->getNextQuestionNumber(),
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'IDENTIFIANT' => '',
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => $this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'PORTEUR' => $payment->getCcNumber(),
            'DATEVAL' => Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear())),
            'CVV' => $payment->getCcCid(),
            'ACTIVITE' => self::PBX_ACTIVITE_VALUE,
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->getQuestionNumberModel()
                ->increaseQuestionNumber();

        $this->setTransactionId($resultArr['NUMTRANS']);
        $this->setRequestNumber($resultArr['NUMAPPEL']);
        $this->setQuestionNumber($resultArr['NUMQUESTION']);

        return $resultArr;
    }

    /**
     * Building array of params for debit (after authorize)
     *
     * @return bool | array
     */
    public function callDoDebitPayment()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => self::PBX_VERSION,
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => self::PBX_PAYMENT_ACTION_DEBIT,
            'NUMQUESTION' => $payment->getPayboxQuestionNumber(),
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => (string)$this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'NUMAPPEL' => $payment->getPayboxRequestNumber(),
            'NUMTRANS' => $payment->getLastTransId(),
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->setTransactionId($resultArr['NUMTRANS']);

        return $resultArr;
    }

    /**
     * Building array of params for refund
     *
     * @return bool | array
     */
    public function callDoRefund()
    {
        $payment = $this->getPayment();
        $requestStr = '';

        $tmpArr = array(
            'VERSION' => self::PBX_VERSION,
            'DATEQ' => Mage::getModel('core/date')->date('dmYHis'),
            'TYPE' => self::PBX_PAYMENT_ACTION_REFUND,
            'NUMQUESTION' => $this->getQuestionNumberModel()->getNextQuestionNumber(),
            'SITE' => $this->getSiteNumber(),
            'RANG' => $this->getRang(),
            'CLE' => $this->getCleNumber(),
            'MONTANT' => ($this->getAmount()*100),
            'DEVISE' => (string)$this->getCurrencyNumb(),
            'REFERENCE' => base64_encode($payment->getOrder()->getRealOrderId()),
            'PORTEUR' => $payment->getCcNumber(),
            'DATEVAL' => Mage::getModel('core/date')->date('my', mktime(0,0,0,$payment->getCcExpMonth(),1,$payment->getCcExpYear())),
            'NUMAPPEL' => '',
            'NUMTRANS' => '',
        );

        foreach ($tmpArr as $param=>$value) {
            $requestStr .= $param . '=' . $value . '&';
        }
        $requestStr = substr($requestStr, 0, -1);

        $resultArr = $this->call($requestStr);

        if ($resultArr === false) {
            return false;
        }

        $this->getQuestionNumberModel()
            ->increaseQuestionNumber();

        $this->setTransactionId($resultArr['NUMTRANS']);

        return $resultArr;
    }

    /**
     * Making a call to gateway
     *
     * @param string $requestStr
     * @return bool | array
     */
    public function call($requestStr)
    {
        if ($this->getDebugFlag()) {
            $debug = Mage::getModel('paybox/api_debug')
                ->setRequestBody($requestStr)
                ->save();
        }
        $recall = true;
        $recallCounter = 0;
        while ($recall && $recallCounter < 3) {
            $recall = false;
            $this->unsError();

            $http = new Varien_Http_Adapter_Curl();
            $config = array('timeout' => 30);
            $http->setConfig($config);
            $http->write(Zend_Http_Client::POST, $this->getPayboxUrl($recallCounter), '1.1', array(), $requestStr);
            $response = $http->read();

            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);

            if ($http->getErrno()) {
                $http->close();
                if ($this->getDebugFlag()) {
                    $debug->setResponseBody($response)->save();
                }
                $this->setError(array(
                    'message' => $http->getError()
                ));
                return false;
            }
            $http->close();

            $parsedResArr = $this->parseResponseStr($response);

            //primary gateway is down, need to recall to backup gateway
            if ($parsedResArr['CODEREPONSE'] == '00001' ||
                $parsedResArr['CODEREPONSE'] == '00097' ||
                $parsedResArr['CODEREPONSE'] == '00098'
                ) {
                $recallCounter++;
                $recall = true;
            }
        }

        if ($this->getDebugFlag()) {
            $debug->setResponseBody($response)->save();
        }

        //if backup gateway was down too
        if ($recall) {
            $this->setError(array(
                'message' => Mage::helper('paybox')->__('Paybox payment gateway is not available right now')
            ));
            return false;
        }

        if ($parsedResArr['CODEREPONSE'] == '00000') {
                return $parsedResArr;
        }

        if (isset($parsedResArr['COMMENTAIRE'])) {
            $this->setError(array(
                'message' => $parsedResArr['CODEREPONSE'] . ':' . $parsedResArr['COMMENTAIRE']
            ));
        }

        return false;
    }

    /**
     * Parsing response string
     *
     * @param string $str
     * @return array
     */
    public function parseResponseStr($str)
    {
        $tmpResponseArr = explode('&', $str);
        $responseArr = array();
        foreach ($tmpResponseArr as $response) {
            $paramValue = explode('=', $response);
            $responseArr[$paramValue[0]] = $paramValue[1];
        }

        return $responseArr;
    }
}
