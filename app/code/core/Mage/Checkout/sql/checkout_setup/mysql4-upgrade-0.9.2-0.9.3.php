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
    'is_html',
    'tinyint(4) NOT NULL DEFAULT 0 AFTER `is_active`',
);

$installer->endSetup();
