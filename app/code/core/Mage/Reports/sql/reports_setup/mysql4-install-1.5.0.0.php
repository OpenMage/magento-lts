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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/viewed_product_index'),
        $installer->getIdxName(
            'reports/viewed_product_index',
            ['customer_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );

    /**
     * Unique indexes for reports/compared_product_index
     */
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            ['visitor_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['visitor_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
    $installer->getConnection()->addIndex(
        $installer->getTable('reports/compared_product_index'),
        $installer->getIdxName(
            'reports/compared_product_index',
            ['customer_id', 'product_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        ['customer_id', 'product_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    );
}
