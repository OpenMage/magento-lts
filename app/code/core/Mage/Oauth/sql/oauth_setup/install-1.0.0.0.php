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
 * @package     Mage_Oauth
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Installation of OAuth module tables
 */
/** @var $install Mage_Oauth_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/** @var $adapter Varien_Db_Adapter_Pdo_Mysql */
$adapter = $installer->getConnection();

/**
 * Create table 'oauth/consumer'
 */
$table = $adapter->newTable($installer->getTable('oauth/consumer'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Entity Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true
        ), 'Updated At')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false
        ), 'Name of consumer')
    ->addColumn('key', Varien_Db_Ddl_Table::TYPE_VARCHAR, Mage_Oauth_Model_Consumer::KEY_LENGTH, array(
            'nullable' => false
        ), 'Key code')
    ->addColumn('secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, Mage_Oauth_Model_Consumer::SECRET_LENGTH, array(
            'nullable' => false
        ), 'Secret code')
    ->addColumn('callback_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(), 'Callback URL')
    ->addColumn('rejected_callback_url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false
        ), 'Rejected callback URL')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth/consumer'),
            array('key'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('key'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth/consumer'),
            array('secret'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('secret'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('oauth/consumer', array('created_at')), array('created_at'))
    ->addIndex($installer->getIdxName('oauth/consumer', array('updated_at')), array('updated_at'))
    ->setComment('OAuth Consumers');
$adapter->createTable($table);

/**
 * Create table 'oauth/token'
 */
$table = $adapter->newTable($installer->getTable('oauth/token'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary'  => true,
        ), 'Entity ID')
    ->addColumn('consumer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false
        ), 'Consumer ID')
    ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true
        ), 'Admin user ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true
        ), 'Customer user ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array(
            'nullable' => false
        ), 'Token Type')
    ->addColumn('token', Varien_Db_Ddl_Table::TYPE_TEXT, Mage_Oauth_Model_Token::LENGTH_TOKEN, array(
            'nullable' => false
        ), 'Token')
    ->addColumn('secret', Varien_Db_Ddl_Table::TYPE_TEXT, Mage_Oauth_Model_Token::LENGTH_SECRET, array(
            'nullable' => false
        ), 'Token Secret')
    ->addColumn('verifier', Varien_Db_Ddl_Table::TYPE_TEXT, Mage_Oauth_Model_Token::LENGTH_VERIFIER, array(
            'nullable' => true
        ), 'Token Verifier')
    ->addColumn('callback_url', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false
        ), 'Token Callback URL')
    ->addColumn('revoked', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => 0,
        ), 'Is Token revoked')
    ->addColumn('authorized', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default'  => 0,
        ), 'Is Token authorized')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
            'default'  => Varien_Db_Ddl_Table::TIMESTAMP_INIT
        ), 'Token creation timestamp')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth/token'),
            array('consumer_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
        ),
        array('consumer_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX))
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth/token'),
            array('token'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('token'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName('oauth/token', 'admin_id', $installer->getTable('admin/user'), 'user_id'),
        'admin_id',
        $installer->getTable('admin/user'),
        'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('oauth/token', 'consumer_id', 'oauth/consumer', 'entity_id'),
        'consumer_id',
        $installer->getTable('oauth/consumer'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('oauth/token', 'customer_id', $installer->getTable('customer/entity'), 'entity_id'),
        'customer_id',
        $installer->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('OAuth Tokens');
$adapter->createTable($table);

/**
 * Create table 'oauth/nonce
 */
$table = $adapter->newTable($installer->getTable('oauth/nonce'))
    ->addColumn('nonce', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
        'nullable' => false
        ), 'Nonce String')
    ->addColumn('timestamp', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned' => true,
            'nullable' => false
        ), 'Nonce Timestamp')
    ->addIndex(
        $installer->getIdxName(
            $installer->getTable('oauth/nonce'),
            array('nonce'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('nonce'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setOption('type', 'MyISAM');
$adapter->createTable($table);

$installer->endSetup();
