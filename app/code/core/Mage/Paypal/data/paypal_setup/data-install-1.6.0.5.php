<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $this Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();
$data = array(
    array('paypal_reversed', 'PayPal Reversed'),
    array('paypal_canceled_reversal', 'PayPal Canceled Reversal')
);
$connection = $installer->getConnection()->insertArray(
    $installer->getTable('sales/order_status'),
    array('status', 'label'),
    $data
);
/**
 * Set default value for "skip order review page" option for Express Checkout for new installations
 */
$ecSkipOrderReviewStepFlagPath = 'payment/paypal_express/skip_order_review_step';
Mage::getConfig()->saveConfig($ecSkipOrderReviewStepFlagPath, '1');

/**
 * Set default value for "Mobile Optimized" option for Payflow Link/Advanced/Hosted Pro for new installations
 */
$paymentCode = array('payflow_link', 'payflow_advanced', 'hosted_pro');
foreach($paymentCode as $value) {
    Mage::getConfig()->saveConfig("payment/{$value}/mobile_optimized", '1');
}
$installer->endSetup();
