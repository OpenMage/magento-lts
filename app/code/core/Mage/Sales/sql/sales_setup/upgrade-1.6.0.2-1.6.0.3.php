<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('sales/shipment'), 'shipping_label', [
        'type'    => Varien_Db_Ddl_Table::TYPE_VARBINARY,
        'comment' => 'Shipping Label Content',
        'length'  => '2m',
    ]);
