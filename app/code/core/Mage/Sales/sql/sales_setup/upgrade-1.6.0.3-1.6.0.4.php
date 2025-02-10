<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;

$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('sales/order_aggregated_created'),
    $installer->getTable('sales/order_aggregated_updated'),
));
