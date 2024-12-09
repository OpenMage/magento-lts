<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
