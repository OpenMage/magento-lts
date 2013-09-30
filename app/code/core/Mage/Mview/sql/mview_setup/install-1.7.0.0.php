<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Mview
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

/**
 * Create table 'mview/metadata'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mview/metadata'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Materialized view Id')
    ->addColumn('mview_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 60, array(
        'nullable'  => false,
    ), 'Materialized view name')
    ->addColumn('view_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'View name')
    ->addColumn('changelog', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(), 'Changelog name')
    ->addColumn('rule_column', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(), 'Rule column name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 16, array(
        'nullable'  => false,
    ), 'Materialized view status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Date of materialized view creation')
    ->addColumn('refreshed_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Date of last refresh')
    ->addIndex($installer->getIdxName('mview/metadata', array('mview_name'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('mview_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addIndex($installer->getIdxName('mview/metadata', array('view_name'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('view_name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Materialized view metadata');
$installer->getConnection()->createTable($table);

/**
 * Create table 'mview/changelog'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mview/changelog'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Changelog Id')
    ->addColumn('mview_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Materialized view Id')
    ->addColumn('log_table', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'Log table name')
    ->addColumn('log_column', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'Log column name')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'default'   => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
    ), 'Date of changelog creation')
    ->addIndex($installer->getIdxName('mview/changelog', array('mview_id', 'log_table'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('mview_id', 'log_table'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->addForeignKey($installer->getFkName('mview/changelog', 'mview_id', 'mview/metadata', 'id'),
        'mview_id',
        $installer->getTable('mview/metadata'),
        'id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_NO_ACTION
    )
    ->setComment('Materialized view metadata');
$installer->getConnection()->createTable($table);
