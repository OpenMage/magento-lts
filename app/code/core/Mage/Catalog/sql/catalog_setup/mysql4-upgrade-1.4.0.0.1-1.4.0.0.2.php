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

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_int',
    'value',
    'int(11) default NULL',
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_decimal',
    'value',
    'decimal(12,4) default NULL',
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category') . '_datetime',
    'value',
    'datetime default NULL',
);

$installer->endSetup();
