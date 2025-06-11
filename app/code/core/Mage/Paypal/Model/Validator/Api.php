<?php
class Mage_Paypal_Model_Validator_Api
{
    public function validateResponse($response, $order)
    {
        if (!$response->isSuccess()) {
            $message = 'PayPal API Error: ' . $response->getError();
            Mage::log($message, Zend_Log::ERR, 'paypal.log', true);
            
            if (Mage::getStoreConfigFlag('payment/paypal/debug')) {
                $quote = $order->getQuote();
                $quoteId = $quote ? $quote->getId() : $order->getQuoteId();
                
                Mage::getModel('paypal/debug')->log(
                    $quoteId,
                    'validation_error',
                    'Validation Error',
                    $response->getError(),
                    null,
                    null
                );
            }
            
            throw new Mage_Paypal_Model_Exception($message);
        }
        return true;
    }
}
