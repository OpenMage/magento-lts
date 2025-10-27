<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
$data = [
    ['paypal_reversed', 'PayPal Reversed'],
    ['paypal_canceled_reversal', 'PayPal Canceled Reversal'],
];
$connection = $installer->getConnection()->insertArray(
    $installer->getTable('sales/order_status'),
    ['status', 'label'],
    $data,
);
/**
 * Set default value for "skip order review page" option for Express Checkout for new installations
 */
$ecSkipOrderReviewStepFlagPath = 'payment/paypal_express/skip_order_review_step';
Mage::getConfig()->saveConfig($ecSkipOrderReviewStepFlagPath, '1');

/**
 * Set default value for "Mobile Optimized" option for Payflow Link/Advanced/Hosted Pro for new installations
 */
$paymentCode = ['payflow_link', 'payflow_advanced', 'hosted_pro'];
foreach ($paymentCode as $value) {
    Mage::getConfig()->saveConfig("payment/{$value}/mobile_optimized", '1');
}

$installer->endSetup();
