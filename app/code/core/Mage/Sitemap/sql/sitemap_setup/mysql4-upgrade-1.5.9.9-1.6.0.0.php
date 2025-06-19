<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('sitemap/sitemap'),
    'FK_SITEMAP_STORE',
);

/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('sitemap/sitemap'),
    'FK_SITEMAP_STORE',
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('sitemap/sitemap') => [
        'columns' => [
            'sitemap_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Sitemap Id',
            ],
            'sitemap_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sitemap Type',
            ],
            'sitemap_filename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sitemap Filename',
            ],
            'sitemap_path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sitemap Path',
            ],
            'sitemap_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Sitemap Time',
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store id',
            ],
        ],
        'comment' => 'Google Sitemap',
    ],
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('sitemap/sitemap'),
    $installer->getIdxName('sitemap/sitemap', ['store_id']),
    ['store_id'],
);

/**
 * Add foreign keys
 */
$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('sitemap/sitemap', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sitemap/sitemap'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
);

$installer->endSetup();
