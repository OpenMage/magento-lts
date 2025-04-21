<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
// DROP TABLE IF EXISTS `{$installer->getTable('checkout_agreement')}`;
CREATE TABLE `{$installer->getTable('checkout_agreement')}` (
   `agreement_id` int(10) unsigned NOT NULL auto_increment,
   `name` varchar(255) NOT NULL default '',
   `content` text NOT NULL,
   `checkbox_text` text NOT NULL,
   `is_active` tinyint(4) NOT NULL default '0',
    PRIMARY KEY  (`agreement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
