<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('paypal/settlement_report_row'), 'payment_tracking_id', [
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Payment Tracking ID',
        'length'  => '255',
    ]);
