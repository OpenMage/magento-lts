<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$select = $installer->getConnection()->select()
    ->from($installer->getTable('catalog_category_product'), [
        'category_id',
        'product_id',
        'position',
        'cnt' => 'COUNT(product_id)',
    ])
    ->group('category_id')
    ->group('product_id')
    ->having('cnt > 1');
$rowSet = $installer->getConnection()->fetchAll($select);

foreach ($rowSet as $row) {
    $data = [
        'category_id'   => $row['category_id'],
        'product_id'    => $row['product_id'],
        'position'      => $row['position'],
    ];
    $installer->getConnection()->delete($installer->getTable('catalog_category_product'), [
        $installer->getConnection()->quoteInto('category_id = ?', $row['category_id']),
        $installer->getConnection()->quoteInto('product_id = ?', $row['product_id']),
    ]);
    $installer->getConnection()->insert($installer->getTable('catalog_category_product'), $data);
}

$installer->run("
ALTER TABLE `{$installer->getTable('catalog_category_product')}`
    ADD UNIQUE `UNQ_CATEGORY_PRODUCT` (`category_id`, `product_id`);
");

$installer->endSetup();
