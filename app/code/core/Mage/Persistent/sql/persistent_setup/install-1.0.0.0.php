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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'persistent/session'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('persistent/session'))
    ->addColumn('persistent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'primary'  => true,
        'nullable' => false,
        'unsigned' => true,
    ), 'Session id')
    ->addColumn('key', Varien_Db_Ddl_Table::TYPE_TEXT, Mage_Persistent_Model_Session::KEY_LENGTH, array(
        'nullable' => false,
    ), 'Unique cookie key')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Customer id')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Website ID')
    ->addColumn('info', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Session Data')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Updated At')
    ->addIndex($installer->getIdxName('persistent/session', array('key')), array('key'), array(
        'type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ))
    ->addIndex($installer->getIdxName('persistent/session', array('customer_id')), array('customer_id'), array(
        'type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ))
    ->addIndex($installer->getIdxName('persistent/session', array('updated_at')), array('updated_at'))
    ->addForeignKey(
        $installer->getFkName('persistent/session', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('persistent/session', 'website_id', 'core/website', 'website_id'),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Persistent Session');

$installer->getConnection()->createTable($table);

/**
 * Alter sales_flat_quote table with is_persistent flag
 *
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('sales/quote'),
        'is_persistent',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned' => true,
            'default'  => '0',
            'comment'  => 'Is Quote Persistent',
        )
    );

$installer->endSetup();
