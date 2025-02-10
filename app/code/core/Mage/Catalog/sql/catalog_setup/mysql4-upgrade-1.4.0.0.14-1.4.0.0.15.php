<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_wysiwyg_enabled',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
);

$installer->endSetup();
