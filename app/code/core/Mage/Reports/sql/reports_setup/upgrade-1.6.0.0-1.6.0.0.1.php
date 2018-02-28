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
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Prepare database for tables install
 */
$installer->startSetup();

$aggregationTables = array(
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
    Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
);
$aggregationTableComments = array(
    'Most Viewed Products Aggregated Daily',
    'Most Viewed Products Aggregated Monthly',
    'Most Viewed Products Aggregated Yearly',
);

for ($i = 0; $i < 3; ++$i) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable($aggregationTables[$i]))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Id')
        ->addColumn('period', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            ), 'Period')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            ), 'Store Id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            ), 'Product Id')
        ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => true,
            ), 'Product Name')
        ->addColumn('product_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
            'default'   => '0.0000',
            ), 'Product Price')
        ->addColumn('views_num', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'default'   => '0',
            ), 'Number of Views')
        ->addColumn('rating_pos', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            ), 'Rating Pos')
        ->addIndex(
            $installer->getIdxName(
                $aggregationTables[$i],
                array('period', 'store_id', 'product_id'),
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            array('period', 'store_id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName($aggregationTables[$i], array('store_id')), array('store_id'))
        ->addIndex($installer->getIdxName($aggregationTables[$i], array('product_id')), array('product_id'))
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'store_id', 'core/store', 'store_id'),
            'store_id', $installer->getTable('core/store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName($aggregationTables[$i], 'product_id', 'catalog/product', 'entity_id'),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment($aggregationTableComments[$i]);
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();
