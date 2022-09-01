<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Sendfriend
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sendfriend_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('sendfriend/sendfriend'),
    'IDX_REMOTE_ADDR'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('sendfriend/sendfriend'),
    'IDX_LOG_TIME'
);

/**
 * Change columns
 */
$tables = [
    $installer->getTable('sendfriend/sendfriend') => [
        'columns' => [
            'log_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Log ID'
            ],
            'ip' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_BIGINT,
                'length'    => 20,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer IP address'
            ],
            'time' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Log time'
            ],
            'website_id' => [
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID'
            ]
        ],
        'comment' => 'Send to friend function log storage table',
        'engine'  => 'InnoDB'
    ]
];

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('sendfriend/sendfriend'),
    $installer->getIdxName('sendfriend/sendfriend', ['ip']),
    ['ip']
);

$installer->getConnection()->addIndex(
    $installer->getTable('sendfriend/sendfriend'),
    $installer->getIdxName('sendfriend/sendfriend', ['time']),
    ['time']
);

$installer->endSetup();
