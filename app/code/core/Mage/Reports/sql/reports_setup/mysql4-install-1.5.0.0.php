<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installFile = __DIR__ . DS . 'install-1.6.0.0.php';
if (file_exists($installFile)) {
    include $installFile;

    /**
     * Unique indexes for reports/viewed_product_index
     */
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/viewed_product_index'),
        $installer->getIdxName(
            'reports/viewed_product_index',
            ['visitor_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/viewed_product_index'),
        $installer->getIdxName(
            'reports/viewed_product_index',
            ['customer_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );

    /**
     * Unique indexes for reports/compared_product_index
     */
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            ['visitor_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            ['customer_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
        ),
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    );
}
