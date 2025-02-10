<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Tax_Model_Resource_Setup $installer
 */
$installer = $this;

/**
 * Add new field to 'tax/sales_order_tax_item'
 */
$installer->getConnection()
    ->addColumn(
        $installer->getTable('tax/sales_order_tax_item'),
        'tax_percent',
        [
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'NULLABLE'  => false,
            'COMMENT'   => 'Real Tax Percent For Item',
        ],
    );
