<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$this->getTable('catalog/product_option_type_value')}` ADD `sort_order` int(10) unsigned NOT NULL default '0';
");

$installer->endSetup();
