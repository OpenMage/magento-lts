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

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/product') . '_int',
    'value',
    'int(11) default NULL',
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/product') . '_decimal',
    'value',
    'decimal(12,4) default NULL',
);
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/product') . '_datetime',
    'value',
    'datetime default NULL',
);

$installer->endSetup();
