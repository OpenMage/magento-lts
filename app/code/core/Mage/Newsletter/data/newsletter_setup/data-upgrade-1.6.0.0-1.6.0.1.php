<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$subscriberTable = $installer->getTable('newsletter/subscriber');

$select = $installer->getConnection()->select()
    ->from(['main_table' => $subscriberTable])
    ->join(
        ['customer' => $installer->getTable('customer/entity')],
        'main_table.customer_id = customer.entity_id',
        ['website_id'],
    )
    ->where('customer.website_id = 0');

$installer->getConnection()->query(
    $installer->getConnection()->deleteFromSelect($select, 'main_table'),
);
