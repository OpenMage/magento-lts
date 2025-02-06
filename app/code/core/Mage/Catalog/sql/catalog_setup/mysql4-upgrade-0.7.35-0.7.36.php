<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
} catch (Exception $e) {
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
