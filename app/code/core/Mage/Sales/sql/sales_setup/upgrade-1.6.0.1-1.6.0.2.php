<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/shipment'), 'packages', [
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Packed Products in Packages',
        'length'  => '20000',
    ]);
$installer->endSetup();
