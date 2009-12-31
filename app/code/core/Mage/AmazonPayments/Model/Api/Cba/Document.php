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
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Amazon Order Document Api
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Cba_Document extends Varien_Object
{
    const MESSAGE_TYPE_ADJUSTMENT       = '_POST_PAYMENT_ADJUSTMENT_DATA_';
    const MESSAGE_TYPE_FULFILLMENT      = '_POST_ORDER_FULFILLMENT_DATA_';
    const MESSAGE_TYPE_ACKNOWLEDGEMENT  = '_POST_ORDER_ACKNOWLEDGEMENT_DATA_';

    protected $_wsdlUri = null;
    protected $_merchantInfo = array();
    protected $_client = null;
    protected $_result = null;
    protected $_proccessFailed = false;
    protected $_options = array(
        'trace'     => true,
        'timeout'   => '20',
    );

    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Set Wsdl uri
     *
     * @param string $wsdlUri
     * @return Mage_AmazonPayments_Model_Api_Cba_Document
     */
    public function setWsdlUri($wsdlUri)
    {
        $this->_wsdlUri = $wsdlUri;
        return $this;
    }

    /**
     * Return Wsdl Uri
     *
     * @return string
     */
    public function getWsdlUri()
    {
        return $this->_wsdlUri;
    }

    /**
     * Set merchant info
     *
     * @param array $merchantInfo
     * @return Mage_AmazonPayments_Model_Api_Cba_Document
     */
    public function setMerchantInfo(array $merchantInfo = array())
    {
        $this->_merchantInfo = $merchantInfo;
        return $this;
    }

    /**
     * Return merchant info
     *
     * @return array
     */
    public function getMerchantInfo()
    {
        return $this->_merchantInfo;
    }

    /**
     * Return merchant identifier
     *
     * @return string
     */
    public function getMerchantIdentifier()
    {
        if (array_key_exists('merchantIdentifier', $this->_merchantInfo)) {
            return $this->_merchantInfo['merchantIdentifier'];
        }
        return null;
    }

    /**
     * Return Soap object
     *
     * @return SOAP_Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Initialize Soap Client object and authorize
     *
     * @param string $login
     * @param string $password
     * @return Mage_AmazonPayments_Model_Api_Cba_Document
     */
    public function init($login, $password)
    {
        if ($this->getWsdlUri()) {
            $this->_client = null;
            $auth = array('user' => $login, 'pass' => $password);
            try {
                set_include_path(
                     BP . DS . 'lib' . DS . 'PEAR' . PS . get_include_path()
                );
                require_once 'SOAP/Client.php';
                $this->_client = new SOAP_Client($this->getWsdlUri(), true, false, $auth, false);
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Create soap attachment (MIME encoding)
     *
     * @param string $document
     * @return string
     */
    protected function _createAttachment($document)
    {
        require_once 'SOAP/Value.php';
        $attachment = new SOAP_Attachment('doc', 'application/binary', null, $document);
        $attachment->options['attachment']['encoding'] = '8bit';
        $this->_options['attachments'] = 'Mime';
        return $attachment;
    }

    /**
     * Proccess request and setting result
     *
     * @param string $method
     * @param array $params
     * @return Mage_AmazonPayments_Model_Api_Cba_Document
     */
    protected function _proccessRequest($method, $params)
    {
        if ($this->getClient()) {
            $this->_result = null;
            $this->_proccessFailed = false;
            try {
                $this->_result = $this->getClient()
                    ->call($method, $params, $this->_options);
            } catch (Exception $e) {
                $this->_proccessFailed = true;
            }
        }
        return $this;
    }

    /**
     * Format amount value (2 digits after the decimal point)
     *
     * @param float $amount
     * @return float
     */
    public function formatAmount($amount)
    {
        return Mage::helper('amazonpayments')->formatAmount($amount);
    }

    /**
     * Get order info
     *
     * @param string $aOrderId Amazon order id
     * @return Varien_Simplexml_Element
     */
    public function getDocument($aOrderId)
    {
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'documentIdentifier' => $aOrderId
        );
        $this->_proccessRequest('getDocument', $params);

        require_once 'Mail/mimeDecode.php';
        $decoder = new Mail_mimeDecode($this->getClient()->xml);
        $decoder->decode(array(
            'include_bodies' => true,
            'decode_bodies'  => true,
            'decode_headers' => true,
        ));
        $xml = $decoder->_body;

        // remove the ending mime boundary
        $boundaryIndex = strripos($xml, '--xxx-WASP-CPP-MIME-Boundary-xxx');
        if (!($boundaryIndex === false)) {
            $xml = substr($xml, 0, $boundaryIndex);
        }

        return simplexml_load_string($xml, 'Varien_Simplexml_Element');
    }

    /**
     * Get pending orders
     *
     * @return array
     */
    public function getPendingDocuments()
    {
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => '_GET_ORDERS_DATA_'
        );
        $this->_proccessRequest('getAllPendingDocumentInfo', $params);
        if (!is_array($this->_result)) {
            $this->_result = array($this->_result);
        }
        return $this->_result;
    }

    /**
     * Associate Magento real order id with Amazon order id
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function sendAcknowledgement($order)
    {
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
        <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
        </Header>
        <MessageType>OrderAcknowledgement</MessageType>
            <Message>
                <MessageID>1</MessageID>
                <OperationType>Update</OperationType>
                <OrderAcknowledgement>
                    <AmazonOrderID>' . $order->getExtOrderId() . '</AmazonOrderID>
                    <MerchantOrderID>' . $order->getRealOrderId() . '</MerchantOrderID>
                    <StatusCode>Success</StatusCode>
                </OrderAcknowledgement>
            </Message>
        </AmazonEnvelope>';

        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_ACKNOWLEDGEMENT,
            'doc' => $this->_createAttachment($_document)
        );

        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }

    /**
     * Cancel order
     *
     * @param Mage_Sales_Model_Order $order
     * @return string Amazon Transaction Id
     */
    public function cancel($order)
    {
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
        <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
        <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
        </Header>
        <MessageType>OrderAcknowledgement</MessageType>
            <Message>
                <MessageID>1</MessageID>
                <OperationType>Update</OperationType>
                <OrderAcknowledgement>
                    <AmazonOrderID>' . $order->getExtOrderId() . '</AmazonOrderID>
                    <StatusCode>Failure</StatusCode>
                </OrderAcknowledgement>
            </Message>
        </AmazonEnvelope>';

        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_ACKNOWLEDGEMENT,
            'doc' => $this->_createAttachment($_document)
        );

        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }

    /**
     * Refund order
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return string Amazon Transaction Id
     */
    public function refund($payment, $amount)
    {
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
            </Header>
            <MessageType>OrderAdjustment</MessageType>';

        $_shippingAmount = $payment->getCreditmemo()->getShippingAmount();
        $_messageId = 1;
        foreach ($payment->getCreditmemo()->getAllItems() as $item) {
            /* @var $item Mage_Sales_Model_Order_Creditmemo_Item */
            if ($item->getOrderItem()->getParentItemId()) {
                continue;
            }

            $shipping = 0;
            $amazon_amounts = unserialize($item->getOrderItem()->getProductOptionByCode('amazon_amounts'));
            if ($amazon_amounts['shipping'] > $_shippingAmount) {
                $shipping = $_shippingAmount;
            } else {
                $shipping = $amazon_amounts['shipping'];
            }
            $_shippingAmount -= $shipping;

            $_document .= '<Message>
                            <MessageID>' . $_messageId . '</MessageID>
                            <OrderAdjustment>
                                <AmazonOrderID>' . $payment->getOrder()->getExtOrderId() . '</AmazonOrderID>
                                <AdjustedItem>
                                    <AmazonOrderItemCode>'. $item->getOrderItem()->getExtOrderItemId() . '</AmazonOrderItemCode>
                                    <AdjustmentReason>GeneralAdjustment</AdjustmentReason>
                                    <ItemPriceAdjustments>
                                        <Component>
                                            <Type>Principal</Type>
                                            <Amount currency="USD">' . $this->formatAmount($item->getBaseRowTotal()) . '</Amount>
                                        </Component>
                                        <Component>
                                            <Type>Tax</Type>
                                            <Amount currency="USD">' . $this->formatAmount($item->getBaseTaxAmount()) . '</Amount>
                                        </Component>'
                                        .'<Component>
                                            <Type>Shipping</Type>
                                            <Amount currency="USD">' . $this->formatAmount($shipping) . '</Amount>
                                        </Component>'
                                    .'</ItemPriceAdjustments>';
            $_document .= '</AdjustedItem>
                        </OrderAdjustment>
                    </Message>';
            $_messageId++;
        }

        $_document .= '</AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_ADJUSTMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }

    /**
     * Confirm creating of shipment
     *
     * @param string $aOrderId
     * @param string $carrierName
     * @param string $shippingMethod
     * @param array $items
     * @param string $trackNumber
     * @return string Amazon Transaction Id
     */
    public function confirmShipment($aOrderId, $carrierName, $shippingMethod, $items, $trackNumber = '')
    {
        $fulfillmentDate = gmdate('Y-m-d\TH:i:s');
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
            </Header>
            <MessageType>OrderFulfillment</MessageType>
            <Message>
                <MessageID>1</MessageID>
                <OrderFulfillment>
                    <AmazonOrderID>' . $aOrderId . '</AmazonOrderID>
                    <FulfillmentDate>' . $fulfillmentDate . '</FulfillmentDate>
                    <FulfillmentData>
                        <CarrierName>' . strtoupper($carrierName) . '</CarrierName>
                        <ShippingMethod>' . $shippingMethod . '</ShippingMethod>
                        <ShipperTrackingNumber>' . $trackNumber .'</ShipperTrackingNumber>
                    </FulfillmentData>';
        foreach ($items as $item) {
            $_document .= '<Item>
                            <AmazonOrderItemCode>' . $item['id'] . '</AmazonOrderItemCode>
                            <Quantity>' . $item['qty'] . '</Quantity>
                        </Item>';
        }
        $_document .= '</OrderFulfillment>
                </Message>
        </AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_FULFILLMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }

    /**
     * Send Tracking Number
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $carrierCode
     * @param string $carrierMethod
     * @param string $trackNumber
     * @return string Amazon Transaction Id
     */
    public function sendTrackNumber($order, $carrierCode, $carrierMethod, $trackNumber)
    {
        $fulfillmentDate = gmdate('Y-m-d\TH:i:s');
        $_document = '<?xml version="1.0" encoding="UTF-8"?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>' . $this->getMerchantIdentifier() . '</MerchantIdentifier>
            </Header>
            <MessageType>OrderFulfillment</MessageType>';
            $_document .= '<Message>
                    <MessageID>1</MessageID>
                    <OrderFulfillment>
                        <AmazonOrderID>' . $order->getExtOrderId() . '</AmazonOrderID>
                        <FulfillmentDate>' . $fulfillmentDate . '</FulfillmentDate>
                        <FulfillmentData>
                            <CarrierCode>' . $carrierCode . '</CarrierCode>
                            <ShippingMethod>' . $carrierMethod . '</ShippingMethod>
                            <ShipperTrackingNumber>' . $trackNumber .'</ShipperTrackingNumber>
                        </FulfillmentData>
                    </OrderFulfillment>
                </Message>';
        $_document .= '</AmazonEnvelope>';
        $params = array(
            'merchant' => $this->getMerchantInfo(),
            'messageType' => self::MESSAGE_TYPE_FULFILLMENT,
            'doc' => $this->_createAttachment($_document)
        );
        $this->_proccessRequest('postDocument', $params);
        return $this->_result;
    }
}
