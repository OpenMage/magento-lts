<?php

/**
 * Set default value for "skip order review page" option for Express Checkout for upgrades
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */
$ecSkipOrderReviewStepFlagPath = 'payment/paypal_express/skip_order_review_step';
Mage::getConfig()->saveConfig($ecSkipOrderReviewStepFlagPath, '0');
