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
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('sales/shipment'), 'packages', [
        'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Packed Products in Packages',
        'length'  => '20000',
    ]);
$installer->endSetup();
