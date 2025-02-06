<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
