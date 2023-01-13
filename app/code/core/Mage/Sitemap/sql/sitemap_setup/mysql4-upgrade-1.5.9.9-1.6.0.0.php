<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$connection = $installer->getConnection()->dropForeignKey(
    $installer->getTable('sitemap/sitemap'),
    'FK_SITEMAP_STORE'
);

/**
 * Drop indexes
 */
$connection = $installer->getConnection()->dropIndex(
    $installer->getTable('sitemap/sitemap'),
    'FK_SITEMAP_STORE'
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
                'comment'   => 'Sitemap Id'
            ],
            'sitemap_type' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sitemap Type'
            ],
            'sitemap_filename' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'comment'   => 'Sitemap Filename'
            ],
            'sitemap_path' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Sitemap Path'
            ],
            'sitemap_time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Sitemap Time'
            ],
            'store_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store id'
            ]
        ],
        'comment' => 'Google Sitemap'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$connection = $installer->getConnection()->addIndex(
    $installer->getTable('sitemap/sitemap'),
    $installer->getIdxName('sitemap/sitemap', ['store_id']),
    ['store_id']
);

/**
 * Add foreign keys
 */
$connection = $installer->getConnection()->addForeignKey(
    $installer->getFkName('sitemap/sitemap', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('sitemap/sitemap'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
