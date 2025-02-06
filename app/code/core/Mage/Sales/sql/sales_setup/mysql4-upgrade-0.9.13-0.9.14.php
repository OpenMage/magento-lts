<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'customer_taxvat',
    'varchar(255) NULL DEFAULT NULL AFTER `customer_is_guest`',
);
$installer->addAttribute('quote', 'customer_taxvat', ['type' => 'static', 'visible' => false]);

// add customer_taxvat
$installer->addAttribute('order', 'customer_taxvat', ['type' => 'varchar', 'visible' => false]);

$installer->endSetup();
