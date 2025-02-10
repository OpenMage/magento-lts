<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/category_anchor_products_indexer_tmp'),
    'position',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => true,
        'comment'   => 'Position',
    ],
);
