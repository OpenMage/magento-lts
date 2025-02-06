<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/** @var Mage_Core_Model_Resource_Setup $this */
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
$installer->endSetup();
