<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Free payment method
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Method_Free extends Mage_Payment_Model_Method_Abstract
{
    /**
     * XML Paths for configuration constants
     */
    public const XML_PATH_PAYMENT_FREE_ACTIVE = 'payment/free/active';

    public const XML_PATH_PAYMENT_FREE_ORDER_STATUS = 'payment/free/order_status';

    public const XML_PATH_PAYMENT_FREE_PAYMENT_ACTION = 'payment/free/payment_action';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'free';

    /**
     * Check whether method is available
     *
     * @param  null|Mage_Sales_Model_Quote $quote
     * @return bool
     */
    #[Override]
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && !empty($quote)
            && Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) == 0;
    }

    /**
     * Validate payment method information object
     *
     * isAvailable() only runs when payment data is imported, but the order can be placed later
     * without re-importing it (onepage saveOrder skips the import when the request carries no
     * payment data), after the cart total has gone back up. Re-check the zero total against the
     * order being placed so this method can never settle an order that has something to pay.
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function validate()
    {
        parent::validate();

        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $grandTotal = $paymentInfo->getOrder()->getGrandTotal();
        } else {
            $quote = $paymentInfo->getQuote();
            $grandTotal = $quote instanceof Mage_Sales_Model_Quote ? $quote->getGrandTotal() : 0;
        }

        if (Mage::app()->getStore()->roundPrice((float) $grandTotal) !== 0.0) {
            Mage::throwException(Mage::helper('payment')->__('The requested Payment Method is not available.'));
        }

        return $this;
    }

    /**
     * Get config payment action, do nothing if status is pending
     *
     * @return null|string
     */
    #[Override]
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('order_status') == 'pending' ? null : parent::getConfigPaymentAction();
    }
}
