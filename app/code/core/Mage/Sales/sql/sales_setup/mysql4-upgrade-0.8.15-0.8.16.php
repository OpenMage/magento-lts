<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;
$this->startSetup();

$installer->getConnection()->addColumn($this->getTable('sales_quote_item'), 'custom_price', 'decimal(12,4) NULL AFTER `price`');

$this->endSetup();
$this->installEntities();
