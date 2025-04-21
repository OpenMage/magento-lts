<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog/product')}` ADD `has_options` SMALLINT(1) NOT NULL DEFAULT '0';
");

$installer->addAttribute('catalog_product', 'has_options', [
    'type' => 'static',
    'visible' => false,
    'default' => false,
]);
$installer->run("
    UPDATE `{$installer->getTable('catalog/product')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_option')}` GROUP BY product_id
    ));
    UPDATE `{$installer->getTable('catalog/product')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_super_attribute')}` GROUP BY product_id
    ));
");

$installer->endSetup();
