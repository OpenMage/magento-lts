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

$installer->getConnection()->addColumn($installer->getTable('sales_quote'), 'reserved_order_id', 'varchar(64) default \'\'');
$installer->addAttribute('quote', 'reserved_order_id', ['type' => 'static']);
