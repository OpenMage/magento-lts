<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$purgeFk = [
    $installer->getTable('tag/relation') => [
        'product_id', 'tag_id', 'customer_id', 'store_id',
    ],
    $installer->getTable('tag/summary') => [
        'tag_id',
    ],
];
$purgeIndex = [
    [
        $installer->getTable('tag/relation'),
        ['product_id'],
    ],
    [
        $installer->getTable('tag/relation'),
        ['tag_id'],
    ],
    [
        $installer->getTable('tag/relation'),
        ['customer_id'],
    ],
    [
        $installer->getTable('tag/relation'),
        ['store_id'],
    ],
    [
        $installer->getTable('tag/summary'),
        ['tag_id'],
    ],
];
foreach ($purgeFk as $tableName => $columns) {
    $foreignKeys = $installer->getConnection()->getForeignKeys($tableName);
    foreach ($foreignKeys as $fkProp) {
        if (in_array($fkProp['COLUMN_NAME'], $columns)) {
            $installer->getConnection()
                ->dropForeignKey($tableName, $fkProp['FK_NAME']);
        }
    }
}

foreach ($purgeIndex as $prop) {
    list($tableName, $columns) = $prop;
    $indexList = $installer->getConnection()->getIndexList($tableName);
    foreach ($indexList as $indexProp) {
        if ($columns === $indexProp['COLUMNS_LIST']) {
            $installer->getConnection()->dropKey($tableName, $indexProp['KEY_NAME']);
        }
    }
}

$installer->getConnection()->addKey(
    $installer->getTable('tag/relation'),
    'IDX_PRODUCT',
    'product_id',
);
$installer->getConnection()->addKey(
    $installer->getTable('tag/relation'),
    'IDX_TAG',
    'tag_id',
);
$installer->getConnection()->addKey(
    $installer->getTable('tag/relation'),
    'IDX_CUSTOMER',
    'customer_id',
);
$installer->getConnection()->addKey(
    $installer->getTable('tag/relation'),
    'IDX_STORE',
    'store_id',
);
$installer->getConnection()->addKey(
    $installer->getTable('tag/summary'),
    'IDX_TAG',
    'tag_id',
);

$installer->getConnection()->addConstraint(
    'FK_TAG_RELATION_PRODUCT',
    $installer->getTable('tag/relation'),
    'product_id',
    $installer->getTable('catalog/product'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_TAG_RELATION_TAG',
    $installer->getTable('tag/relation'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_TAG_RELATION_CUSTOMER',
    $installer->getTable('tag/relation'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_TAG_RELATION_STORE',
    $installer->getTable('tag/relation'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->getConnection()->addConstraint(
    'FK_TAG_SUMMARY_TAG',
    $installer->getTable('tag/summary'),
    'tag_id',
    $installer->getTable('tag/tag'),
    'tag_id',
    'CASCADE',
    'CASCADE',
    true,
);
$installer->endSetup();
