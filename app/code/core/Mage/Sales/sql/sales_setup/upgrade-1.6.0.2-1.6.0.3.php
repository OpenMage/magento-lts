<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$installer->getConnection()
    ->addColumn($installer->getTable('sales/shipment'), 'shipping_label', [
        'type'    => Varien_Db_Ddl_Table::TYPE_VARBINARY,
        'comment' => 'Shipping Label Content',
        'length'  => '2m',
    ]);
