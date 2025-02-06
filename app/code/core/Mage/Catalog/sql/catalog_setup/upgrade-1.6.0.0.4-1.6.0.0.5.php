<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
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
