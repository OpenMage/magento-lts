<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;

/**
 * Add new field to 'cataloginventory/stock_item'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('cataloginventory/stock_item'),
        'is_decimal_divided',
        [
            'TYPE' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'LENGTH' => 5,
            'UNSIGNED' => true,
            'NULLABLE' => false,
            'DEFAULT' => 0,
            'COMMENT' => 'Is Divided into Multiple Boxes for Shipping',
        ],
    );
