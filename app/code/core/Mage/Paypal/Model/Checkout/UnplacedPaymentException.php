<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * The buyer has been charged but the Magento order could not be placed. The store has been notified and the
 * quote keeps the payment; callers must show the message and must not let the buyer pay again.
 */
class Mage_Paypal_Model_Checkout_UnplacedPaymentException extends Mage_Paypal_Model_Exception {}
