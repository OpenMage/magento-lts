<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`',
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/shipment_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`',
);
$installer->getConnection()->addColumn(
    $installer->getTable('sales/creditmemo_comment'),
    'is_visible_on_front',
    'tinyint(1) unsigned not null default 0 after `is_customer_notified`',
);

$installer->endSetup();
