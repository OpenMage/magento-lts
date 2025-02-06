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

$installer->getConnection()->addColumn($installer->getTable('sales_quote_address'), 'save_in_address_book', 'tinyint(1) default 0 after `customer_id`');
$installer->addAttribute('quote_address', 'save_in_address_book', ['type' => 'static']);

$installer->endSetup();
