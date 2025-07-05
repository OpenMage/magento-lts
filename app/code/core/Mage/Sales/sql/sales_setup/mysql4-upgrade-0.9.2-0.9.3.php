<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;

$installer->run("
ALTER TABLE `{$installer->getTable('sales_flat_quote_address')}` CHANGE `applied_taxes` `applied_taxes` TEXT;

CREATE TABLE `{$installer->getTable('sales_order_tax')}` (
  `tax_id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `percent` decimal(12,4) NOT NULL,
  `priority` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY  (`tax_id`),
  KEY `IDX_ORDER_TAX` (`order_id`,`priority`,`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `{$installer->getTable('sales_order_tax')}` ADD `amount` DECIMAL( 12, 4 ) NOT NULL AFTER `percent`;
");
