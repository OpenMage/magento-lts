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

$installer = $this;
/** @var Mage_Sendfriend_Model_Resource_Setup $installer */

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('sendfriend/sendfriend'))
    ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Log ID')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_BIGINT, '20', [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Customer IP address')
    ->addColumn('time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Log time')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, [
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ], 'Website ID')
    ->addIndex($installer->getIdxName('sendfriend/sendfriend', 'ip'), 'ip')
    ->addIndex($installer->getIdxName('sendfriend/sendfriend', 'time'), 'time')
    ->setComment('Send to friend function log storage table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
