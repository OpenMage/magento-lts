<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/** @var Mage_Paypal_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

// A stable, indexed link from a PayPal order to its quote and order. paypal_correlation_id is not enough:
// it is overwritten with the capture id once the payment is captured.
foreach (['quote_payment' => 'sales/quote_payment', 'order_payment' => 'sales/order_payment'] as $entity => $table) {
    $installer->addAttribute($entity, 'paypal_order_id', ['type' => Varien_Db_Ddl_Table::TYPE_VARCHAR]);
    $installer->getConnection()->addIndex(
        $installer->getTable($table),
        $installer->getIdxName($table, ['paypal_order_id']),
        ['paypal_order_id'],
    );
}

$installer->endSetup();
