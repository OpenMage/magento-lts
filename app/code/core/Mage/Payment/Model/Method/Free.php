<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Free payment method
 *
 * @category   Mage
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
     * @param Mage_Sales_Model_Quote|null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && !empty($quote)
            && Mage::app()->getStore()->roundPrice($quote->getGrandTotal()) == 0;
    }

    /**
     * Get config payment action, do nothing if status is pending
     *
     * @return string|null
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('order_status') == 'pending' ? null : parent::getConfigPaymentAction();
    }
}
