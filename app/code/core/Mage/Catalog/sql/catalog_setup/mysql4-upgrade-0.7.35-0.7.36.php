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

try {
    $installer->run("
        ALTER TABLE `{$installer->getTable('catalog_category_entity')}` ADD `level` INT NOT NULL;
        ALTER TABLE `{$installer->getTable('catalog_category_entity')}` ADD INDEX `IDX_LEVEL` ( `level` );
    ");
} catch (Exception) {
}

$installer->rebuildCategoryLevels();

$installer->addAttribute('catalog_category', 'level', [
    'type'              => 'static',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Level',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => false,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
]);

$installer->endSetup();
