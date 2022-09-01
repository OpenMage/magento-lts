<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */

$installer->startSetup();

$select = $installer->getConnection()->select()
    ->from($installer->getTable('catalog_category_product'), [
        'category_id',
        'product_id',
        'position',
        'cnt' => 'COUNT(product_id)'
    ])
    ->group('category_id')
    ->group('product_id')
    ->having('cnt > 1');
$rowSet = $installer->getConnection()->fetchAll($select);

foreach ($rowSet as $row) {
    $data = [
        'category_id'   => $row['category_id'],
        'product_id'    => $row['product_id'],
        'position'      => $row['position']
    ];
    $installer->getConnection()->delete($installer->getTable('catalog_category_product'), [
        $installer->getConnection()->quoteInto('category_id = ?', $row['category_id']),
        $installer->getConnection()->quoteInto('product_id = ?', $row['product_id'])
    ]);
    $installer->getConnection()->insert($installer->getTable('catalog_category_product'), $data);
}

$installer->run("
ALTER TABLE `{$installer->getTable('catalog_category_product')}`
    ADD UNIQUE `UNQ_CATEGORY_PRODUCT` (`category_id`, `product_id`);
");

$installer->endSetup();
