<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('checkout_agreement'),
    'content_height',
    'varchar(25) NULL DEFAULT NULL AFTER `content`',
);

$installer->endSetup();
