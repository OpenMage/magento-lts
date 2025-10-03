<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/** @var Mage_Tax_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
 CREATE TABLE `{$installer->getTable('tax_calculation_rate')}` (
`tax_calculation_rate_id` INT NOT NULL AUTO_INCREMENT ,
`tax_country_id` CHAR( 2 ) NOT NULL ,
`tax_region_id` MEDIUMINT NOT NULL ,
`tax_postcode` VARCHAR( 12 ) NOT NULL ,
`code` VARCHAR( 255 ) NOT NULL ,
`rate` DECIMAL( 12, 4 ) NOT NULL ,
PRIMARY KEY ( `tax_calculation_rate_id` ),
KEY `IDX_TAX_CALCULATION_RATE` (`tax_country_id`, `tax_region_id`, `tax_postcode`),
KEY `IDX_TAX_CALCULATION_RATE_CODE` (`code`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

 CREATE TABLE `{$installer->getTable('tax_calculation_rate_title')}` (
`tax_calculation_rate_title_id` INT NOT NULL AUTO_INCREMENT ,
`tax_calculation_rate_id` INT NOT NULL ,
`store_id` SMALLINT( 5 ) UNSIGNED NOT NULL ,
`value` VARCHAR( 255 ) NOT NULL ,
PRIMARY KEY ( `tax_calculation_rate_title_id` ),
KEY `IDX_TAX_CALCULATION_RATE_TITLE` (`tax_calculation_rate_id`, `store_id`),
KEY `FK_TAX_CALCULATION_RATE_TITLE_RATE` (`tax_calculation_rate_id`),
KEY `FK_TAX_CALCULATION_RATE_TITLE_STORE` (`store_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

 CREATE TABLE `{$installer->getTable('tax_calculation_rule')}` (
`tax_calculation_rule_id` INT NOT NULL AUTO_INCREMENT ,
`code` VARCHAR( 255 ) NOT NULL ,
`priority` MEDIUMINT NOT NULL ,
`position` MEDIUMINT NOT NULL ,
PRIMARY KEY ( `tax_calculation_rule_id` ),
KEY `IDX_TAX_CALCULATION_RULE` (`priority`, `position`, `tax_calculation_rule_id`),
KEY `IDX_TAX_CALCULATION_RULE_CODE` (`code`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

 CREATE TABLE `{$installer->getTable('tax_calculation')}` (
`tax_calculation_rate_id` INT NOT NULL,
`tax_calculation_rule_id` INT NOT NULL ,
`customer_tax_class_id`   SMALLINT( 6 ) NOT NULL ,
`product_tax_class_id`    SMALLINT( 6 ) NOT NULL ,
KEY `FK_TAX_CALCULATION_RULE` (`tax_calculation_rule_id`),
KEY `FK_TAX_CALCULATION_RATE` (`tax_calculation_rate_id`),
KEY `FK_TAX_CALCULATION_CTC` (`customer_tax_class_id`),
KEY `FK_TAX_CALCULATION_PTC` (`product_tax_class_id`),
KEY `IDX_TAX_CALCULATION` (`tax_calculation_rate_id`, `customer_tax_class_id`, `product_tax_class_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_RATE_TITLE_RATE', $installer->getTable('tax_calculation_rate_title'), 'tax_calculation_rate_id', $installer->getTable('tax_calculation_rate'), 'tax_calculation_rate_id');
$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_RATE_TITLE_STORE', $installer->getTable('tax_calculation_rate_title'), 'store_id', $installer->getTable('core_store'), 'store_id');

$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_RULE', $installer->getTable('tax_calculation'), 'tax_calculation_rule_id', $installer->getTable('tax_calculation_rule'), 'tax_calculation_rule_id');
$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_RATE', $installer->getTable('tax_calculation'), 'tax_calculation_rate_id', $installer->getTable('tax_calculation_rate'), 'tax_calculation_rate_id');
$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_CTC', $installer->getTable('tax_calculation'), 'customer_tax_class_id', $installer->getTable('tax_class'), 'class_id');
$installer->getConnection()->addConstraint('FK_TAX_CALCULATION_PTC', $installer->getTable('tax_calculation'), 'product_tax_class_id', $installer->getTable('tax_class'), 'class_id');

$installer->convertOldTaxData();

$installer->run("
DROP TABLE `{$installer->getTable('tax_rule')}`;
DROP TABLE `{$installer->getTable('tax_rate_type')}`;
DROP TABLE `{$installer->getTable('tax_rate_data')}`;
DROP TABLE `{$installer->getTable('tax_rate')}`;
");

$installer->endSetup();
