<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
DELETE `{$installer->getTable('sales_flat_quote')}`.* FROM `{$installer->getTable('sales_flat_quote')}`
    LEFT JOIN `{$installer->getTable('core_store')}`
        ON `{$installer->getTable('sales_flat_quote')}`.`store_id`=`{$installer->getTable('core_store')}`.`store_id`
    WHERE `{$installer->getTable('core_store')}`.`store_id` IS NULL;
");

$installer->getConnection()->addConstraint(
    'FK_SALES_QUOTE_STORE',
    $installer->getTable('sales_flat_quote'),
    'store_id',
    $installer->getTable('core_store'),
    'store_id',
);

$installer->endSetup();
