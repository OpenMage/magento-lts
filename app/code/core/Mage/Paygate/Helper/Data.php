<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paygate
 */

/**
 * Paygate data helper
 *
 * @package    Mage_Paygate
 */
class Mage_Paygate_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Paygate';

    /**
     * Converts a lot of messages to message
     *
     * @param  array $messages
     * @return string
     */
    public function convertMessagesToMessage($messages)
    {
        return implode(' | ', $messages);
    }

    /**
     * Return message for gateway transaction request
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $requestType
     * @param string $lastTransactionId
     * @param Varien_Object $card
     * @param float|false $amount
     * @param string|false $exception
     * @return bool|string
     */
    public function getTransactionMessage(
        $payment,
        $requestType,
        $lastTransactionId,
        $card,
        $amount = false,
        $exception = false
    ) {
        return $this->getExtendedTransactionMessage(
            $payment,
            $requestType,
            $lastTransactionId,
            $card,
            $amount,
            $exception,
        );
    }

    /**
     * Return message for gateway transaction request
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $requestType
     * @param string|null $lastTransactionId
     * @param Varien_Object $card
     * @param float|false $amount
     * @param string|false $exception
     * @param string|false $additionalMessage Custom message, which will be added to the end of generated message
     * @return bool|string
     */
    public function getExtendedTransactionMessage(
        $payment,
        $requestType,
        $lastTransactionId,
        $card,
        $amount = false,
        $exception = false,
        $additionalMessage = false
    ) {
        $operation = $this->_getOperation($requestType);

        if (!$operation) {
            return false;
        }

        if ($amount) {
            $amount = $this->__('amount %s', $this->_formatPrice($payment, $amount));
        }

        if ($exception) {
            $result = $this->__('failed');
        } else {
            $result = $this->__('successful');
        }

        $card = $this->__('Credit Card: xxxx-%s', $card->getCcLast4());

        $pattern = '%s %s %s - %s.';
        $texts = [$card, $amount, $operation, $result];

        if (!is_null($lastTransactionId)) {
            $pattern .= ' %s.';
            $texts[] = $this->__('Authorize.Net Transaction ID %s', $lastTransactionId);
        }

        if ($additionalMessage) {
            $pattern .= ' %s.';
            $texts[] = $additionalMessage;
        }
        $pattern .= ' %s';
        $texts[] = $exception;

        return call_user_func_array([$this, '__'], array_merge([$pattern], $texts));
    }

    /**
     * Return operation name for request type
     *
     * @param  string $requestType
     * @return bool|string
     */
    protected function _getOperation($requestType)
    {
        return match ($requestType) {
            Mage_Paygate_Model_Authorizenet::REQUEST_TYPE_AUTH_ONLY => $this->__('authorize'),
            Mage_Paygate_Model_Authorizenet::REQUEST_TYPE_AUTH_CAPTURE => $this->__('authorize and capture'),
            Mage_Paygate_Model_Authorizenet::REQUEST_TYPE_PRIOR_AUTH_CAPTURE => $this->__('capture'),
            Mage_Paygate_Model_Authorizenet::REQUEST_TYPE_CREDIT => $this->__('refund'),
            Mage_Paygate_Model_Authorizenet::REQUEST_TYPE_VOID => $this->__('void'),
            default => false,
        };
    }

    /**
     * Format price with currency sign
     * @param  Mage_Payment_Model_Info $payment
     * @param float $amount
     * @return string
     */
    protected function _formatPrice($payment, $amount)
    {
        return $payment->getOrder()->getBaseCurrency()->formatTxt($amount);
    }
}
