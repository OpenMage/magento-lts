<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal event observers
 */
class Mage_Paypal_Model_Observer
{
    /**
     * Refuse to convert a quote into an order when PayPal is the selected
     * method but no PayPal order was created and approved for the quote.
     *
     * The REST flow (PaymentController / ExpressController) stores the
     * PayPal request id on the quote payment before placing the order.
     * Third-party checkouts (one-step checkouts and the like) can submit
     * the quote directly through the generic sales service, which would
     * otherwise create an unpaid order that looks paid.
     *
     * Observes: sales_model_service_quote_submit_before
     */
    public function requirePaypalOrderReference(Varien_Event_Observer $observer): void
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            return;
        }

        $payment = $quote->getPayment();
        if (!$payment || $payment->getMethod() !== 'paypal') {
            return;
        }

        $hasReference = $payment->getAdditionalInformation(Mage_Paypal_Model_Payment::PAYPAL_REQUEST_ID)
            || $payment->getPaypalCorrelationId();

        if (!$hasReference) {
            Mage::throwException(
                Mage::helper('paypal')->__('Please complete the PayPal payment before placing the order.'),
            );
        }
    }
}
