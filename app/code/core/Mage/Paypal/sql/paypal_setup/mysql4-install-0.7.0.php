<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/** @var Mage_Paypal_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('paypal_api_debug')}`;
CREATE TABLE `{$this->getTable('paypal_api_debug')}` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY  (`debug_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();

$installer->addAttribute('quote_payment', 'paypal_payer_id', []);
$installer->addAttribute('quote_payment', 'paypal_payer_status', []);
$installer->addAttribute('quote_payment', 'paypal_correlation_id', []);
