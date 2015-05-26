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
 * @package     Mage_Shipping
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'shipping/tablerate'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('shipping/tablerate'))
    ->addColumn('pk', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Primary key')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Website Id')
    ->addColumn('dest_country_id', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Destination coutry ISO/2 or ISO/3 code')
    ->addColumn('dest_region_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Destination Region Id')
    ->addColumn('dest_zip', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        'nullable'  => false,
        'default'   => '*',
        ), 'Destination Post Code (Zip)')
    ->addColumn('condition_name', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        'nullable'  => false,
        ), 'Rate Condition name')
    ->addColumn('condition_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Rate condition value')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    ->addColumn('cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Cost')
    ->addIndex($installer->getIdxName('shipping/tablerate', array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Shipping Tablerate');
$installer->getConnection()->createTable($table);

$installer->endSetup();
