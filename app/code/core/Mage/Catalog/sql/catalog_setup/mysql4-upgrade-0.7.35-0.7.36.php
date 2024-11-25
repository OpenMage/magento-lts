<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
