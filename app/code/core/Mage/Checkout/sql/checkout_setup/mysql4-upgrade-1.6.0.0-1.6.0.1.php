<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/** @var Mage_Checkout_Model_Resource_Setup $this */
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
