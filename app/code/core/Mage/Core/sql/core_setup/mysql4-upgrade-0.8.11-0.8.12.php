<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('core_store'),
    'name',
    'name',
    'varchar(255) not null',
    true,
);

$installer->getConnection()->changeColumn(
    $installer->getTable('core_store_group'),
    'name',
    'name',
    'varchar(255) not null',
    true,
);

$installer->endSetup();
