<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Tax_Model_Resource_Setup $installer
 */
$installer = $this;

$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getTable('tax/tax_order_aggregated_updated'),
));
