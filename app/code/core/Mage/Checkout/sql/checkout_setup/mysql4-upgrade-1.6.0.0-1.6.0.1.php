<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$table = $installer->getTable('checkout/agreement');
$column = 'position';

if (!$connection->tableColumnExists($table, $column)) {
    $connection->addColumn(
        $table,
        $column,
        [
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length'    => 2,
            'nullable'  => false,
            'default'   => 0,
            'comment'   => 'Agreement Position',
        ],
    );
}

$installer->endSetup();
