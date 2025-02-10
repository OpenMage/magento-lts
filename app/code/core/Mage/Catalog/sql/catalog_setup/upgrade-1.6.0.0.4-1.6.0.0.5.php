<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/category_product_index'),
    'position',
    [
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => false,
        'nullable'  => true,
        'default'   => null,
        'comment'   => 'Position',
    ],
);
