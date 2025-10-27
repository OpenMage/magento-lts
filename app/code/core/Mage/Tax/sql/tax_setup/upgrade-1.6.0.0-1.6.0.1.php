<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Tax_Model_Resource_Setup $this */
$installer = $this;

$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('tax/tax_order_aggregated_created'),
    $installer->getTable('tax/tax_order_aggregated_updated'),
));
