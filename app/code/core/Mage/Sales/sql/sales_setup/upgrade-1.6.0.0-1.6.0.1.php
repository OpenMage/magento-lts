<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('sales/order_status_history'), 'entity_name', [
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 32,
        'nullable'  => true,
        'comment'   => 'Shows what entity history is bind to.',
    ]);
