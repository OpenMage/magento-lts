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
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('customer/flowpassword'))
    ->addColumn('flowpassword_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Flow password Id')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable' => false,
    ), 'User IP')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Requested email for change')
    ->addColumn('requested_date', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'default'  => '0000-00-00 00:00:00',
    ), 'Requested date for change')
    ->addIndex($installer->getIdxName('customer/flowpassword', array('email')),
        array('email'))
    ->addIndex($installer->getIdxName('customer/flowpassword', array('ip')),
        array('ip'))
    ->addIndex($installer->getIdxName('customer/flowpassword', array('requested_date')),
        array('requested_date'))
    ->setComment('Customer flow password');
$installer->getConnection()->createTable($table);

$installer->endSetup();
