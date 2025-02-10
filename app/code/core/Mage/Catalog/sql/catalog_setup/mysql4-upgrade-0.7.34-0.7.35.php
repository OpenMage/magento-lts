<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `{$installer->getTable('catalog_category_entity')}` DROP `is_active`;");

$installer->endSetup();
