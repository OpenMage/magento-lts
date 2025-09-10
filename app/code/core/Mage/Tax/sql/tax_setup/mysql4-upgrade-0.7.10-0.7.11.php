<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('tax_order_aggregated_created')}` (
        `id`                    int(11) unsigned NOT NULL auto_increment,
        `period`                date NOT NULL DEFAULT '0000-00-00',
        `store_id`              smallint(5) unsigned NULL DEFAULT NULL,
        `code`                  varchar(255) NOT NULL default '',
        `order_status`          varchar(50) NOT NULL default '',
        `percent`               float(12,4) NOT NULL default '0.0000',
        `orders_count`          int(11) unsigned NOT NULL default '0',
        `tax_base_amount_sum`   float(12,4) NOT NULL default '0.0000',
        PRIMARY KEY  (`id`),
        UNIQUE KEY `UNQ_PERIOD_STORE_CODE_ORDER_STATUS` (`period`,`store_id`, `code`, `order_status`),
        KEY `IDX_STORE_ID` (`store_id`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    'FK_TAX_ORDER_AGGREGATED_CREATED_STORE',
    $this->getTable('tax_order_aggregated_created'),
    'store_id',
    $this->getTable('core_store'),
    'store_id',
);

$installer->endSetup();
