<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$subscriberTable = $installer->getTable('newsletter/subscriber');

$select = $connection->select()
    ->from(['main_table' => $subscriberTable])
    ->join(
        ['customer' => $installer->getTable('customer/entity')],
        'main_table.customer_id = customer.entity_id',
        ['website_id'],
    )
    ->where('customer.website_id = 0');

$connection->query(
    $connection->deleteFromSelect($select, 'main_table'),
);
