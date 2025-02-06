<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Set default value for "skip order review page" option for Express Checkout for upgrades
 */
$ecSkipOrderReviewStepFlagPath = 'payment/paypal_express/skip_order_review_step';
Mage::getConfig()->saveConfig($ecSkipOrderReviewStepFlagPath, '0');
