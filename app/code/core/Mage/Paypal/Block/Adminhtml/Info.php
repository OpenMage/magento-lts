<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * PayPal payment information block for admin order view
 */
class Mage_Paypal_Block_Adminhtml_Info extends Mage_Payment_Block_Info
{
    /**
     * Initializes the payment information block by setting the template.
     */
    protected function _construct(): void
    {
        parent::_construct();
        $this->setTemplate('paypal/info.phtml');
    }

    /**
     * Retrieves the transaction ID from the payment information.
     */
    public function getTransactionId(): ?string
    {
        return $this->getInfo()->getLastTransId();
    }

    /**
     * Generates the PayPal transaction URL based on the transaction ID and sandbox status.
     */
    public function getTransactionUrl(): ?string
    {
        $transactionId = $this->getTransactionId();
        if (!$transactionId) {
            return null;
        }

        $isSandbox = Mage::getSingleton('paypal/config')->isSandbox();
        return Mage::helper('paypal')->getTransactionUrl($transactionId, $isSandbox);
    }

    /**
     * Retrieves and formats additional payment information, such as status and authorization details.
     */
    public function getPaymentInfo(): array
    {
        $payment = $this->getInfo();
        $info = [];
        if ($payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_STATUS)) {
            $info[Mage::helper('paypal')->__('Status')] =
                $payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_STATUS);
        }
        if ($payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID)) {
            $info[Mage::helper('paypal')->__('Authorization ID')] =
                $payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID);
            if ($payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME)) {
                $expirationTime =
                    $payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);
                $info[Mage::helper('paypal')->__('Authorization Expires')] = $this->formatExpirationDate($expirationTime);
            }
        }

        return $info;
    }

    /**
     * Formats a given UTC expiration timestamp to the store's local timezone.
     *
     * @param string $expirationTime The expiration timestamp in UTC.
     * @return string The formatted date and time string.
     */
    protected function formatExpirationDate(string $expirationTime): string
    {
        $storeTimezone = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
        $date = new DateTime($expirationTime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($storeTimezone));
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Determines if the payment can be reauthorized based on its method, authorization ID, and expiration/creation time.
     *
     * @return bool True if the payment can be reauthorized, false otherwise.
     */
    public function canReauthorize(): bool
    {
        $payment = $this->getInfo();

        if (
            $payment->getMethod() !== 'paypal' ||
            !$payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_ID)
        ) {
            return false;
        }

        $expirationTime = $payment->getAdditionalInformation(Mage_Paypal_Model_Transaction::PAYPAL_PAYMENT_AUTHORIZATION_EXPIRATION_TIME);
        if ($expirationTime) {
            $now = new DateTime();
            $expDate = new DateTime($expirationTime);
            $daysDiff = $now->diff($expDate)->days;
            return $daysDiff <= 26;
        }

        $authTime = $payment->getCreatedAt();
        $now = new DateTime();
        $authDate = new DateTime($authTime);
        $daysDiff = $now->diff($authDate)->days;

        return $daysDiff >= 3;
    }

    /**
     * Generates the URL for reauthorizing the payment.
     *
     * @return string The reauthorization URL.
     */
    public function getReauthorizeUrl(): string
    {
        return Mage::getUrl('*/paypal_transaction/reauthorize', [
            'order_id' => $this->getInfo()->getOrder()->getId(),
            '_secure' => true,
        ]);
    }
}
