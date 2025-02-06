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

$installer->run("
ALTER TABLE `{$installer->getTable('sales_order_tax')}` ADD `base_real_amount` DECIMAL( 12, 4 ) NOT NULL;
");
